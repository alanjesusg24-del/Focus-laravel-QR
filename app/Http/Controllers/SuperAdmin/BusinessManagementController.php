<?php

/**
 * Company: CETAM
 * Project: FF
 * File: BusinessManagementController.php
 * Created on: 23/11/2025
 * Created by: Dafne Vanessa Castillo Moreno
 * Approved by: Dafne Vanessa Castillo Moreno
 *
 * Changelog:
 * - ID: 1 | Modified on: 04/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreno |
 *   Description: SuperAdmin controller for business management |
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BusinessManagementController extends Controller
{
    /**
     * Display a listing of businesses
     */
    public function index(Request $request): View
    {
        $query = Business::with('plan');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('rfc', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // 5.1.2: Filter by status using match instead of if-elseif
        if ($request->filled('status')) {
            $query->where('is_active', match ($request->status) {
                'active' => true,
                'inactive' => false,
                default => null
            });
        }

        // Filter by plan
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'business_id');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $businesses = $query->paginate(15)->withQueryString();
        $plans = Plan::all();

        return view('superadmin.businesses.index', compact('businesses', 'plans'));
    }

    /**
     * Show the form for editing the specified business
     */
    public function edit(int $id): View
    {
        $business = Business::findOrFail($id);
        $plans = Plan::all();

        return view('superadmin.businesses.edit', compact('business', 'plans'));
    }

    /**
     * Update the specified business
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $business = Business::findOrFail($id);

        $request->validate([
            'business_name' => 'required|string|max:255',
            'rfc' => 'required|string|max:13',
            'email' => [
                'required',
                'email',
                Rule::unique('businesses', 'email')->ignore($business->business_id, 'business_id')
            ],
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'plan_id' => 'required|exists:plans,plan_id',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|max:12',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'business_name.required' => 'El nombre del negocio es obligatorio.',
            'rfc.required' => 'El RFC es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'phone.required' => 'El teléfono es obligatorio.',
            'plan_id.required' => 'El plan es obligatorio.',
            'plan_id.exists' => 'El plan seleccionado no es válido.',
            'latitude.between' => 'La latitud debe estar entre -90 y 90.',
            'longitude.between' => 'La longitud debe estar entre -180 y 180.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no debe superar los 12 caracteres.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser formato JPEG, PNG o JPG.',
            'photo.max' => 'La imagen no debe superar los 2MB.',
        ]);

        $data = $request->except(['password', 'photo', '_token', '_method']);

        // Handle checkbox (is_active)
        $data['is_active'] = $request->has('is_active');

        // Handle plan change and update derived fields
        if ($request->filled('plan_id') && $request->plan_id != $business->plan_id) {
            $this->updatePlanDerivedFields($data, $request->plan_id);
        }

        // Handle password update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->handlePhotoUpload($request, $business);
        }

        $business->update($data);

        return redirect()->route('superadmin.businesses.index')
            ->with('success', 'Negocio actualizado correctamente.');
    }

    /**
     * Update plan-derived fields
     */
    private function updatePlanDerivedFields(array &$data, int $planId): void
    {
        $selectedPlan = Plan::findOrFail($planId);
        $data['monthly_price'] = $selectedPlan->price;
        $data['has_chat_module'] = $selectedPlan->has_chat_module;
        $data['data_retention_months'] = $selectedPlan->retention_days
            ? ceil($selectedPlan->retention_days / 30)
            : 1;
    }

    /**
     * Handle photo upload and delete old photo
     */
    private function handlePhotoUpload(Request $request, Business $business): string
    {
        // Delete old photo if exists
        if ($business->photo && Storage::disk('public')->exists($business->photo)) {
            Storage::disk('public')->delete($business->photo);
        }

        return $request->file('photo')->store('businesses', 'public');
    }

    /**
     * Toggle business active status
     * Solo permite desactivar - La reactivación se hace automáticamente al pagar/renovar plan
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $business = Business::findOrFail($id);

        // Solo permitir desactivar, no reactivar manualmente
        if ($business->is_active) {
            $business->is_active = false;
            $business->save();

            return redirect()->back()
                ->with('success', 'Cuenta inactivada correctamente. Solo se reactivará cuando el negocio pague o renueve su plan.');
        }

        // Si está inactivo, no permitir reactivar manualmente
        return redirect()->back()
            ->with('warning', 'No se puede reactivar manualmente. La cuenta se reactivará automáticamente cuando el negocio pague o renueve su plan.');
    }

    /**
     * Remove the specified business (soft delete)
     */
    public function destroy(int $id): RedirectResponse
    {
        $business = Business::findOrFail($id);

        // Delete photo if exists
        $this->deleteBusinessPhoto($business);

        $business->delete();

        return redirect()->route('superadmin.businesses.index')
            ->with('success', 'Negocio eliminado correctamente.');
    }

    /**
     * Delete business photo from storage
     */
    private function deleteBusinessPhoto(Business $business): void
    {
        if ($business->photo && Storage::disk('public')->exists($business->photo)) {
            Storage::disk('public')->delete($business->photo);
        }
    }
}
