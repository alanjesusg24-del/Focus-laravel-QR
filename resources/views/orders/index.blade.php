@extends('layouts.business-app')

@section('title', 'Órdenes - Order QR System')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Gestión de Órdenes</h2>
            <p class="mb-0">Administra las órdenes de tu negocio</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <form method="GET" action="{{ route('business.orders.index') }}">
                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Listos</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregados</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelados</option>
                    </select>
                </form>
            </div>
            <a href="{{ route('business.orders.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Orden
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">Órdenes</h2>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="border-bottom" scope="col">Folio</th>
                        <th class="border-bottom" scope="col">Descripción</th>
                        <th class="border-bottom" scope="col">Estado</th>
                        <th class="border-bottom" scope="col">Creada</th>
                        <th class="border-bottom" scope="col">QR</th>
                        <th class="border-bottom text-center" scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="fw-bolder text-gray-500">{{ $order->folio_number }}</td>
                        <td class="text-gray-900">{{ Str::limit($order->description ?? 'Sin descripción', 50) }}</td>
                        <td>
                            @php
                                $statusConfig = [
                                    'pending' => ['class' => 'bg-warning', 'label' => 'Pendiente'],
                                    'ready' => ['class' => 'bg-success', 'label' => 'Listo'],
                                    'delivered' => ['class' => 'bg-info', 'label' => 'Entregado'],
                                    'cancelled' => ['class' => 'bg-danger', 'label' => 'Cancelado'],
                                ];
                                $config = $statusConfig[$order->status] ?? ['class' => 'bg-secondary', 'label' => 'Desconocido'];
                            @endphp
                            <span class="badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                        </td>
                        <td class="text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($order->qr_code_url)
                                <a href="{{ route('business.orders.downloadQr', $order) }}" class="btn btn-sm btn-secondary" title="Descargar QR">
                                    <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('business.orders.show', $order) }}" class="btn btn-sm btn-primary">Ver</a>

                                @if($order->status === 'pending')
                                    <form action="{{ route('business.orders.markAsReady', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success">Marcar Listo</button>
                                    </form>
                                @endif

                                @if(in_array($order->status, ['pending', 'ready']))
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $order->order_id }}">
                                        Cancelar
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Cancel Modal -->
                    <div class="modal fade" id="cancelModal{{ $order->order_id }}" tabindex="-1" aria-labelledby="cancelModalLabel{{ $order->order_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cancelModalLabel{{ $order->order_id }}">Cancelar Orden</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('business.orders.cancel', $order) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <p class="text-gray-600">¿Estás seguro de cancelar la orden <strong>{{ $order->folio_number }}</strong>?</p>

                                        <div class="mb-3">
                                            <label for="cancellation_reason{{ $order->order_id }}" class="form-label">Motivo de cancelación</label>
                                            <textarea
                                                name="cancellation_reason"
                                                id="cancellation_reason{{ $order->order_id }}"
                                                rows="3"
                                                required
                                                class="form-control"
                                                placeholder="Explica el motivo de la cancelación..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-danger">Cancelar Orden</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <svg class="icon icon-xxl text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-600 mb-3">No hay órdenes disponibles</p>
                            <a href="{{ route('business.orders.create') }}" class="btn btn-primary btn-sm">Crear Primera Orden</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
