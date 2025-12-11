<?php

namespace App\Livewire\Auth;

use App\Models\Plan;
use App\Models\Business;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Company: CETAM
 * Project: FQR
 * File: RegisterWizard.php
 * Description: Wizard de registro corregido para login funcional.
 * Standard: Section 7.5
 */
class RegisterWizard extends Component
{
    // Estado
    public int $currentStep = 1;

    // Paso 1: Negocio
    public $business_name;
    public $rfc;
    public $phone;

    // Paso 2: Cuenta
    public $email;
    public $password;
    public $password_confirmation;

    // Paso 3: Plan
    public $plan_id; // Plan por defecto (se asigna en mount)
    public $terms = false;
    public $hasActivePlans = true; // Flag para verificar si hay planes activos

    public function mount()
    {
        // Verificar si hay planes activos disponibles
        $activePlansCount = Plan::where('is_active', true)->count();

        if ($activePlansCount === 0) {
            $this->hasActivePlans = false;
            session()->flash('error', 'Actualmente no hay planes disponibles para registro. Por favor contacta al administrador del sistema para que active planes de suscripción.');
            return redirect()->route('business.login');
        }

        // Obtener el primer plan activo disponible
        $firstPlan = Plan::where('is_active', true)->orderBy('plan_id')->first();
        $this->plan_id = $firstPlan->plan_id;
    }

    protected function rules()
    {
        return [
            1 => [
                'business_name' => 'required|string|max:255',
                'rfc' => 'required|string|max:13|unique:businesses,rfc',
                'phone' => 'required|numeric|digits:10',
            ],
            2 => [
                'email' => 'required|email|unique:businesses,email',
                'password' => 'required|min:8|confirmed',
            ],
            3 => [
                'plan_id' => 'required',
                'terms' => 'accepted',
            ],
        ];
    }

    // Mensajes en Español
    protected $messages = [
        'business_name.required' => 'El campo nombre del negocio es obligatorio.',
        'business_name.max' => 'El nombre del negocio no puede tener más de 255 caracteres.',
        'rfc.required' => 'El campo RFC es obligatorio.',
        'rfc.max' => 'El RFC no puede tener más de 13 caracteres.',
        'rfc.unique' => 'Este RFC ya está registrado. Por favor verifica tus datos.',
        'phone.required' => 'El campo teléfono es obligatorio.',
        'phone.numeric' => 'El teléfono debe contener solo números.',
        'phone.digits' => 'El teléfono debe tener 10 dígitos.',
        'email.required' => 'El campo correo electrónico es obligatorio.',
        'email.email' => 'El correo electrónico debe ser una dirección válida.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        'password.required' => 'El campo contraseña es obligatorio.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'terms.accepted' => 'Debes aceptar los términos y condiciones.',
    ];

    public function nextStep()
    {
        $this->validate($this->rules()[$this->currentStep]);
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    public function selectPlan($id)
    {
        $this->plan_id = $id;
    }

    public function submit()
    {
        // Verificar si hay planes activos antes de proceder
        if (!$this->hasActivePlans) {
            session()->flash('error', 'No se puede completar el registro porque no hay planes disponibles. Por favor contacta al administrador del sistema.');
            return;
        }

        $this->validate($this->rules()[3]);

        DB::beginTransaction();

        try {
            
            $business = Business::create([
                'business_name' => $this->business_name,
                'rfc' => $this->rfc,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'phone' => $this->phone,
                'address' => '', // Campo requerido por la base de datos
                'plan_id' => $this->plan_id, // Plan seleccionado pero no pagado
                'is_active' => false, // Se activa después del pago
                'registration_date' => now(),
                'latitude' => 0,
                'longitude' => 0,
                'last_payment_date' => null, // Sin pagos aún
            ]);

            DB::commit();

            // Iniciar sesión automáticamente después del registro
            auth()->guard('business')->login($business);

            session()->flash('success', '¡Cuenta creada exitosamente! Para activar tu cuenta, realiza el pago de tu plan.');

            return redirect()->route('business.payments.index');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Log del error completo para depuración
            \Log::error('Error de registro de negocio:', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A'
            ]);

            if ($e->getCode() == 23000) {
                if (str_contains($e->getMessage(), 'rfc')) {
                    session()->flash('error', 'El RFC ingresado ya está registrado en el sistema.');
                } elseif (str_contains($e->getMessage(), 'email')) {
                    session()->flash('error', 'El correo electrónico ya está registrado en el sistema.');
                } elseif (str_contains($e->getMessage(), 'plan_id') || str_contains($e->getMessage(), 'businesses_plan_id_foreign')) {
                    session()->flash('error', 'El plan seleccionado no es válido. Por favor recarga la página e intenta nuevamente.');
                } else {
                    session()->flash('error', 'Ya existe un registro con estos datos.');
                }
            } else {
                session()->flash('error', 'Error al procesar el registro. Por favor intenta nuevamente. Código: ' . $e->getCode());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error inesperado en registro:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $plans = Plan::where('is_active', true)->get();
        
        return view('livewire.auth.register-wizard', compact('plans'))
            ->layout('layouts.base');
    }
}