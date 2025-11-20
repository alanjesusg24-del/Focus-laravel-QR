@extends('layouts.business-app')

@section('title', 'Órdenes - Sistema de Órdenes QR')

@section('page')
<div class="py-4">
    <!-- Mensajes de éxito y error -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    <!-- Encabezado de Página -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Gestión de Órdenes</h2>
            <p class="mb-0">Administra las órdenes de tu negocio</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="me-2">
                <form method="GET" action="{{ route('business.orders.index') }}">
                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="min-width: 180px; width: auto;">
                        <option value="">Todos los estados</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Listos</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregados</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelados</option>
                    </select>
                </form>
            </div>
            <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createOrderModal">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Orden
            </button>
        </div>
    </div>

    <!-- Tabla de Órdenes -->
    <div class="card border-0 shadow mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">Órdenes</h2>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <input type="text" id="search-orders" class="form-control" placeholder="Buscar orden..." style="min-width: 200px;">
                    </div>
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
                        @if(auth()->guard('business')->user()->plan && auth()->guard('business')->user()->plan->has_chat_module)
                        <th class="border-bottom" scope="col">Chat</th>
                        @endif
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
                                @if(!$order->mobile_user_id)
                                    {{-- Mostrar QR solo si no está ligado a celular --}}
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#qrModal{{ $order->order_id }}" title="Ver QR">
                                        <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                    </button>
                                @else
                                    {{-- Mostrar indicador de que está ligado --}}
                                    <span class="badge bg-success" title="Ligado a celular">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                        </svg>
                                        Ligado
                                    </span>
                                @endif
                            @endif
                        </td>
                        @if(auth()->guard('business')->user()->plan && auth()->guard('business')->user()->plan->has_chat_module)
                        <td>
                            @if($order->mobile_user_id)
                                {{-- Solo mostrar botón de chat si el dispositivo está ligado --}}
                                <a href="{{ route('business.chat.index', ['order_id' => $order->order_id]) }}" class="btn btn-sm btn-info" title="Chat con Cliente">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        @endif
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('business.orders.show', $order) }}" class="btn btn-sm btn-primary">Ver</a>

                                @if($order->status === 'pending' && $order->mobile_user_id)
                                    {{-- Solo mostrar "Marcar Listo" si está ligado a celular --}}
                                    <button type="button" class="btn btn-sm btn-success" onclick="event.preventDefault(); this.closest('td').querySelector('#mark-ready-form-{{ $order->order_id }}').submit();">
                                        Marcar Listo
                                    </button>
                                    <form id="mark-ready-form-{{ $order->order_id }}" action="{{ route('business.orders.markAsReady', $order) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('PUT')
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

                    <!-- Modal de Código QR -->
                    @if($order->qr_code_url && !$order->mobile_user_id)
                    <div class="modal fade" id="qrModal{{ $order->order_id }}" tabindex="-1" aria-labelledby="qrModalLabel{{ $order->order_id }}" aria-hidden="true" data-order-id="{{ $order->order_id }}">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title" id="qrModalLabel{{ $order->order_id }}">
                                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                        </svg>
                                        Código QR - {{ $order->folio_number }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body text-center py-4">
                                    <img src="{{ $order->qr_code_url }}" alt="Código QR {{ $order->folio_number }}" class="img-fluid rounded border shadow-sm" style="max-width: 350px;">
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Modal de Cancelación -->
                    <div class="modal fade" id="cancelModal{{ $order->order_id }}" tabindex="-1" aria-labelledby="cancelModalLabel{{ $order->order_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cancelModalLabel{{ $order->order_id }}">Cancelar Orden</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                        <td colspan="{{ (auth()->guard('business')->user()->plan && auth()->guard('business')->user()->plan->has_chat_module) ? '7' : '6' }}" class="text-center py-5">
                            <svg class="icon icon-xxl text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-600 mb-3">No hay órdenes disponibles</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createOrderModal">Crear Primera Orden</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($orders->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            {{ $orders->links('vendor.pagination.volt-custom') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: Crear Nueva Orden -->
<div class="modal fade" id="createOrderModal" tabindex="-1" aria-labelledby="createOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createOrderModalLabel">
                    <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Crear Nueva Orden
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('business.orders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Descripción de la Orden <span class="text-danger">*</span>
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            required
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Ej: 2 cafés americanos, 1 latte grande, 1 bagel...">{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted">Máximo 500 caracteres</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Crear Orden
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Funcionalidad de búsqueda
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-orders');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr:not(:last-child)');

                rows.forEach(row => {
                    // Omitir si es la fila de estado vacío
                    if (row.querySelector('td[colspan]')) {
                        return;
                    }

                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Si hay errores de validación, abrir el modal de crear orden
        @if($errors->any())
        const createOrderModal = new bootstrap.Modal(document.getElementById('createOrderModal'));
        createOrderModal.show();
        @endif

        // Verificar cada 3 segundos si hay modales de QR abiertos y si la orden fue ligada
        setInterval(checkQRModalsForLinkedOrders, 3000);
    });

    function checkQRModalsForLinkedOrders() {
        // Buscar modales de QR que estén abiertos
        const openModals = document.querySelectorAll('.modal.show[id^="qrModal"]');

        openModals.forEach(modal => {
            const orderId = modal.getAttribute('data-order-id');
            if (orderId) {
                // Hacer petición AJAX para verificar si la orden fue ligada
                fetch(`/business/orders/${orderId}/check-linked`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_linked) {
                            // Cerrar el modal
                            const modalInstance = bootstrap.Modal.getInstance(modal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                            // Recargar la página para actualizar la vista
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        }
                    })
                    .catch(error => console.log('Error verificando estado de orden:', error));
            }
        });
    }
</script>
@endsection
