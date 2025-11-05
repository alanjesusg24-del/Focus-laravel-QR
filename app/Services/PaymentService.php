<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
use Stripe\Subscription;
use Stripe\Customer;
use Stripe\Webhook;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    protected ?string $stripeKey;
    protected ?string $stripeSecret;

    public function __construct()
    {
        $this->stripeKey = config('services.stripe.key', env('STRIPE_KEY'));
        $this->stripeSecret = config('services.stripe.secret', env('STRIPE_SECRET'));

        // Initialize Stripe API
        if ($this->stripeSecret) {
            Stripe::setApiKey($this->stripeSecret);
        }
    }

    /**
     * Create Stripe checkout session for payment
     *
     * @param Business $business
     * @param Plan $plan
     * @return string|null
     */
    public function createCheckoutSession(Business $business, Plan $plan): ?string
    {
        try {
            if (!$this->stripeSecret) {
                Log::warning('Stripe is not configured. Using test mode.');
                return null;
            }

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'mxn',
                        'product_data' => [
                            'name' => $plan->name,
                            'description' => $plan->description,
                        ],
                        'unit_amount' => (int)($plan->price * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('order-qr.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('order-qr.payment.cancel'),
                'client_reference_id' => (string)$business->business_id,
                'metadata' => [
                    'business_id' => $business->business_id,
                    'plan_id' => $plan->plan_id,
                ],
            ]);

            return $session->url;
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new payment for a business
     *
     * @param int $businessId
     * @param int $planId
     * @param string $paymentMethodId
     * @return Payment
     */
    public function createPayment(int $businessId, int $planId, string $paymentMethodId): Payment
    {
        return DB::transaction(function () use ($businessId, $planId, $paymentMethodId) {
            $business = Business::findOrFail($businessId);
            $plan = Plan::findOrFail($planId);

            // Create Stripe payment intent
            $stripePaymentId = $this->createStripePaymentIntent($plan->price, $paymentMethodId);

            // Create payment record
            $payment = Payment::create([
                'business_id' => $businessId,
                'plan_id' => $planId,
                'amount' => $plan->price,
                'stripe_payment_id' => $stripePaymentId,
                'status' => 'pending',
                'payment_date' => now(),
                'next_payment_date' => now()->addDays($plan->duration_days),
            ]);

            // If payment is successful, update payment status
            if ($stripePaymentId && !str_starts_with($stripePaymentId, 'pi_failed_')) {
                $payment->status = 'completed';
                $payment->save();

                // Update business
                $this->updateBusinessAfterPayment($business, $plan);
            }

            return $payment;
        });
    }

    /**
     * Create Stripe subscription for recurring payments
     *
     * @param int $businessId
     * @param int $planId
     * @param string $paymentMethodId
     * @return array
     */
    public function createSubscription(int $businessId, int $planId, string $paymentMethodId): array
    {
        $business = Business::findOrFail($businessId);
        $plan = Plan::findOrFail($planId);

        // Create Stripe subscription
        $subscriptionId = $this->createStripeSubscription($business, $plan, $paymentMethodId);

        // Create initial payment
        $payment = Payment::create([
            'business_id' => $businessId,
            'plan_id' => $planId,
            'amount' => $plan->price,
            'stripe_subscription_id' => $subscriptionId,
            'status' => 'completed',
            'payment_date' => now(),
            'next_payment_date' => now()->addDays($plan->duration_days),
        ]);

        // Update business
        $this->updateBusinessAfterPayment($business, $plan);

        return [
            'payment' => $payment,
            'subscription_id' => $subscriptionId,
        ];
    }

    /**
     * Cancel subscription
     *
     * @param int $businessId
     * @return bool
     */
    public function cancelSubscription(int $businessId): bool
    {
        $business = Business::findOrFail($businessId);

        $lastPayment = Payment::where('business_id', $businessId)
            ->whereNotNull('stripe_subscription_id')
            ->latest('payment_id')
            ->first();

        if (!$lastPayment) {
            return false;
        }

        // Cancel Stripe subscription
        $this->cancelStripeSubscription($lastPayment->stripe_subscription_id);

        // Deactivate business if needed
        $business->is_active = false;
        $business->save();

        return true;
    }

    /**
     * Process webhook from Stripe
     *
     * @param array $payload
     * @return bool
     */
    public function processStripeWebhook(array $payload): bool
    {
        $eventType = $payload['type'] ?? null;

        switch ($eventType) {
            case 'checkout.session.completed':
                return $this->handleCheckoutSessionCompleted($payload['data']['object']);

            case 'payment_intent.succeeded':
                return $this->handlePaymentSuccess($payload['data']['object']);

            case 'payment_intent.payment_failed':
                return $this->handlePaymentFailure($payload['data']['object']);

            case 'customer.subscription.deleted':
                return $this->handleSubscriptionCancelled($payload['data']['object']);

            default:
                Log::info("Unhandled webhook event: {$eventType}");
                return true;
        }
    }

    /**
     * Get payment history for a business
     *
     * @param int $businessId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPaymentHistory(int $businessId, int $limit = 10)
    {
        return Payment::where('business_id', $businessId)
            ->with('plan')
            ->orderBy('payment_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if business payment is expired
     *
     * @param Business $business
     * @return bool
     */
    public function isPaymentExpired(Business $business): bool
    {
        if (!$business->last_payment_date) {
            return true;
        }

        $expirationDate = $business->last_payment_date
            ->addDays($business->plan->duration_days);

        return now()->gt($expirationDate);
    }

    /**
     * Get payment statistics
     *
     * @param int|null $businessId
     * @return array
     */
    public function getPaymentStatistics(?int $businessId = null): array
    {
        $query = Payment::query();

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $payments = $query->get();

        return [
            'total_payments' => $payments->count(),
            'total_revenue' => $payments->sum('amount'),
            'completed' => $payments->where('status', 'completed')->count(),
            'pending' => $payments->where('status', 'pending')->count(),
            'failed' => $payments->where('status', 'failed')->count(),
            'refunded' => $payments->where('status', 'refunded')->count(),
        ];
    }

    // ===== Stripe API Methods =====

    /**
     * Create Stripe payment intent
     *
     * @param float $amount
     * @param string $paymentMethodId
     * @return string
     */
    protected function createStripePaymentIntent(float $amount, string $paymentMethodId): string
    {
        try {
            if (!$this->stripeSecret) {
                return 'pi_test_' . bin2hex(random_bytes(12));
            }

            $intent = PaymentIntent::create([
                'amount' => (int)($amount * 100),
                'currency' => 'mxn',
                'payment_method' => $paymentMethodId,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            return $intent->id;
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent creation failed: ' . $e->getMessage());
            return 'pi_failed_' . bin2hex(random_bytes(12));
        }
    }

    /**
     * Create Stripe subscription
     *
     * @param Business $business
     * @param Plan $plan
     * @param string $paymentMethodId
     * @return string
     */
    protected function createStripeSubscription(Business $business, Plan $plan, string $paymentMethodId): string
    {
        try {
            if (!$this->stripeSecret) {
                return 'sub_test_' . bin2hex(random_bytes(12));
            }

            $subscription = Subscription::create([
                'customer' => $this->getOrCreateStripeCustomer($business),
                'items' => [[
                    'price_data' => [
                        'currency' => 'mxn',
                        'product_data' => [
                            'name' => $plan->name,
                        ],
                        'unit_amount' => (int)($plan->price * 100),
                        'recurring' => [
                            'interval' => 'month',
                            'interval_count' => (int)($plan->duration_days / 30),
                        ],
                    ],
                ]],
                'default_payment_method' => $paymentMethodId,
            ]);

            return $subscription->id;
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription creation failed: ' . $e->getMessage());
            return 'sub_failed_' . bin2hex(random_bytes(12));
        }
    }

    /**
     * Cancel Stripe subscription
     *
     * @param string $subscriptionId
     * @return bool
     */
    protected function cancelStripeSubscription(string $subscriptionId): bool
    {
        try {
            if (!$this->stripeSecret || str_starts_with($subscriptionId, 'sub_test_')) {
                Log::info("Cancelled test subscription: {$subscriptionId}");
                return true;
            }

            Subscription::update($subscriptionId, [
                'cancel_at_period_end' => true,
            ]);

            Log::info("Cancelled subscription: {$subscriptionId}");
            return true;
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription cancellation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get or create Stripe customer for business
     *
     * @param Business $business
     * @return string
     */
    protected function getOrCreateStripeCustomer(Business $business): string
    {
        try {
            $customer = Customer::create([
                'email' => $business->email,
                'name' => $business->business_name,
                'metadata' => [
                    'business_id' => $business->business_id,
                ],
            ]);

            return $customer->id;
        } catch (ApiErrorException $e) {
            Log::error('Stripe customer creation failed: ' . $e->getMessage());
            return 'cus_test_' . bin2hex(random_bytes(12));
        }
    }

    /**
     * Verify Stripe webhook signature
     *
     * @param string $payload
     * @param string $signature
     * @return object|null
     */
    public function verifyWebhookSignature(string $payload, string $signature): ?object
    {
        try {
            $webhookSecret = config('services.stripe.webhook_secret');

            if (!$webhookSecret) {
                return json_decode($payload);
            }

            return Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle checkout session completed
     *
     * @param array $session
     * @return bool
     */
    protected function handleCheckoutSessionCompleted(array $session): bool
    {
        $businessId = $session['metadata']['business_id'] ?? null;
        $planId = $session['metadata']['plan_id'] ?? null;

        if (!$businessId || !$planId) {
            Log::error('Missing metadata in checkout session');
            return false;
        }

        $business = Business::find($businessId);
        $plan = Plan::find($planId);

        if (!$business || !$plan) {
            Log::error('Business or Plan not found');
            return false;
        }

        // Create payment record
        Payment::create([
            'business_id' => $businessId,
            'plan_id' => $planId,
            'amount' => $plan->price,
            'stripe_payment_id' => $session['payment_intent'] ?? null,
            'status' => 'completed',
            'payment_date' => now(),
            'next_payment_date' => now()->addDays($plan->duration_days),
        ]);

        // Update business
        $this->updateBusinessAfterPayment($business, $plan);

        return true;
    }

    /**
     * Handle successful payment
     *
     * @param array $paymentIntent
     * @return bool
     */
    protected function handlePaymentSuccess(array $paymentIntent): bool
    {
        $payment = Payment::where('stripe_payment_id', $paymentIntent['id'])->first();

        if ($payment) {
            $payment->status = 'completed';
            $payment->save();

            $business = $payment->business;
            $this->updateBusinessAfterPayment($business, $payment->plan);
        }

        return true;
    }

    /**
     * Handle failed payment
     *
     * @param array $paymentIntent
     * @return bool
     */
    protected function handlePaymentFailure(array $paymentIntent): bool
    {
        $payment = Payment::where('stripe_payment_id', $paymentIntent['id'])->first();

        if ($payment) {
            $payment->status = 'failed';
            $payment->save();
        }

        return true;
    }

    /**
     * Handle cancelled subscription
     *
     * @param array $subscription
     * @return bool
     */
    protected function handleSubscriptionCancelled(array $subscription): bool
    {
        $payment = Payment::where('stripe_subscription_id', $subscription['id'])->first();

        if ($payment) {
            $business = $payment->business;
            $business->is_active = false;
            $business->save();
        }

        return true;
    }

    /**
     * Update business after successful payment
     *
     * @param Business $business
     * @param Plan $plan
     * @return void
     */
    protected function updateBusinessAfterPayment(Business $business, Plan $plan): void
    {
        $business->plan_id = $plan->plan_id;
        $business->last_payment_date = now();
        $business->is_active = true;
        $business->save();
    }
}
