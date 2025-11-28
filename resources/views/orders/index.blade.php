@extends('layouts.business-app')

@section('title', 'Órdenes - Sistema de Órdenes QR')

@section('page')
<div class="py-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                    <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item">
                            <a href="#"><x-icon name="home" /></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Órdenes</li>
                    </ol>
                </nav>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

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
                Nueva Orden
            </button>
        </div>
    </div>

    <div class="card border-0 shadow mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">Órdenes</h2>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
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
                        <th class="border-bottom text-end" scope="col" style="padding-right: 1.5rem;">Acciones</th>
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
                                    <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#qrModal{{ $order->order_id }}" title="Ver QR">
                                        <x-icon name="order.qr" class="text-dark" style="font-size: 1rem;" />
                                    </button>
                                @else
                                    <span class="badge bg-success" title="Ligado a celular">Ligado</span>
                                @endif
                            @endif
                        </td>
                        @if(auth()->guard('business')->user()->plan && auth()->guard('business')->user()->plan->has_chat_module)
                        <td>
                            @if($order->mobile_user_id)
                                <a href="{{ route('business.chat.index', ['order_id' => $order->order_id]) }}" class="btn btn-sm btn-info" title="Chat con Cliente">
                                    Chat
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        @endif
                        
                        <td class="text-end" style="padding-right: 1.5rem;">
                            <div class="dropdown">
                                <button class="btn btn-link text-dark dropdown-toggle m-0 p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                                <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
                                    
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('business.orders.show', $order) }}">
                                        <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Ver detalles
                                    </a>

                                    @if($order->status === 'pending' && $order->mobile_user_id)
                                        <a class="dropdown-item d-flex align-items-center text-success" href="#" onclick="event.preventDefault(); document.getElementById('mark-ready-form-{{ $order->order_id }}').submit();">
                                            <svg class="dropdown-icon text-success me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Marcar Listo
                                        </a>
                                        <form id="mark-ready-form-{{ $order->order_id }}" action="{{ route('business.orders.markAsReady', $order) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    @endif

                                    @if(in_array($order->status, ['pending', 'ready']))
                                        <div role="separator" class="dropdown-divider my-1"></div>
                                        <a class="dropdown-item d-flex align-items-center text-danger" href="#" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $order->order_id }}">
                                            <svg class="dropdown-icon text-danger me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Cancelar Orden
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>

                    </tr>

                    @if($order->qr_code_url && !$order->mobile_user_id)
                    <div class="modal fade" id="qrModal{{ $order->order_id }}" tabindex="-1" aria-labelledby="qrModalLabel{{ $order->order_id }}" aria-hidden="true" data-order-id="{{ $order->order_id }}">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title" id="qrModalLabel{{ $order->order_id }}">
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
                                            <textarea name="cancellation_reason" id="cancellation_reason{{ $order->order_id }}" rows="3" required class="form-control" placeholder="Explica el motivo..."></textarea>
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
        @if($orders->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            {{ $orders->links('vendor.pagination.volt-custom') }}
        </div>
        @endif
    </div>
</div>

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