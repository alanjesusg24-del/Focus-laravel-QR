@extends('layouts.order-qr')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Chat Module Notification (si está activado) -->
    @if(auth()->user()->has_chat_module)
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Módulo de Chat Activado</h3>
                    <p class="text-sm text-blue-100">Haz clic en el botón flotante de chat en la esquina inferior derecha para interactuar con tus clientes en tiempo real.</p>
                </div>
            </div>
            <div class="hidden sm:block">
                <span class="px-4 py-2 bg-white/20 rounded-full text-sm font-semibold">
                    +$150 MXN/mes
                </span>
            </div>
        </div>
    </div>
    @endif

    <!-- Cards de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de Órdenes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Órdenes</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $orderStats['total'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">Últimos {{ $days }} días</p>
                </div>
                <div class="h-12 w-12 bg-institutional-blue rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Órdenes Pendientes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendientes</p>
                    <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $orderStats['pending'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">En preparación</p>
                </div>
                <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Órdenes Listas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Listas</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">{{ $orderStats['ready'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">Para recoger</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tiempo Promedio -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tiempo Promedio</p>
                    <p class="mt-2 text-3xl font-bold text-institutional-orange">
                        {{ $orderStats['avg_preparation_time'] ? number_format($orderStats['avg_preparation_time'], 0) : '--' }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">Minutos</p>
                </div>
                <div class="h-12 w-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-institutional-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Órdenes Activas -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Órdenes Activas</h3>
                <a href="{{ route('orders.index') }}" class="text-sm text-institutional-blue hover:text-blue-700">
                    Ver todas
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creada</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activeOrders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->folio_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-xs">{{ Str::limit($order->description, 50) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badge = $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                                $label = $order->status === 'pending' ? 'Pendiente' : 'Listo';
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('orders.show', $order) }}" class="text-institutional-blue hover:text-blue-700">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                            No hay órdenes activas en este momento
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Estado del Plan y Recordatorios -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Estado del Plan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tu Plan Actual</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Plan:</span>
                    <span class="font-semibold text-gray-900">{{ $business->plan->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Precio:</span>
                    <span class="font-semibold text-gray-900">${{ number_format($business->plan->price, 2) }} MXN</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Último Pago:</span>
                    <span class="font-semibold text-gray-900">{{ $business->last_payment_date ? $business->last_payment_date->format('d/m/Y') : 'Pendiente' }}</span>
                </div>

                @if($paymentExpired)
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-800 font-medium">¡Tu pago ha vencido!</p>
                        <a href="{{ route('order-qr.payment.index') }}" class="mt-2 inline-block">
                            <x-button variant="danger" size="sm">Renovar Ahora</x-button>
                        </a>
                    </div>
                @else
                    <a href="{{ route('order-qr.payment.index') }}" class="mt-4 block">
                        <x-button variant="outline" class="w-full">Ver Historial de Pagos</x-button>
                    </a>
                @endif
            </div>
        </div>

        <!-- Órdenes Recientes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Órdenes Recientes</h3>
            <div class="space-y-3">
                @foreach($recentOrders as $order)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $order->folio_number }}</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <a href="{{ route('orders.show', $order) }}" class="text-sm text-institutional-blue hover:text-blue-700">
                        Ver
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
