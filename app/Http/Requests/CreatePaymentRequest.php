<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan_id' => 'required|integer|exists:plans,plan_id',
            'payment_method_id' => 'required|string|max:255',
            'payment_type' => 'sometimes|in:one_time,subscription',
            'cardholder_name' => 'sometimes|required|string|max:255',
            'billing_address' => 'sometimes|required|string|max:500',
            'billing_city' => 'sometimes|required|string|max:100',
            'billing_state' => 'sometimes|required|string|max:100',
            'billing_postal_code' => 'sometimes|required|string|regex:/^[0-9]{5}$/',
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
            'plan_id.required' => 'Debe seleccionar un plan.',
            'plan_id.exists' => 'El plan seleccionado no existe.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.max' => 'El método de pago no puede exceder 255 caracteres.',
            'payment_type.in' => 'El tipo de pago debe ser único o suscripción.',
            'cardholder_name.required' => 'El nombre del titular es obligatorio.',
            'cardholder_name.max' => 'El nombre del titular no puede exceder 255 caracteres.',
            'billing_address.required' => 'La dirección de facturación es obligatoria.',
            'billing_address.max' => 'La dirección de facturación no puede exceder 500 caracteres.',
            'billing_city.required' => 'La ciudad de facturación es obligatoria.',
            'billing_state.required' => 'El estado de facturación es obligatorio.',
            'billing_postal_code.required' => 'El código postal de facturación es obligatorio.',
            'billing_postal_code.regex' => 'El código postal debe tener 5 dígitos.',
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
            'plan_id' => 'plan',
            'payment_method_id' => 'método de pago',
            'payment_type' => 'tipo de pago',
            'cardholder_name' => 'nombre del titular',
            'billing_address' => 'dirección de facturación',
            'billing_city' => 'ciudad de facturación',
            'billing_state' => 'estado de facturación',
            'billing_postal_code' => 'código postal de facturación',
        ];
    }
}
