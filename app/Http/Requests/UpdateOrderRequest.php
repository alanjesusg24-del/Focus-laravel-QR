<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $order = $this->route('order');

        // User must be authenticated and own the order
        return auth()->check() &&
               $order &&
               $order->business_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:500',
            'pickup_token' => 'sometimes|required|string|max:100',
            'cancellation_reason' => 'sometimes|required|string|max:500',
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
            'description.required' => 'La descripción de la orden es obligatoria.',
            'description.string' => 'La descripción debe ser texto válido.',
            'description.max' => 'La descripción no puede exceder 500 caracteres.',
            'pickup_token.required' => 'El token de recogida es obligatorio.',
            'pickup_token.string' => 'El token de recogida debe ser texto válido.',
            'pickup_token.max' => 'El token de recogida no puede exceder 100 caracteres.',
            'cancellation_reason.required' => 'El motivo de cancelación es obligatorio.',
            'cancellation_reason.string' => 'El motivo de cancelación debe ser texto válido.',
            'cancellation_reason.max' => 'El motivo de cancelación no puede exceder 500 caracteres.',
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
            'description' => 'descripción',
            'pickup_token' => 'token de recogida',
            'cancellation_reason' => 'motivo de cancelación',
        ];
    }
}
