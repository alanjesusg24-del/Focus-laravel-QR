<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display payment plans selection
     */
    public function index(Request $request)
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        $business = Auth::guard('business')->user();
        $payments = $this->paymentService->getPaymentHistory($business->business_id, 5);

        return view('payments.index', compact('plans', 'business', 'payments'));
    }

    /**
     * Show payment checkout page for selected plan
     */
    public function create(Plan $plan)
    {
        if (!$plan->is_active) {
            return redirect()
                ->route('business.payments.index')
                ->with('error', 'Selected plan is not available');
        }

        $business = Auth::guard('business')->user();

        return view('payments.checkout', compact('plan', 'business'));
    }

    /**
     * Create Stripe checkout session and redirect
     * TEMPORARY: Simulating successful payment without Stripe
     */
    public function createCheckoutSession(Request $request, Plan $plan)
    {
        $business = Auth::guard('business')->user();

        try {
            // Simulate payment creation
            $payment = Payment::create([
                'business_id' => $business->business_id,
                'plan_id' => $plan->plan_id,
                'amount' => $business->monthly_price ?? $plan->price,
                'payment_method' => 'simulated',
                'payment_status' => 'completed',
                'transaction_id' => 'SIM-' . strtoupper(uniqid()),
                'payment_date' => now(),
            ]);

            // Update business last payment date
            $business->update([
                'last_payment_date' => now(),
            ]);

            Log::info('Simulated payment created', [
                'payment_id' => $payment->payment_id,
                'business_id' => $business->business_id,
                'amount' => $payment->amount,
            ]);

            return redirect()
                ->route('order-qr.payment.success')
                ->with('success', 'Pago procesado exitosamente (modo simulación)');
        } catch (\Exception $e) {
            Log::error('Simulated payment failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment callback
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id') ?? null;

        return view('payments.success', compact('sessionId'));
    }

    /**
     * Handle cancelled payment
     */
    public function cancel()
    {
        return view('payments.cancel');
    }

    /**
     * Display payment history
     */
    public function history()
    {
        $business = Auth::guard('business')->user();

        $payments = $this->paymentService->getPaymentHistory(
            $business->business_id,
            20
        );

        $statistics = $this->paymentService->getPaymentStatistics(
            $business->business_id
        );

        return view('payments.history', compact('payments', 'statistics'));
    }

    /**
     * Process a one-time payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,plan_id',
            'payment_method_id' => 'required|string',
        ]);

        try {
            $businessId = Auth::id();
            $payment = $this->paymentService->createPayment(
                $businessId,
                $validated['plan_id'],
                $validated['payment_method_id']
            );

            return redirect()
                ->route('business.payments.show', $payment->payment_id)
                ->with('success', 'Pago procesado exitosamente');
        } catch (\Exception $e) {
            Log::error('Payment creation failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show subscription creation form
     */
    public function subscription()
    {
        $businessId = Auth::id();
        $plans = Plan::where('is_active', true)->get();
        $currentPayment = Payment::where('business_id', $businessId)
            ->whereNotNull('stripe_subscription_id')
            ->latest('payment_id')
            ->first();

        return view('payments.subscription', compact('plans', 'currentPayment'));
    }

    /**
     * Create a recurring subscription
     */
    public function createSubscription(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,plan_id',
            'payment_method_id' => 'required|string',
        ]);

        try {
            $businessId = Auth::id();
            $result = $this->paymentService->createSubscription(
                $businessId,
                $validated['plan_id'],
                $validated['payment_method_id']
            );

            return redirect()
                ->route('business.payments.subscription')
                ->with('success', 'Suscripción creada exitosamente');
        } catch (\Exception $e) {
            Log::error('Subscription creation failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Error al crear la suscripción: ' . $e->getMessage());
        }
    }

    /**
     * Cancel active subscription
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $businessId = Auth::id();
            $cancelled = $this->paymentService->cancelSubscription($businessId);

            if ($cancelled) {
                return redirect()
                    ->route('business.payments.subscription')
                    ->with('success', 'Suscripción cancelada exitosamente');
            }

            return back()->with('error', 'No se encontró una suscripción activa');
        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed: ' . $e->getMessage());
            return back()->with('error', 'Error al cancelar la suscripción: ' . $e->getMessage());
        }
    }

    /**
     * Handle Stripe webhook events
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$signature) {
            Log::error('Stripe webhook: Missing signature');
            return response()->json(['error' => 'Missing signature'], 400);
        }

        $event = $this->paymentService->verifyWebhookSignature($payload, $signature);

        if (!$event) {
            Log::error('Stripe webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', ['type' => $event->type ?? 'unknown']);

        try {
            $this->paymentService->processStripeWebhook((array)$event);

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function statistics(Request $request)
    {
        $businessId = Auth::id();
        $stats = $this->paymentService->getPaymentStatistics($businessId);

        return response()->json($stats);
    }
}
