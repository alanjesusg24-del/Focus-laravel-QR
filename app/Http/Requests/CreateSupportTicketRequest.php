<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupportTicketRequest extends FormRequest
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
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
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
            'subject.required' => 'El asunto del ticket es obligatorio.',
            'subject.max' => 'El asunto no puede exceder 255 caracteres.',
            'description.required' => 'La descripción del problema es obligatoria.',
            'description.max' => 'La descripción no puede exceder 2000 caracteres.',
            'priority.required' => 'La prioridad es obligatoria.',
            'priority.in' => 'La prioridad debe ser baja, media o alta.',
            'attachment.file' => 'El adjunto debe ser un archivo válido.',
            'attachment.max' => 'El archivo adjunto no puede exceder 5MB.',
            'attachment.mimes' => 'El archivo adjunto debe ser JPG, PNG, PDF, DOC o DOCX.',
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
            'subject' => 'asunto',
            'description' => 'descripción',
            'priority' => 'prioridad',
            'attachment' => 'archivo adjunto',
        ];
    }
}
