@extends('layouts.order-qr')

@section('title', 'Crear Ticket de Soporte')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('support.index') }}" class="text-institutional-blue hover:underline flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Tickets
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Crear Ticket de Soporte</h1>

        <form method="POST" action="{{ route('support.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Asunto *</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-transparent @error('subject') border-red-500 @enderror">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Prioridad *</label>
                    <select name="priority" id="priority" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-transparent @error('priority') border-red-500 @enderror">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Baja</option>
                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Media</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                    <textarea name="description" id="description" rows="6" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Attachment -->
                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Archivo Adjunto (Opcional)</label>
                    <input type="file" name="attachment" id="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-transparent @error('attachment') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG, PDF, DOC, DOCX. Tamaño máximo: 5MB</p>
                    @error('attachment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('support.index') }}">
                        <x-button variant="outline" type="button">Cancelar</x-button>
                    </a>
                    <x-button variant="primary" type="submit">Crear Ticket</x-button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
