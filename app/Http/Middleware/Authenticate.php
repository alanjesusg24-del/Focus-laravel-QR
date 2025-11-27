<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Detectar el guard según la URL actual
            $path = $request->path();

            // Si la ruta comienza con 'superadmin', redirigir a login de superadmin
            if (str_starts_with($path, 'superadmin')) {
                return route('superadmin.login');
            }

            // Si la ruta comienza con 'business', redirigir a login de business
            if (str_starts_with($path, 'business')) {
                return route('business.login');
            }

            // Por defecto, redirigir al login del proyecto Volt
            return route(config('proj.route_name_prefix', 'proj') . '.auth.login');
        }
    }
}
