@extends('layouts.order-qr')

@section('title', 'Órdenes')
@section('page-title', 'Mis Órdenes')

@section('content')
<div class="space-y-6">
    <!-- Header con filtros y botón crear -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-900">Gestión de Órdenes</h2>
            <p class="mt-1 text-sm text-gray-600">Administra las órdenes de tu negocio</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Filtros -->
            <form method="GET" action="{{ route('orders.index') }}" class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-institutional-blue">
                    <option value="">Todos los estados</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Listos</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregados</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelados</option>
                </select>
            </form>

            <!-- Botón crear orden -->
            <a href="{{ route('orders.create') }}">
                <x-button variant="primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Orden
                </x-button>
            </a>
        </div>
    </div>

    <!-- Tabla de órdenes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Folio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->folio_number }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate">{{ $order->description ?? 'Sin descripción' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'ready' => 'bg-green-100 text-green-800',
                                    'delivered' => 'bg-blue-100 text-blue-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pendiente',
                                    'ready' => 'Listo',
                                    'delivered' => 'Entregado',
                                    'cancelled' => 'Cancelado',
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$order->status] }}">
                                {{ $statusLabels[$order->status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->qr_code_url)
                                <a href="{{ route('orders.downloadQr', $order) }}" class="text-institutional-blue hover:text-blue-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                </a>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('orders.show', $order) }}" class="text-institutional-blue hover:text-blue-700">Ver</a>

                            @if($order->status === 'pending')
                                <form action="{{ route('orders.markAsReady', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-700">Marcar Listo</button>
                                </form>
                            @endif

                            @if(in_array($order->status, ['pending', 'ready']))
                                <button
                                    @click="$dispatch('open-modal-cancel-{{ $order->order_id }}')"
                                    class="text-red-600 hover:text-red-700"
                                >
                                    Cancelar
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal para cancelar -->
                    <x-modal name="cancel-{{ $order->order_id }}" title="Cancelar Orden">
                        <form action="{{ route('orders.cancel', $order) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <p class="text-sm text-gray-600">¿Estás seguro de cancelar la orden <strong>{{ $order->folio_number }}</strong>?</p>

                                <div>
                                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">Motivo de cancelación</label>
                                    <textarea
                                        name="cancellation_reason"
                                        id="cancellation_reason"
                                        rows="3"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-institutional-blue"
                                        placeholder="Explica el motivo de la cancelación..."
                                    ></textarea>
                                </div>
                            </div>

                            <x-slot name="footer">
                                <button type="button" @click="$dispatch('close-modal-cancel-{{ $order->order_id }}')" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                                    Cerrar
                                </button>
                                <x-button type="submit" variant="danger">Cancelar Orden</x-button>
                            </x-slot>
                        </form>
                    </x-modal>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">No hay órdenes disponibles</p>
                            <a href="{{ route('orders.create') }}" class="mt-4 inline-block">
                                <x-button variant="primary" size="sm">Crear Primera Orden</x-button>
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($orders->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
