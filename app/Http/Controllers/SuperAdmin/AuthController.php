<?php

/**
 * Company: CETAM
 * Project: FF
 * File: AuthController.php (SuperAdmin)
 * Created on: 20/11/2025
 * Created by: Alan Jesus Garcia Nava
 * Approved by: Alan Jesus Garcia Nava
 *
 * Changelog:
 * - ID: 1 | Modified on: 15/12/2025 |
 *   Modified by: Alan Jesus Garcia Nava |
 *   Description: Refactored to comply  |
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    /**
     * Show the login form for super admin
     */
    public function showLoginForm(): RedirectResponse
    {
        // 5.4.1: Early Return - Already authenticated
        if (Auth::guard('superadmin')->check()) {
            return redirect()->route('superadmin.dashboard');
        }

        // Redirect to unified login
        return redirect()->route('business.login');
    }

    /**
     * Handle super admin login
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // 5.4.1: Early Return - Authentication success
        if (Auth::guard('superadmin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('superadmin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Handle super admin logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('superadmin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('business.login');
    }
}
