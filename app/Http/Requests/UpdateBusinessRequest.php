<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated businesses can update their profile
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $businessId = auth()->id();

        return [
            'business_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('businesses', 'email')->ignore($businessId, 'business_id'),
            ],
            'phone' => 'required|string|max:15|regex:/^[0-9+\-\s()]+$/',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10|regex:/^[0-9]{5}$/',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo_url' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'theme' => 'sometimes|in:professional,modern,classic',
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
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El formato del teléfono no es válido.',
            'address.required' => 'La dirección es obligatoria.',
            'city.required' => 'La ciudad es obligatoria.',
            'state.required' => 'El estado es obligatorio.',
            'postal_code.required' => 'El código postal es obligatorio.',
            'postal_code.regex' => 'El código postal debe tener 5 dígitos.',
            'latitude.between' => 'La latitud debe estar entre -90 y 90.',
            'longitude.between' => 'La longitud debe estar entre -180 y 180.',
            'logo_url.image' => 'El archivo debe ser una imagen.',
            'logo_url.max' => 'La imagen no puede exceder 2MB.',
            'logo_url.mimes' => 'La imagen debe ser JPG, JPEG o PNG.',
            'theme.in' => 'El tema seleccionado no es válido.',
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
            'phone' => 'teléfono',
            'address' => 'dirección',
            'city' => 'ciudad',
            'state' => 'estado',
            'postal_code' => 'código postal',
            'latitude' => 'latitud',
            'longitude' => 'longitud',
            'logo_url' => 'logo',
            'theme' => 'tema',
        ];
    }
}
