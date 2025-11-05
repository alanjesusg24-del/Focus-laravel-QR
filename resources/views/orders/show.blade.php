@extends('layouts.order-qr')

@section('title', 'Orden ' . $order->folio_number)
@section('page-title', 'Detalle de Orden')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Botón regresar -->
    <div>
        <a href="{{ route('orders.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a órdenes
        </a>
    </div>

    <!-- Header con estado -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">{{ $order->folio_number }}</h2>
                <p class="mt-1 text-sm text-gray-600">Creada el {{ $order->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
            </div>

            <div>
                @php
                    $statusClasses = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'ready' => 'bg-green-100 text-green-800',
                        'delivered' => 'bg-blue-100 text-blue-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    $statusLabels = [
                        'pending' => 'Pendiente',
                        'ready' => 'Listo para Recoger',
                        'delivered' => 'Entregado',
                        'cancelled' => 'Cancelado',
                    ];
                @endphp
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClasses[$order->status] }}">
                    {{ $statusLabels[$order->status] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información de la orden -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Descripción -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Descripción</h3>
                <p class="text-gray-700">{{ $order->description ?? 'Sin descripción' }}</p>
            </div>

            <!-- Timeline de estados -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Historial de Estados</h3>

                <div class="flow-root">
                    <ul class="space-y-4">
                        <!-- Creada -->
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Orden Creada</p>
                                <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </li>

                        <!-- Lista -->
                        @if($order->ready_at)
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Orden Lista</p>
                                <p class="text-sm text-gray-500">{{ $order->ready_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </li>
                        @endif

                        <!-- Entregada -->
                        @if($order->delivered_at)
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Orden Entregada</p>
                                <p class="text-sm text-gray-500">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </li>
                        @endif

                        <!-- Cancelada -->
                        @if($order->cancelled_at)
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Orden Cancelada</p>
                                <p class="text-sm text-gray-500">{{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                @if($order->cancellation_reason)
                                <p class="mt-1 text-sm text-red-600">Motivo: {{ $order->cancellation_reason }}</p>
                                @endif
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Panel lateral: QR y tokens -->
        <div class="space-y-6">
            <!-- Código QR -->
            @if($order->qr_code_url)
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Código QR</h3>
                <img src="{{ $order->qr_code_url }}" alt="QR Code" class="mx-auto w-64 h-64 border-2 border-gray-200 rounded-lg">

                <a href="{{ route('orders.downloadQr', $order) }}" class="mt-4 block">
                    <x-button variant="outline" class="w-full">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar QR
                    </x-button>
                </a>
            </div>
            @endif

            <!-- Token de recogida -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Token de Recogida</h3>
                <div class="bg-gray-100 rounded-lg p-4 text-center">
                    <p class="text-2xl font-mono font-bold text-institutional-blue">{{ $order->pickup_token }}</p>
                </div>
                <p class="mt-2 text-xs text-gray-500 text-center">El cliente debe mostrar este código al recoger</p>
            </div>

            <!-- Acciones -->
            <div class="bg-white rounded-lg shadow p-6 space-y-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h3>

                @if($order->status === 'pending')
                    <form action="{{ route('orders.markAsReady', $order) }}" method="POST">
                        @csrf
                        <x-button type="submit" variant="success" class="w-full">
                            Marcar como Listo
                        </x-button>
                    </form>
                @endif

                @if($order->status === 'ready')
                    <button @click="$dispatch('open-modal-deliver')" class="w-full">
                        <x-button type="button" variant="primary" class="w-full">
                            Marcar como Entregado
                        </x-button>
                    </button>
                @endif

                @if(in_array($order->status, ['pending', 'ready']))
                    <button @click="$dispatch('open-modal-cancel')" class="w-full">
                        <x-button type="button" variant="danger" class="w-full">
                            Cancelar Orden
                        </x-button>
                    </button>
                @endif

                <a href="{{ route('orders.edit', $order) }}" class="block">
                    <x-button type="button" variant="outline" class="w-full">
                        Editar Descripción
                    </x-button>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Marcar como Entregado -->
<x-modal name="deliver" title="Marcar como Entregado">
    <form action="{{ route('orders.markAsDelivered', $order) }}" method="POST">
        @csrf
        <div class="space-y-4">
            <p class="text-sm text-gray-600">Ingresa el token de recogida que el cliente te proporcionó:</p>

            <div>
                <label for="pickup_token" class="block text-sm font-medium text-gray-700 mb-2">Token de Recogida</label>
                <input
                    type="text"
                    name="pickup_token"
                    id="pickup_token"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-institutional-blue font-mono"
                    placeholder="Ingresa el token..."
                >
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal-deliver')" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                Cancelar
            </button>
            <x-button type="submit" variant="primary">Confirmar Entrega</x-button>
        </x-slot>
    </form>
</x-modal>

<!-- Modal: Cancelar Orden -->
<x-modal name="cancel" title="Cancelar Orden">
    <form action="{{ route('orders.cancel', $order) }}" method="POST">
        @csrf
        <div class="space-y-4">
            <p class="text-sm text-gray-600">¿Estás seguro de cancelar esta orden?</p>

            <div>
                <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">Motivo de Cancelación</label>
                <textarea
                    name="cancellation_reason"
                    id="cancellation_reason"
                    rows="3"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-institutional-blue"
                    placeholder="Explica el motivo..."
                ></textarea>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal-cancel')" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                Cerrar
            </button>
            <x-button type="submit" variant="danger">Cancelar Orden</x-button>
        </x-slot>
    </form>
</x-modal>
@endsection
