@extends('layouts.order-qr')

@section('title', 'Pago Exitoso')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 text-center">
    <div class="bg-white border-2 border-green-400 rounded-lg p-8 shadow-lg">
        <div class="mb-4">
            <svg class="w-20 h-20 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-green-600 mb-4">¡Pago Procesado Exitosamente!</h1>

        <p class="text-gray-700 mb-6">
            Tu pago ha sido procesado correctamente. Tu cuenta está activa y ahora puedes acceder a todas las funcionalidades del sistema.
        </p>

        @if(session('success'))
            <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-6">
                <p class="text-sm text-blue-800">{{ session('success') }}</p>
            </div>
        @endif

        <div class="space-y-3">
            <a href="{{ route('business.dashboard.index') }}" class="inline-block px-8 py-3 text-base font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                Ir al Dashboard
            </a>

            <br>

            <a href="{{ route('business.orders.index') }}" class="inline-block px-8 py-3 text-base font-medium text-blue-600 bg-transparent border-2 border-blue-600 hover:bg-blue-50 rounded-lg transition">
                Crear Órdenes
            </a>

            <br>

            <a href="{{ route('order-qr.payment.history') }}" class="inline-block text-gray-600 hover:text-blue-600 hover:underline">
                Ver Historial de Pagos
            </a>
        </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded">
        <p class="text-sm text-yellow-800">
            <strong>Modo de Prueba:</strong> Este pago ha sido simulado para fines de desarrollo y pruebas.
        </p>
    </div>
</div>
@endsection
