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

        /*
        |--------------------------------------------------------------------------
        | DESACTIVACIÓN TEMPORAL PARA DESARROLLO/PRUEBAS
        |--------------------------------------------------------------------------
        | Este bloque se ha comentado para permitir el acceso a las rutas
        | aunque la suscripción no esté activa.
        |
        | ¡RECUERDA DESCOMENTARLO (O RESTAURARLO) EN PRODUCCIÓN!
        |
        */
        /*
        // Verificar si la suscripción está activa
        if (!$this->mercadoPagoService->isSubscriptionActive($business)) {
            // Redirigir a página de pagos con mensaje
            return redirect()
                ->route('business.payments.index')
                ->with('error', 'Tu suscripción ha expirado. Por favor renueva para continuar usando el sistema.');
        }
        */

        // Suscripción activa (o verificación desactivada), permitir acceso
        return $next($request);
    }
}