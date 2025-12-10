<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $business = Auth::guard('business')->user();

        if (!$business) {
            return redirect()->route('business.login');
        }

        // Verificar si la cuenta del negocio está activa
        if (!$business->is_active) {
            // Redirigir a página de pagos con mensaje
            return redirect()
                ->route('business.payments.index')
                ->with('warning', 'Para usar el sistema, primero debes activar tu cuenta realizando el pago.');
        }

        // Verificar si el plan ha vencido (30 días desde el último pago)
        if ($business->last_payment_date) {
            $expirationDate = \Carbon\Carbon::parse($business->last_payment_date)->addDays(30);

            if (now()->greaterThan($expirationDate)) {
                // Plan vencido, desactivar cuenta
                $business->is_active = false;
                $business->save();

                return redirect()
                    ->route('business.payments.index')
                    ->with('error', 'Tu plan ha vencido. Por favor renueva para continuar usando el sistema.');
            }
        }

        // Cuenta activa y plan vigente, permitir acceso
        return $next($request);
    }
}