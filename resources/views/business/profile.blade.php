@extends('layouts.order-qr')

@section('title', 'Mi Perfil')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Mi Perfil</h1>

    @if(session('success'))
        <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Info Card -->
        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Información del Negocio</h2>
                <a href="{{ route('business.edit') }}">
                    <x-button variant="outline" size="sm">Editar</x-button>
                </a>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-500">Nombre del Negocio</label>
                    <p class="text-gray-900 font-medium">{{ $business->business_name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">RFC</label>
                    <p class="text-gray-900 font-medium">{{ $business->rfc }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Email</label>
                    <p class="text-gray-900 font-medium">{{ $business->email }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Teléfono</label>
                    <p class="text-gray-900 font-medium">{{ $business->phone }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Dirección</label>
                    <p class="text-gray-900 font-medium">{{ $business->address ?? 'No especificada' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Plan Actual</label>
                    <p class="text-gray-900 font-medium">{{ $business->plan->name ?? 'Sin plan' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Precio Mensual</label>
                    <p class="text-gray-900 font-medium">${{ number_format($business->monthly_price ?? 0, 2) }} MXN</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Módulo de Chat</label>
                    <p class="text-gray-900 font-medium">
                        @if($business->has_chat_module)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Activado</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">No activado</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Retención de Datos</label>
                    <p class="text-gray-900 font-medium">{{ $business->data_retention_months ?? 1 }} {{ $business->data_retention_months == 1 ? 'mes' : 'meses' }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-2">
                    <a href="{{ route('business.changePassword') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                        Cambiar Contraseña
                    </a>
                    <a href="{{ route('order-qr.payment.index') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                        Gestionar Plan
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Estado de Cuenta</h3>
                <div class="flex items-center">
                    @if($business->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Activo</span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">Inactivo</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
