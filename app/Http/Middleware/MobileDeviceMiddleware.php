<?php

namespace App\Http\Middleware;

use App\Models\MobileUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileDeviceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado con Sanctum, el device_id es opcional
        $user = $request->user('sanctum');
        $deviceId = $request->header('X-Device-ID');

        // Si hay usuario autenticado, continuar sin requerir device_id
        if ($user) {
            // Intentar obtener mobile_user si hay device_id (opcional)
            if ($deviceId) {
                $mobileUser = MobileUser::where('device_id', $deviceId)->first();
                if ($mobileUser) {
                    $mobileUser->update(['last_seen_at' => now()]);
                    $request->merge(['mobile_user' => $mobileUser]);
                }
            }
            return $next($request);
        }

        // Sin autenticación: requerir device_id
        if (!$deviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Device ID is required',
            ], 401);
        }

        $mobileUser = MobileUser::where('device_id', $deviceId)->first();

        if (!$mobileUser) {
            return response()->json([
                'success' => false,
                'message' => 'Device not registered',
            ], 404);
        }

        // Actualizar last_seen_at
        $mobileUser->update(['last_seen_at' => now()]);

        // Agregar el usuario al request
        $request->merge(['mobile_user' => $mobileUser]);

        return $next($request);
    }
}
