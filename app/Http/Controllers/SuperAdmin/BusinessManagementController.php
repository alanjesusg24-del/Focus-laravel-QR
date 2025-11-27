<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BusinessManagementController extends Controller
{
    /**
     * Display a listing of businesses
     */
    public function index(Request $request)
    {
        $query = Business::with('plan');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('rfc', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by plan
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'registration_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $businesses = $query->paginate(15)->withQueryString();
        $plans = Plan::all();

        return view('superadmin.businesses.index', compact('businesses', 'plans'));
    }

    /**
     * Display the specified business
     */
    public function show($id)
    {
        $business = Business::with(['plan', 'orders', 'payments', 'supportTickets'])
            ->findOrFail($id);

        // Get statistics for this business
        $stats = [
            'total_orders' => $business->orders()->count(),
            'pending_orders' => $business->orders()->where('status', 'pending')->count(),
            'delivered_orders' => $business->orders()->where('status', 'delivered')->count(),
            'total_payments' => $business->payments()->sum('amount'),
            'open_tickets' => $business->supportTickets()->where('status', 'open')->count(),
        ];

        $recentOrders = $business->orders()->latest()->limit(10)->get();
        $recentPayments = $business->payments()->latest()->limit(10)->get();

        return view('superadmin.businesses.show', compact('business', 'stats', 'recentOrders', 'recentPayments'));
    }

    /**
     * Show the form for editing the specified business
     */
    public function edit($id)
    {
        $business = Business::findOrFail($id);
        $plans = Plan::all();

        return view('superadmin.businesses.edit', compact('business', 'plans'));
    }

    /**
     * Update the specified business
     */
    public function update(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        $request->validate([
            'business_name' => 'required|string|max:255',
            'rfc' => 'nullable|string|max:13',
            'email' => [
                'required',
                'email',
                Rule::unique('businesses', 'email')->ignore($business->business_id, 'business_id')
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_description' => 'nullable|string|max:500',
            'plan_id' => 'required|exists:plans,plan_id',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'business_name.required' => 'El nombre del negocio es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'plan_id.required' => 'El plan es obligatorio.',
            'plan_id.exists' => 'El plan seleccionado no es válido.',
            'latitude.between' => 'La latitud debe estar entre -90 y 90.',
            'longitude.between' => 'La longitud debe estar entre -180 y 180.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser formato JPEG, PNG o JPG.',
            'photo.max' => 'La imagen no debe superar los 2MB.',
        ]);

        $data = $request->except(['password', 'photo', '_token', '_method']);

        // Handle checkbox (is_active)
        $data['is_active'] = $request->has('is_active');

        // Si se cambió el plan, actualizar los campos derivados del plan
        if ($request->filled('plan_id') && $request->plan_id != $business->plan_id) {
            $selectedPlan = Plan::findOrFail($request->plan_id);
            $data['monthly_price'] = $selectedPlan->price;
            $data['has_chat_module'] = $selectedPlan->has_chat_module;
            $data['data_retention_months'] = $selectedPlan->retention_days ? ceil($selectedPlan->retention_days / 30) : 1;
        }

        // Handle password update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($business->photo && Storage::disk('public')->exists($business->photo)) {
                Storage::disk('public')->delete($business->photo);
            }

            $path = $request->file('photo')->store('businesses', 'public');
            $data['photo'] = $path;
        }

        $business->update($data);

        return redirect()->route('superadmin.businesses.index')
            ->with('success', 'Negocio actualizado correctamente.');
    }

    /**
     * Toggle business active status
     */
    public function toggleStatus($id)
    {
        $business = Business::findOrFail($id);
        $business->is_active = !$business->is_active;
        $business->save();

        $status = $business->is_active ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Negocio {$status} correctamente.");
    }

    /**
     * Remove the specified business (soft delete)
     */
    public function destroy($id)
    {
        $business = Business::findOrFail($id);

        // Delete photo if exists
        if ($business->photo && Storage::disk('public')->exists($business->photo)) {
            Storage::disk('public')->delete($business->photo);
        }

        $business->delete();

        return redirect()->route('superadmin.businesses.index')
            ->with('success', 'Negocio eliminado correctamente.');
    }
}
