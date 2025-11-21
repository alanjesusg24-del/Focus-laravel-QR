<?php

/**
 * ============================================
 * CETAM - Business Controller
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        BusinessController.php
 * @description Controlador de gestión de negocios y perfiles
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BusinessController extends Controller
{
    /**
     * Show business registration form
     */
    public function register()
    {
        $plans = Plan::where('is_active', true)->get();

        return view('business.register', compact('plans'));
    }

    /**
     * Process business registration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'email' => 'required|email|unique:businesses,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:15',
            'rfc' => 'required|string|min:12|max:13|unique:businesses,rfc',
            'plan_id' => 'required|exists:plans,plan_id',
            'terms' => 'required|accepted',
        ]);

        try {
            // Get the selected plan - business will inherit all settings from the plan
            $plan = \App\Models\Plan::findOrFail($validated['plan_id']);

            // Create business with only the fields that exist in the table
            // All plan features (chat, retention, etc) are inherited from the plan relationship
            $business = Business::create([
                'business_name' => $validated['business_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'rfc' => strtoupper($validated['rfc']),
                'plan_id' => $validated['plan_id'],
                'registration_date' => now(),
                'theme' => 'professional',
                'is_active' => true,
            ]);

            return redirect()
                ->route('business.login')
                ->with('success', 'Registro exitoso. Ya puedes iniciar sesión con tu cuenta.');
        } catch (\Exception $e) {
            Log::error('Business registration failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Error al registrar el negocio: ' . $e->getMessage());
        }
    }

    /**
     * Show business profile
     */
    public function profile()
    {
        $businessId = Auth::id();
        $business = Business::with('plan')->findOrFail($businessId);

        return view('business.profile', compact('business'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $businessId = Auth::id();
        $business = Business::findOrFail($businessId);

        return view('business.edit', compact('business'));
    }

    /**
     * Update business profile
     */
    public function update(Request $request)
    {
        $businessId = Auth::id();
        $business = Business::findOrFail($businessId);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('businesses', 'email')->ignore($businessId, 'business_id'),
            ],
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_description' => 'nullable|string|max:500',
            'logo_url' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'photo' => 'nullable|image|max:5120|mimes:jpg,jpeg,png',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo_url')) {
            // Delete old logo
            if ($business->logo_url) {
                $oldPath = str_replace('/storage/', '', $business->logo_url);
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('logo_url');
            $fileName = 'logo_' . $businessId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('business_logos', $fileName, 'public');
            $validated['logo_url'] = Storage::url($path);
        }

        // Handle business photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($business->photo) {
                $oldPath = str_replace('/storage/', '', $business->photo);
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('photo');
            $fileName = 'business_photo_' . $businessId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('business_photos', $fileName, 'public');
            $validated['photo'] = Storage::url($path);
        }

        $business->update($validated);

        return redirect()
            ->route('business.profile.index')
            ->with('success', 'Perfil actualizado exitosamente');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('business.change-password');
    }

    /**
     * Update business password
     */
    public function updatePassword(Request $request)
    {
        $businessId = Auth::id();
        $business = Business::findOrFail($businessId);

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $business->password)) {
            return back()->with('error', 'La contraseña actual es incorrecta');
        }

        $business->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('business.profile.index')
            ->with('success', 'Contraseña actualizada exitosamente');
    }

    /**
     * Show theme customization
     */
    public function showTheme()
    {
        $businessId = Auth::id();
        $business = Business::findOrFail($businessId);

        $availableThemes = [
            'professional' => 'Profesional (Azul institucional)',
            'modern' => 'Moderno (Naranja vibrante)',
            'classic' => 'Clásico (Gris elegante)',
        ];

        return view('business.theme', compact('business', 'availableThemes'));
    }

    /**
     * Update business theme
     */
    public function updateTheme(Request $request)
    {
        $businessId = Auth::id();
        $business = Business::findOrFail($businessId);

        $validated = $request->validate([
            'theme' => 'required|in:professional,modern,classic',
        ]);

        $business->update(['theme' => $validated['theme']]);

        return redirect()
            ->route('business.profile.index')
            ->with('success', 'Tema actualizado exitosamente');
    }

    /**
     * Deactivate business account
     */
    public function deactivate(Request $request)
    {
        $businessId = Auth::id();
        $business = Business::findOrFail($businessId);

        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        // Verify password
        if (!Hash::check($validated['password'], $business->password)) {
            return back()->with('error', 'Contraseña incorrecta');
        }

        $business->update(['is_active' => false]);

        Auth::logout();

        return redirect()
            ->route('business.login')
            ->with('success', 'Cuenta desactivada exitosamente');
    }
}
