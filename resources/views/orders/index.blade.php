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
        <x-icon name="state.success" class="me-2"/> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <x-icon name="state.error" class="me-2"/> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                            <x-icon name="nav.home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Órdenes</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Gestión de Órdenes</h2>
            <p class="mb-0 text-primary">Administra las órdenes de tu negocio</p>
        </div>

        {{-- DERECHA: Botón Nueva Orden (Movido aquí) --}}
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.orders.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
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
                <div class="input-group" style="max-width: 350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <x-icon name="action.search" class="text-gray-500" />
                    </span>
                    <input type="text"
                           id="search-orders"
                           class="form-control border-start-0 ps-0"
                           placeholder="Buscar por folio o descripción..."
                           autocomplete="off">
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
    <div class="card border-0 shadow mb-4" style="overflow: visible;">
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible;">
                <table class="table align-items-center table-flush table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-bottom" scope="col">ID</th>
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
                            <td class="text-gray-500">
                                <small>{{ $order->folio_number }}</small>
                            </td>
                            <td>
                                @if($order->business_folio)
                                    <span class="fw-bold text-primary">{{ $order->business_folio }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
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
                                            <x-icon name="action.scan" class="text-dark fs-5" />
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
                            
    
                            <td class="text-center position-static">
                                <div class="dropdown position-static">
                                    <button class="btn btn-link text-dark m-0 p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <x-icon name="action.more" class="icon-xs text-dark" />
                                    </button>
                                    <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">

                                        @if($order->status === 'ready')
                                            {{-- Solo mostrar Entregar Orden cuando esté lista --}}
                                            <a class="dropdown-item d-flex align-items-center text-primary" href="#" data-bs-toggle="modal" data-bs-target="#deliverModal{{ $order->order_id }}">
                                                <x-icon name="action.edit" class="text-primary me-2"/> Entregar Orden
                                            </a>
                                        @else
                                            {{-- Editar solo si está pendiente y NO ligada --}}
                                            @if($order->status === 'pending' && !$order->mobile_user_id)
                                                <a class="dropdown-item d-flex align-items-center" href="{{ route('business.orders.edit', $order) }}">
                                                    <x-icon name="edit" class="text-gray-400 me-2"/> Editar Orden
                                                </a>
                                            @endif

                                            {{-- Ver detalles solo para órdenes entregadas o canceladas --}}
                                            @if(in_array($order->status, ['delivered', 'cancelled']))
                                                <a class="dropdown-item d-flex align-items-center" href="{{ route('business.orders.show', $order) }}">
                                                    <x-icon name="action.view" class="text-gray-400 me-2"/> Ver detalles
                                                </a>
                                            @endif

                                            {{-- Marcar como Listo si está pendiente y ligada --}}
                                            @if($order->status === 'pending' && $order->mobile_user_id)
                                                <a class="dropdown-item d-flex align-items-center text-success" href="#" onclick="event.preventDefault(); document.getElementById('mark-ready-form-{{ $order->order_id }}').submit();">
                                                    <x-icon name="state.success" class="text-success me-2"/> Marcar Listo
                                                </a>
                                                <form id="mark-ready-form-{{ $order->order_id }}" action="{{ route('business.orders.markAsReady', $order) }}" method="POST" class="d-none">
                                                    @csrf @method('PUT')
                                                </form>
                                            @endif

                                            {{-- Cancelar solo si está pendiente (ligada o no) --}}
                                            @if($order->status === 'pending')
                                                <div role="separator" class="dropdown-divider my-1"></div>
                                                <a class="dropdown-item d-flex align-items-center text-danger" href="#" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $order->order_id }}">
                                                     Cancelar Orden
                                                </a>
                                            @endif
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
                                        <h5 class="modal-title">
                                            Código QR - {{ $order->folio_number }}@if($order->business_folio) - {{ $order->business_folio }}@endif
                                        </h5>
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

                        {{-- Modal Entregar Orden --}}
                        @if($order->status === 'ready')
                        <div class="modal fade" id="deliverModal{{ $order->order_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0 bg-light">
                                        <h5 class="modal-title">
                                            <x-icon name="action.send" class="text-primary me-2"/>
                                            Entregar Orden
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <form action="{{ route('business.orders.markAsDelivered', $order) }}" method="POST">
                                        @csrf @method('PUT')

                                        <div class="modal-body">
                                            {{-- Info de la orden --}}
                                            <div class="alert alert-light border d-flex align-items-center mb-3">
                                                <x-icon name="nav.home" class="me-2 text-muted"/>
                                                <div>
                                                    @if($order->business_folio)
                                                        <strong>Folio: {{ $order->business_folio }}</strong><br>
                                                        <small class="text-muted">Sistema: {{ $order->folio_number }}</small>
                                                    @else
                                                        <strong>Orden: {{ $order->folio_number }}</strong>
                                                    @endif
                                                    @if($order->description)
                                                        <br><small class="text-muted">{{ Str::limit($order->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>

                                            <p class="text-muted mb-0">¿Confirmas que deseas marcar esta orden como entregada?</p>
                                        </div>

                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">
                                                <x-icon name="state.success" class="me-2"/>
                                                Confirmar Entrega
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

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
        @if($orders->hasPages())
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
                        <label for="business_folio" class="form-label">
                            Folio del Negocio (opcional)
                        </label>
                        <input type="text"
                               name="business_folio"
                               id="business_folio"
                               class="form-control @error('business_folio') is-invalid @enderror"
                               value="{{ old('business_folio') }}"
                               autocomplete="off">
                        @error('business_folio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Descripción de la Orden (opcional)
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <x-icon name="action.create" class="me-2"/>
                        Crear Orden
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-orders');
        const tbody = document.querySelector('tbody');

        if (searchInput) {
            // Búsqueda mejorada
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('tbody tr');
                let visibleCount = 0;
                let noResultsRow = document.getElementById('no-results-row');

                rows.forEach(row => {
                    // Ignorar filas vacías o de "no hay órdenes"
                    if (row.querySelector('td[colspan]')) {
                        if (row.id !== 'no-results-row') {
                            row.style.display = 'none';
                        }
                        return;
                    }

                    // Buscar en ID, Folio y Descripción
                    const idCell = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                    const folioCell = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    const description = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';

                    // Busca en todas las columnas relevantes
                    const matches = idCell.includes(searchTerm) || folioCell.includes(searchTerm) || description.includes(searchTerm);
                    row.style.display = matches ? '' : 'none';

                    if (matches) visibleCount++;
                });

                // Mostrar mensaje de "sin resultados" si no hay coincidencias
                if (searchTerm && visibleCount === 0) {
                    if (!noResultsRow) {
                        noResultsRow = document.createElement('tr');
                        noResultsRow.id = 'no-results-row';
                        noResultsRow.innerHTML = `
                            <td colspan="7" class="text-center py-5">
                                <p class="text-gray-600 mb-0">No se encontraron órdenes que coincidan con "<strong>${searchTerm}</strong>"</p>
                                <small class="text-muted">Intenta buscar por otro folio o descripción</small>
                            </td>
                        `;
                        tbody.appendChild(noResultsRow);
                    } else {
                        noResultsRow.querySelector('strong').textContent = searchTerm;
                        noResultsRow.style.display = '';
                    }
                } else if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
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