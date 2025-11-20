<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->filled('remember');

        // First, try to authenticate as superadmin
        if (Auth::guard('superadmin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('superadmin.dashboard'))
                ->with('success', '¡Bienvenido de nuevo, ' . Auth::guard('superadmin')->user()->full_name . '!');
        }

        // If not superadmin, try to authenticate as business
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
    public function logout(Request $request)
    {
        Auth::guard('business')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('business.login');
    }
}
