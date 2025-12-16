<?php

/**
 * Company: CETAM
 * Project: FF
 * File: PlanManagementController.php (SuperAdmin)
 * Created on: 20/11/2025
 * Created by: Dafne Vanessa Castillo Moreo
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 16/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreo |
 *   Description: Refactored Plan Management Controller|
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PlanManagementController extends Controller
{
    /**
     * Display a listing of plans
     */
    public function index(Request $request): View
    {
        $query = Plan::withCount('businesses');

        // 5.5: Apply filters through private method
        $query = $this->applyFilters($query, $request);

        $plans = $query->orderBy('plan_id', 'desc')->paginate(15);

        return view('superadmin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan
     */
    public function create(): View
    {
        return view('superadmin.plans.create');
    }

    /**
     * Store a newly created plan
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePlanData($request);

        // 5.5: Extract checkbox processing to private method
        $processedData = $this->processCheckboxValues($validated, $request);

        Plan::create($processedData);

        return redirect()->route('superadmin.plans.index');
    }

    /**
     * Display the specified plan
     */
    public function show(int $id): View
    {
        $plan = Plan::withCount('businesses')->findOrFail($id);

        return view('superadmin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified plan
     */
    public function edit(int $id): View
    {
        $plan = Plan::findOrFail($id);

        return view('superadmin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $plan = Plan::findOrFail($id);

        $validated = $this->validatePlanData($request);

        // 5.5: Extract checkbox processing to private method
        $processedData = $this->processCheckboxValues($validated, $request);

        // Track if chat module changed
        $chatModuleChanged = $plan->has_chat_module !== $processedData['has_chat_module'];

        $plan->update($processedData);

        // Update all businesses with this plan if chat module changed
        if ($chatModuleChanged) {
            $plan->businesses()->update(['has_chat_module' => $processedData['has_chat_module']]);
        }

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    /**
     * Remove the specified plan
     */
    public function destroy(int $id): RedirectResponse
    {
        $plan = Plan::findOrFail($id);

        // 5.4.1: Early Return - Plan has businesses
        if ($plan->businesses()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el plan porque tiene negocios asociados.');
        }

        $plan->delete();

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan eliminado correctamente.');
    }

    /**
     * Apply all filters to the query
     * 5.5: Private helper method following SRP
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        return $query;
    }

    /**
     * Validate plan data
     * 5.5: Private helper method following SRP - Eliminates code duplication
     */
    private function validatePlanData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'retention_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'has_chat_module' => 'boolean',
            'has_realerts' => 'boolean',
            'realert_interval_minutes' => 'required_if:has_realerts,1|nullable|integer|min:1',
            'realert_max_count' => 'required_if:has_realerts,1|nullable|integer|min:1|max:20',
            'realert_days' => 'nullable|integer|min:0|max:30',
            'realert_hours' => 'nullable|integer|min:0|max:23',
            'realert_minutes' => 'nullable|integer|min:0|max:59',
        ], $this->getValidationMessages());
    }

    /**
     * Get validation messages
     * 5.5: Private helper method following SRP
     */
    private function getValidationMessages(): array
    {
        return [
            'name.required' => 'El nombre del plan es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio debe ser mayor o igual a 0.',
            'duration_days.required' => 'La duración es obligatoria.',
            'duration_days.integer' => 'La duración debe ser un número entero.',
            'duration_days.min' => 'La duración debe ser al menos 1 día.',
            'retention_days.required' => 'La retención de datos es obligatoria.',
            'retention_days.min' => 'La retención de datos debe ser al menos de 1 día.',
            'realert_interval_minutes.required_if' => 'El intervalo de re-alertas es obligatorio cuando las re-alertas están activas.',
            'realert_interval_minutes.min' => 'El intervalo debe ser al menos 1 minuto.',
            'realert_interval_minutes.max' => 'El intervalo no puede ser mayor a 1440 minutos (24 horas).',
            'realert_max_count.required_if' => 'El máximo de re-alertas es obligatorio cuando las re-alertas están activas.',
            'realert_max_count.min' => 'Debe enviar al menos 1 re-alerta.',
            'realert_max_count.max' => 'No se pueden enviar más de 20 re-alertas.',
        ];
    }

    /**
     * Process checkbox values and clear realert fields if disabled
     * 5.5: Private helper method following SRP - Eliminates code duplication
     */
    private function processCheckboxValues(array $validated, Request $request): array
    {
        // Convert checkbox values to boolean
        $validated['is_active'] = $request->has('is_active');
        $validated['has_chat_module'] = $request->has('has_chat_module');
        $validated['has_realerts'] = $request->has('has_realerts');

        // Clear realert fields if realerts is disabled
        if (!$validated['has_realerts']) {
            $validated['realert_interval_minutes'] = null;
            $validated['realert_max_count'] = null;
        }

        return $validated;
    }
}
