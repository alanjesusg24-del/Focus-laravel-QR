@extends('layouts.order-qr')

@section('title', 'Crear Orden')
@section('page-title', 'Nueva Orden')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Crear Nueva Orden</h2>
            <p class="mt-1 text-sm text-gray-600">Completa los datos para generar una orden con código QR</p>
        </div>

        <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Descripción -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción de la Orden <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="description"
                    id="description"
                    rows="4"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-institutional-blue @error('description') border-red-500 @enderror"
                    placeholder="Ej: 2 cafés americanos, 1 latte grande, 1 bagel..."
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <p class="mt-1 text-xs text-gray-500">Máximo 500 caracteres</p>
            </div>

            <!-- ID de usuario móvil (opcional) -->
            <div>
                <label for="mobile_user_id" class="block text-sm font-medium text-gray-700 mb-2">
                    ID de Usuario Móvil <span class="text-gray-400">(Opcional)</span>
                </label>
                <input
                    type="number"
                    name="mobile_user_id"
                    id="mobile_user_id"
                    value="{{ old('mobile_user_id') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-institutional-blue @error('mobile_user_id') border-red-500 @enderror"
                    placeholder="Deja en blanco si el usuario escaneará el QR después"
                >

                @error('mobile_user_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <p class="mt-1 text-xs text-gray-500">Si conoces el ID del usuario, la orden se vinculará automáticamente</p>
            </div>

            <!-- Info adicional -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">Sobre el código QR:</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li>Se generará automáticamente al crear la orden</li>
                            <li>El cliente podrá escanearlo para vincular la orden a su app</li>
                            <li>Se generará un token de recogida único de 16 caracteres</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('orders.index') }}">
                    <x-button type="button" variant="outline">
                        Cancelar
                    </x-button>
                </a>

                <x-button type="submit" variant="primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear Orden
                </x-button>
            </div>
        </form>
    </div>
</div>
@endsection
