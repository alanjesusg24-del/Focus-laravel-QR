<?php

/**
 * Company: CETAM
 * Project: FF
 * File: PaymentController.php
 * Created on: 04/10/2025
 * Created by: Alan Jesus Garcia Nava
 * Approved by: Alan Jesus Garcia Nava
 *
 * Changelog:
 * - ID: 1 | Modified on: 04/12/2025 |
 *   Modified by: Alan Jesus Garcia Nava |
 *   Description: Controller to handle payment operations |
 */

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Services\PaymentService;
use App\Services\MercadoPagoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected MercadoPagoService $mercadoPagoService;

    public function __construct(PaymentService $paymentService, MercadoPagoService $mercadoPagoService)
    {
        $this->paymentService = $paymentService;
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Display payment plans selection
     */
    public function index(Request $request): View
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
    public function create(Plan $plan): View|RedirectResponse
    {
        // 5.4.1: Early Return - Guard Clause
        if (!$plan->is_active) {
            return redirect()
                ->route('business.payments.index')
                ->with('error', 'Selected plan is not available');
        }

        $business = Auth::guard('business')->user();

        return view('payments.checkout', compact('plan', 'business'));
    }

    /**
     * Process simulated payment (for educational purposes)
     */
    public function processSimulation(Request $request, Plan $plan): RedirectResponse
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'card_name' => 'required|string|max:255',
            'card_number' => 'required|string|min:16|max:19',
            'expiry_month' => 'required|string|size:2',
            'expiry_year' => 'required|string|size:2',
            'cvc' => 'required|string|min:3|max:4',
        ]);

        $business = Auth::guard('business')->user();

        try {
            // Actualizar el plan del negocio y activar la cuenta
            $business->plan_id = $plan->plan_id;
            $business->last_payment_date = now();
            $business->is_active = true; // Activar cuenta después del pago
            $business->save();

            // Crear registro del pago simulado
            Payment::create([
                'business_id' => $business->business_id,
                'plan_id' => $plan->plan_id,
                'amount' => $plan->price,
                'payment_provider' => 'simulated',
                'status' => 'completed',
            ]);

            Log::info('Simulated payment processed successfully', [
                'business_id' => $business->business_id,
                'plan_id' => $plan->plan_id,
                'amount' => $plan->price,
            ]);

            // Redireccionar al dashboard con mensaje de éxito
            return redirect()
                ->route('business.dashboard.index')
                ->with('success', '¡Pago procesado exitosamente! Tu plan ha sido actualizado.');

        } catch (\Exception $e) {
            Log::error('Simulated payment failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Error al procesar el pago simulado: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment callback
     */
    public function success(Request $request): View
    {
        $sessionId = $request->query('session_id');

        return view('payments.success', compact('sessionId'));
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(): View
    {
        return view('payments.cancel');
    }

    /**
     * Display payment history
     */
    public function history(): View
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
    public function store(Request $request): RedirectResponse
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
    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show subscription creation form
     */
    public function subscription(): View
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
    public function createSubscription(Request $request): RedirectResponse
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
    public function cancelSubscription(Request $request): RedirectResponse
    {
        try {
            $businessId = Auth::id();
            $cancelled = $this->paymentService->cancelSubscription($businessId);

            // 5.4.1: Early Return - Guard Clause
            if (!$cancelled) {
                return back()->with('error', 'No se encontró una suscripción activa');
            }

            return redirect()
                ->route('business.payments.subscription')
                ->with('success', 'Suscripción cancelada exitosamente');

        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed: ' . $e->getMessage());
            return back()->with('error', 'Error al cancelar la suscripción: ' . $e->getMessage());
        }
    }

    /**
     * Handle Stripe webhook events
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        // 5.4.1: Early Return - Guard Clause
        if (!$signature) {
            Log::error('Stripe webhook: Missing signature');
            return response()->json(['error' => 'Missing signature'], 400);
        }

        $event = $this->paymentService->verifyWebhookSignature($payload, $signature);

        // 5.4.1: Early Return - Guard Clause
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
    public function statistics(Request $request): JsonResponse
    {
        $businessId = Auth::id();
        $stats = $this->paymentService->getPaymentStatistics($businessId);

        return response()->json($stats);
    }
}
