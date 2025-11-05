<?php

namespace App\Http\Middleware;

use App\Services\PaymentService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBusinessPayment
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $business = Auth::guard('business')->user();

        if (!$business) {
            return redirect()->route('login')
                ->with('error', 'Please login to continue');
        }

        // Allow access to payment and profile routes
        $allowedRoutes = [
            'order-qr.payment.index',
            'order-qr.payment.checkout',
            'order-qr.payment.create-checkout-session',
            'order-qr.payment.success',
            'order-qr.payment.cancel',
            'order-qr.payment.history',
            'order-qr.business.profile',
            'order-qr.business.edit',
            'order-qr.business.update',
            'logout',
        ];

        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // Check if payment is expired
        if ($this->paymentService->isPaymentExpired($business)) {
            return redirect()
                ->route('order-qr.payment.index')
                ->with('warning', 'Your payment has expired. Please renew your plan to continue using the system.');
        }

        // Check if business is active
        if (!$business->is_active) {
            return redirect()
                ->route('order-qr.payment.index')
                ->with('error', 'Your account is inactive. Please contact support or renew your subscription.');
        }

        return $next($request);
    }
}
