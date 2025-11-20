<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class OptionalSanctumAuth
{
    /**
     * Handle an incoming request.
     *
     * Permite autenticación opcional con Sanctum - no falla si no hay token
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Intentar autenticar pero no fallar si no hay token
        if ($token = $request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                // Establecer el usuario autenticado en la request
                $request->setUserResolver(function () use ($accessToken) {
                    return $accessToken->tokenable;
                });
            }
        }

        return $next($request);
    }
}
