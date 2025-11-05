@extends('layouts.order-qr')

@section('title', 'Editar Ticket #' . $supportTicket->support_ticket_id)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('support.show', $supportTicket->support_ticket_id) }}" class="text-institutional-blue hover:underline flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al Ticket
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Editar Ticket #{{ $supportTicket->support_ticket_id }}</h1>

        <form method="POST" action="{{ route('support.update', $supportTicket->support_ticket_id) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Subject (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asunto</label>
                    <input type="text" value="{{ $supportTicket->subject }}" disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-500">El asunto no puede ser modificado</p>
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Prioridad *</label>
                    <select name="priority" id="priority" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-transparent @error('priority') border-red-500 @enderror">
                        <option value="low" {{ old('priority', $supportTicket->priority) === 'low' ? 'selected' : '' }}>Baja</option>
                        <option value="medium" {{ old('priority', $supportTicket->priority) === 'medium' ? 'selected' : '' }}>Media</option>
                        <option value="high" {{ old('priority', $supportTicket->priority) === 'high' ? 'selected' : '' }}>Alta</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                    <textarea name="description" id="description" rows="6" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $supportTicket->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('support.show', $supportTicket->support_ticket_id) }}">
                        <x-button variant="outline" type="button">Cancelar</x-button>
                    </a>
                    <x-button variant="primary" type="submit">Actualizar Ticket</x-button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
