<?php

/**
 * ============================================
 * CETAM - Search Businesses Request
 * ============================================
 *
 * @project     Order QR API
 * @file        SearchBusinessesRequest.php
 * @description Request de validación para búsqueda de negocios
 * @author      CETAM Dev Team
 * @created     2025-11-26
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchBusinessesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Public endpoint - no authentication required
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.string' => 'La consulta debe ser una cadena de texto',
            'query.max' => 'La consulta no puede exceder los 255 caracteres',
            'city.string' => 'La ciudad debe ser una cadena de texto',
            'city.max' => 'La ciudad no puede exceder los 100 caracteres',
            'state.string' => 'El estado debe ser una cadena de texto',
            'state.max' => 'El estado no puede exceder los 100 caracteres',
            'postal_code.string' => 'El código postal debe ser una cadena de texto',
            'postal_code.max' => 'El código postal no puede exceder los 20 caracteres',
            'latitude.numeric' => 'La latitud debe ser un número válido',
            'latitude.min' => 'La latitud debe estar entre -90 y 90',
            'latitude.max' => 'La latitud debe estar entre -90 y 90',
            'longitude.numeric' => 'La longitud debe ser un número válido',
            'longitude.min' => 'La longitud debe estar entre -180 y 180',
            'longitude.max' => 'La longitud debe estar entre -180 y 180',
            'page.integer' => 'La página debe ser un número entero',
            'page.min' => 'La página mínima es 1',
            'per_page.integer' => 'Los resultados por página deben ser un número entero',
            'per_page.min' => 'El mínimo de resultados por página es 1',
            'per_page.max' => 'El máximo de resultados por página es 50',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Parámetros de búsqueda inválidos',
                'errors' => $validator->errors()
            ], 400)
        );
    }
}
