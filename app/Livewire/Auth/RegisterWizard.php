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
                'rfc' => ['required', 'string', 'min:12', 'max:13', 'unique:businesses,rfc', 'regex:/^[A-ZÑ&]{3,4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[A-Z\d]{3}$/'],
                'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            ],
            2 => [
                'email' => ['required', 'email', 'max:255', 'unique:businesses,email', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
                'password' => 'required|string|min:8|max:12',
                'password_confirmation' => 'required|same:password',
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
        'business_name.max' => 'El campo nombre del negocio es muy largo.',
        'rfc.required' => 'El campo RFC es obligatorio.',
        'rfc.min' => 'El campo RFC debe tener al menos 12 caracteres.',
        'rfc.max' => 'El campo RFC debe tener máximo 13 caracteres.',
        'rfc.unique' => 'Este RFC ya está registrado.',
        'rfc.regex' => 'El formato del RFC no es válido. Debe seguir el formato estándar (ejemplo: XAXX010101000).',
        'phone.required' => 'El campo teléfono es obligatorio.',
        'phone.regex' => 'El campo teléfono debe tener exactamente 10 dígitos.',
        'email.required' => 'El campo correo electrónico es obligatorio.',
        'email.email' => 'El campo correo electrónico debe ser válido.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        'email.regex' => 'El correo electrónico debe tener un formato válido con dominio (ejemplo: usuario@dominio.com).',
        'password.required' => 'El campo contraseña es obligatorio.',
        'password.min' => 'El campo contraseña debe tener al menos 8 caracteres.',
        'password.max' => 'El campo contraseña no debe superar los 12 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'password_confirmation.required' => 'El campo confirmación de contraseña es obligatorio.',
        'password_confirmation.same' => 'Las contraseñas no coinciden.',
        'terms.accepted' => 'Debe aceptar los términos y aviso de privacidad.',
    ];

    public function updated($propertyName)
    {
        // Validar en tiempo real solo el campo que cambió
        if ($propertyName === 'rfc') {
            $this->rfc = strtoupper($this->rfc);
        }
        
        if ($propertyName === 'phone') {
            // Eliminar todo excepto números usando str_replace
            $this->phone = str_replace([' ', '-', '(', ')', '+', '.'], '', $this->phone);
            // Mantener solo caracteres numéricos
            $cleanPhone = '';
            for ($i = 0; $i < strlen($this->phone); $i++) {
                if (is_numeric($this->phone[$i])) {
                    $cleanPhone .= $this->phone[$i];
                }
            }
            $this->phone = substr($cleanPhone, 0, 10); // Limitar a 10 dígitos
        }
        
        // Validar el campo específico según el paso actual
        if ($this->currentStep === 1 && in_array($propertyName, ['business_name', 'rfc', 'phone'])) {
            $this->validateOnly($propertyName, $this->rules()[1]);
        } elseif ($this->currentStep === 2 && in_array($propertyName, ['email', 'password', 'password_confirmation'])) {
            if ($propertyName === 'password_confirmation') {
                $this->validateOnly($propertyName, ['password_confirmation' => 'required|same:password']);
            } else {
                $this->validateOnly($propertyName, $this->rules()[2]);
            }
        }
    }

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