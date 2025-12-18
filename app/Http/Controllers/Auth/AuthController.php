<?php

/**
 * Company: CETAM
 * Project: FF
 * File: AuthController.php
 * Created on: 20/11/2025
 * Created by: Dafne Vanessa Castillo Moreo
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 15/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreo |
 *   Description: Refactored to comply  |
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'password.required' => 'El campo contraseña es obligatorio.',
        ]);

        $remember = $request->filled('remember');

        // 5.4.1: Early Return - Superadmin authentication
        if (Auth::guard('superadmin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('superadmin.dashboard'));
        }

        // 5.4.1: Early Return - Business authentication
        if (Auth::guard('business')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('business.orders.index'));
        }

        throw ValidationException::withMessages([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('business')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('business.login');
    }
}
