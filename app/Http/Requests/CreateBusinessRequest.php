<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBusinessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Public registration - anyone can create a business account
        return true;
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Convert RFC to uppercase
        if ($this->has('rfc')) {
            $this->merge([
                'rfc' => strtoupper($this->rfc),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:businesses,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:15|regex:/^[0-9+\-\s()]+$/',
            'rfc' => 'required|string|min:12|max:13|unique:businesses,rfc',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10|regex:/^[0-9]{5}$/',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'plan_id' => 'required|integer|exists:plans,plan_id',
        ];
    }

    /**
     * Get custom messages for validator errors (Spanish)
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'El nombre del negocio es obligatorio.',
            'business_name.max' => 'El nombre del negocio no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El formato del teléfono no es válido.',
            'rfc.required' => 'El RFC es obligatorio.',
            'rfc.min' => 'El RFC debe tener al menos 12 caracteres.',
            'rfc.max' => 'El RFC debe tener máximo 13 caracteres.',
            'rfc.unique' => 'Este RFC ya está registrado.',
            'address.required' => 'La dirección es obligatoria.',
            'city.required' => 'La ciudad es obligatoria.',
            'state.required' => 'El estado es obligatorio.',
            'postal_code.required' => 'El código postal es obligatorio.',
            'postal_code.regex' => 'El código postal debe tener 5 dígitos.',
            'latitude.between' => 'La latitud debe estar entre -90 y 90.',
            'longitude.between' => 'La longitud debe estar entre -180 y 180.',
            'plan_id.required' => 'Debe seleccionar un plan.',
            'plan_id.exists' => 'El plan seleccionado no existe.',
        ];
    }

    /**
     * Get custom attributes for validator errors (Spanish)
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'business_name' => 'nombre del negocio',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'phone' => 'teléfono',
            'rfc' => 'RFC',
            'address' => 'dirección',
            'city' => 'ciudad',
            'state' => 'estado',
            'postal_code' => 'código postal',
            'latitude' => 'latitud',
            'longitude' => 'longitud',
            'plan_id' => 'plan',
        ];
    }
}
