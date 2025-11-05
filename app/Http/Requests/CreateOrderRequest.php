<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'description' => 'required|string|max:500',
            'mobile_user_id' => 'nullable|integer|exists:mobile_users,mobile_user_id',
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
            'mobile_user_id.integer' => 'El ID de usuario móvil debe ser un número.',
            'mobile_user_id.exists' => 'El usuario móvil especificado no existe.',
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
            'mobile_user_id' => 'usuario móvil',
        ];
    }
}
