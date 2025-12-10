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
    public $plan_id = 4; // Plan por defecto (primer plan activo)
    public $terms = false;

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
        'business_name.required' => 'El nombre del negocio es obligatorio.',
        'rfc.required' => 'El RFC es obligatorio.',
        'rfc.unique' => 'Este RFC ya está registrado. Por favor verifica tus datos.',
        'phone.required' => 'El teléfono es obligatorio.',
        'phone.digits' => 'El teléfono debe tener 10 dígitos.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El formato del correo no es válido.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
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