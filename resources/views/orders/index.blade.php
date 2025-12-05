{{--
  Company: CETAM
  Project: Focus QR System
  File: orders/index.blade.php
  Description: Listado de órdenes con barra de filtros estandarizada (Manual Figura 68).
--}}
@extends('layouts.business-app')

@section('title', 'Órdenes - Sistema de Órdenes QR')

@section('page')
<div class="py-4">
    
    {{-- ALERTAS DE SISTEMA --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <x-icon name="success" class="me-2"/> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <x-icon name="error" class="me-2"/> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    {{-- ENCABEZADO Y BREADCRUMB --}}
    {{-- ENCABEZADO DE PÁGINA (Título + Botón alineados) --}}
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        
        {{-- IZQUIERDA: Breadcrumb y Títulos --}}
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                            <x-icon name="home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Órdenes</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Gestión de Órdenes</h2>
            <p class="mb-0 text-muted">Administra las órdenes de tu negocio</p>
        </div>

        {{-- DERECHA: Botón Nueva Orden (Movido aquí) --}}
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.orders.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                {{-- Usamos el icono estándar 'add' --}}
                <x-icon name="action.create" class="me-2"/> Nueva Orden
            </a>
        </div>
    </div>

    {{-- 
        BARRA DE HERRAMIENTAS (Solo Buscador y Filtros)
    --}}
    <div class="table-settings mb-4">
        <div class="row align-items-center justify-content-between">
            
            {{-- OCUPA TODO EL ANCHO (col-12) --}}
            <div class="col-12 d-flex align-items-center flex-wrap gap-3">
                
                {{-- 1. Buscador --}}
                <div class="input-group fmxw-300">
                
                    <input type="text" id="search-orders" class="form-control" placeholder="Buscar orden...">
                </div>

                {{-- 2. Filtro --}}
                <div class="d-flex align-items-center">
                    <span class="small fw-bold text-gray-600 me-2">Filtrar por estado:</span>
                    <form method="GET" action="{{ route('business.orders.index') }}">
                        <select name="status" onchange="this.form.submit()" class="form-select" style="min-width: 160px;">
                            <option value="">Todos</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                            <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Listos</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregados</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelados</option>
                        </select>
                    </form>
                </div>
            </div>
            
            {{-- (La columna derecha del botón se eliminó de aquí) --}}

        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-hover">
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
                            <th class="border-bottom text-center pe-4" scope="col">Acciones</th>
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
                                        'pending'   => ['class' => 'text-warning', 'label' => 'Pendiente'],
                                        'ready'     => ['class' => 'text-info',    'label' => 'Listo'],
                                        'delivered' => ['class' => 'text-success', 'label' => 'Entregado'],
                                        'cancelled' => ['class' => 'text-danger',  'label' => 'Cancelado'],
                                    ];
                                    $config = $statusConfig[$order->status] ?? ['class' => 'text-muted', 'label' => 'Desconocido'];
                                @endphp
                                <span class="fw-bold {{ $config['class'] }}">{{ $config['label'] }}</span>
                            </td>

                            <td class="text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($order->qr_code_url)
                                    @if(!$order->mobile_user_id)
                                        <button type="button" class="btn btn-sm p-0" data-bs-toggle="modal" data-bs-target="#qrModal{{ $order->order_id }}" title="Ver QR">
                                            <x-icon name="order.qr" class="text-dark fs-5" />
                                        </button>
                                    @else
                                        <span class="text-success fw-bold">Ligado</span>
                                    @endif
                                @endif
                            </td>
                            @if(auth()->guard('business')->user()->plan && auth()->guard('business')->user()->plan->has_chat_module)
                            <td>
                                @if($order->mobile_user_id)
                                    <a href="{{ route('business.chat.index', ['order_id' => $order->order_id]) }}" class="btn btn-sm btn-info text-white p-1 px-2" title="Chat">
                                        <x-icon name="chat" class="icon-xs"/>
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            @endif
                            
                            {{-- MENÚ DE ACCIONES (Alineado al centro) --}}
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-link text-dark dropdown-toggle m-0 p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
                                        
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('business.orders.show', $order) }}">
                                            <x-icon name="view" class="text-gray-400 me-2"/> Ver detalles
                                        </a>

                                        @if($order->status === 'pending' && $order->mobile_user_id)
                                            <a class="dropdown-item d-flex align-items-center text-success" href="#" onclick="event.preventDefault(); document.getElementById('mark-ready-form-{{ $order->order_id }}').submit();">
                                                <x-icon name="success" class="text-success me-2"/> Marcar Listo
                                            </a>
                                            <form id="mark-ready-form-{{ $order->order_id }}" action="{{ route('business.orders.markAsReady', $order) }}" method="POST" class="d-none">
                                                @csrf @method('PUT')
                                            </form>
                                        @endif

                                        @if(in_array($order->status, ['pending', 'ready']))
                                            <div role="separator" class="dropdown-divider my-1"></div>
                                            <a class="dropdown-item d-flex align-items-center text-danger" href="#" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $order->order_id }}">
                                                <x-icon name="action.cancel" class="text-danger me-2"/> Cancelar Orden
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- MODALES --}}
                        @if($order->qr_code_url && !$order->mobile_user_id)
                        <div class="modal fade" id="qrModal{{ $order->order_id }}" tabindex="-1" aria-hidden="true" data-order-id="{{ $order->order_id }}">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title">Código QR - {{ $order->folio_number }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body text-center py-4">
                                        <img src="{{ $order->qr_code_url }}" alt="QR" class="img-fluid rounded border shadow-sm" style="max-width: 350px;">
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="modal fade" id="cancelModal{{ $order->order_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Cancelar Orden</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <form action="{{ route('business.orders.cancel', $order) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <p class="text-gray-600">¿Estás seguro de cancelar la orden <strong>{{ $order->folio_number }}</strong>?</p>
                                            <div class="mb-3">
                                                <label class="form-label">Motivo de cancelación</label>
                                                <textarea name="cancellation_reason" rows="3" required class="form-control" placeholder="Explica el motivo..."></textarea>
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
                            <td colspan="7" class="text-center py-5">
                                <p class="text-gray-600 mb-3">No hay órdenes disponibles</p>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createOrderModal">Crear Primera Orden</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->total() >= 10 && $orders->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            {{ $orders->links('vendor.pagination.volt-custom') }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="createOrderModal" tabindex="-1" aria-labelledby="createOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createOrderModalLabel">Crear Nueva Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('business.orders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción de la Orden <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="4" required class="form-control @error('description') is-invalid @enderror" placeholder="Ej: 2 cafés americanos...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Orden</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-orders');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr:not(:last-child)');
                rows.forEach(row => {
                    if (row.querySelector('td[colspan]')) return;
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
        @if($errors->any())
        const createOrderModal = new bootstrap.Modal(document.getElementById('createOrderModal'));
        createOrderModal.show();
        @endif
        setInterval(checkQRModalsForLinkedOrders, 3000);
    });

    function checkQRModalsForLinkedOrders() {
        const openModals = document.querySelectorAll('.modal.show[id^="qrModal"]');
        openModals.forEach(modal => {
            const orderId = modal.getAttribute('data-order-id');
            if (orderId) {
                fetch(`/business/orders/${orderId}/check-linked`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_linked) {
                            const modalInstance = bootstrap.Modal.getInstance(modal);
                            if (modalInstance) modalInstance.hide();
                            setTimeout(() => { window.location.reload(); }, 500);
                        }
                    })
                    .catch(error => console.log('Error:', error));
            }
        });
    }
</script>
@endsection