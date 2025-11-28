<?php

/**
 * ============================================
 * CETAM - Nearby Businesses Request
 * ============================================
 *
 * @project     Order QR API
 * @file        NearbyBusinessesRequest.php
 * @description Request de validación para búsqueda de negocios cercanos
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

class NearbyBusinessesRequest extends FormRequest
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
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'radius' => 'integer|min:1|max:100',
            'limit' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'latitude.required' => 'La latitud es requerida',
            'latitude.numeric' => 'La latitud debe ser un número válido',
            'latitude.min' => 'La latitud debe estar entre -90 y 90',
            'latitude.max' => 'La latitud debe estar entre -90 y 90',
            'longitude.required' => 'La longitud es requerida',
            'longitude.numeric' => 'La longitud debe ser un número válido',
            'longitude.min' => 'La longitud debe estar entre -180 y 180',
            'longitude.max' => 'La longitud debe estar entre -180 y 180',
            'radius.integer' => 'El radio debe ser un número entero',
            'radius.min' => 'El radio mínimo es 1 km',
            'radius.max' => 'El radio máximo permitido es 100 km',
            'limit.integer' => 'El límite debe ser un número entero',
            'limit.min' => 'El límite mínimo es 1',
            'limit.max' => 'El límite máximo permitido es 50',
            'page.integer' => 'La página debe ser un número entero',
            'page.min' => 'La página mínima es 1',
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
                'message' => 'Parámetros de ubicación inválidos',
                'errors' => $validator->errors()
            ], 400)
        );
    }
}
