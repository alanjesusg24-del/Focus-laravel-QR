<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanManagementController extends Controller
{
    /**
     * Display a listing of plans
     */
    public function index()
    {
        $plans = Plan::withCount('businesses')->paginate(15);

        return view('superadmin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan
     */
    public function create()
    {
        return view('superadmin.plans.create');
    }

    /**
     * Store a newly created plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'retention_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'has_chat_module' => 'boolean',
            'has_realerts' => 'boolean',
            'realert_interval_minutes' => 'required_if:has_realerts,1|nullable|integer|min:1',
            'realert_max_count' => 'required_if:has_realerts,1|nullable|integer|min:1|max:20',
            'realert_days' => 'nullable|integer|min:0|max:30',
            'realert_hours' => 'nullable|integer|min:0|max:23',
            'realert_minutes' => 'nullable|integer|min:0|max:59',
        ], [
            'name.required' => 'El nombre del plan es obligatorio.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio debe ser mayor o igual a 0.',
            'duration_days.required' => 'La duración es obligatoria.',
            'duration_days.integer' => 'La duración debe ser un número entero.',
            'duration_days.min' => 'La duración debe ser al menos 1 día.',
            'realert_interval_minutes.required_if' => 'El intervalo de re-alertas es obligatorio cuando las re-alertas están activas.',
            'realert_interval_minutes.min' => 'El intervalo debe ser al menos 1 minuto.',
            'realert_interval_minutes.max' => 'El intervalo no puede ser mayor a 1440 minutos (24 horas).',
            'realert_max_count.required_if' => 'El máximo de re-alertas es obligatorio cuando las re-alertas están activas.',
            'realert_max_count.min' => 'Debe enviar al menos 1 re-alerta.',
            'realert_max_count.max' => 'No se pueden enviar más de 20 re-alertas.',
        ]);

        // Convert checkbox values to boolean
        $validated['is_active'] = $request->has('is_active');
        $validated['has_chat_module'] = $request->has('has_chat_module');
        $validated['has_realerts'] = $request->has('has_realerts');

        // Clear realert fields if realerts is disabled
        if (!$validated['has_realerts']) {
            $validated['realert_interval_minutes'] = null;
            $validated['realert_max_count'] = null;
        }

        Plan::create($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan creado correctamente.');
    }

    /**
     * Display the specified plan
     */
    public function show($id)
    {
        $plan = Plan::withCount('businesses')->findOrFail($id);

        return view('superadmin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified plan
     */
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);

        return view('superadmin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan
     */
    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'retention_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'has_chat_module' => 'boolean',
            'has_realerts' => 'boolean',
            'realert_interval_minutes' => 'required_if:has_realerts,1|nullable|integer|min:1',
            'realert_max_count' => 'required_if:has_realerts,1|nullable|integer|min:1|max:20',
            'realert_days' => 'nullable|integer|min:0|max:30',
            'realert_hours' => 'nullable|integer|min:0|max:23',
            'realert_minutes' => 'nullable|integer|min:0|max:59',
        ], [
            'name.required' => 'El nombre del plan es obligatorio.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio debe ser mayor o igual a 0.',
            'duration_days.required' => 'La duración es obligatoria.',
            'duration_days.integer' => 'La duración debe ser un número entero.',
            'duration_days.min' => 'La duración debe ser al menos 1 día.',
            'realert_interval_minutes.required_if' => 'El intervalo de re-alertas es obligatorio cuando las re-alertas están activas.',
            'realert_interval_minutes.min' => 'El intervalo debe ser al menos 1 minuto.',
            'realert_interval_minutes.max' => 'El intervalo no puede ser mayor a 1440 minutos (24 horas).',
            'realert_max_count.required_if' => 'El máximo de re-alertas es obligatorio cuando las re-alertas están activas.',
            'realert_max_count.min' => 'Debe enviar al menos 1 re-alerta.',
            'realert_max_count.max' => 'No se pueden enviar más de 20 re-alertas.',
        ]);

        // Convert checkbox values to boolean
        $validated['is_active'] = $request->has('is_active');
        $validated['has_chat_module'] = $request->has('has_chat_module');
        $validated['has_realerts'] = $request->has('has_realerts');

        // Clear realert fields if realerts is disabled
        if (!$validated['has_realerts']) {
            $validated['realert_interval_minutes'] = null;
            $validated['realert_max_count'] = null;
        }

        // Track if chat module changed
        $chatModuleChanged = $plan->has_chat_module !== $validated['has_chat_module'];

        $plan->update($validated);

        // Update all businesses with this plan if chat module changed
        if ($chatModuleChanged) {
            $plan->businesses()->update(['has_chat_module' => $validated['has_chat_module']]);
        }

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    /**
     * Remove the specified plan
     */
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);

        // Check if plan has businesses
        if ($plan->businesses()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el plan porque tiene negocios asociados.');
        }

        $plan->delete();

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan eliminado correctamente.');
    }
}
