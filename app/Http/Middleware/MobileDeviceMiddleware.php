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
        $deviceId = $request->header('X-Device-ID');

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
