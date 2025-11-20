@extends('layouts.superadmin-app')

@section('title', 'Órdenes Globales')

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.dashboard') }}">
                    <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Órdenes Globales</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Órdenes Globales</h1>
            <p class="mb-0">Visualiza todas las órdenes de todos los negocios</p>
        </div>
    </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters Card -->
<div class="card card-body border-0 shadow mb-4">
    <form method="GET" action="{{ route('superadmin.orders.index') }}" id="filterForm">
        <div class="row align-items-end">
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Folio, cliente...">
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="business_id" class="form-label">Negocio</label>
                <select class="form-select auto-submit" id="business_id" name="business_id">
                    <option value="">Todos los negocios</option>
                    @foreach($businesses as $business)
                        <option value="{{ $business->business_id }}" {{ request('business_id') == $business->business_id ? 'selected' : '' }}>
                            {{ $business->business_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3 mb-md-0">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select auto-submit" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Lista</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregada</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-2 mb-3 mb-md-0">
                <label for="date_from" class="form-label">Fecha desde</label>
                <input type="date" class="form-control auto-submit" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2 mb-3 mb-md-0">
                <label for="date_to" class="form-label">Fecha hasta</label>
                <input type="date" class="form-control auto-submit" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
        </div>
        @if(request()->hasAny(['search', 'business_id', 'status', 'date_from', 'date_to']))
            <div class="row mt-3">
                <div class="col-12">
                    <a href="{{ route('superadmin.orders.index') }}" class="btn btn-sm btn-secondary">
                        <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Limpiar filtros
                    </a>
                </div>
            </div>
        @endif
    </form>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="fs-5 fw-bold mb-0">Lista de Órdenes ({{ $orders->total() }})</h2>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom" scope="col">FOLIO</th>
                    <th class="border-bottom" scope="col">NEGOCIO</th>
                    <th class="border-bottom" scope="col">CLIENTE</th>
                    <th class="border-bottom" scope="col">TOTAL</th>
                    <th class="border-bottom" scope="col">ESTADO</th>
                    <th class="border-bottom" scope="col">FECHA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="fw-bold">{{ $order->folio_number ?? $order->order_number }}</td>
                        <td>
                            @if($order->business)
                                <div class="d-flex align-items-center">
                                    @if($order->business->photo)
                                        <img src="{{ asset('storage/' . $order->business->photo) }}" class="avatar rounded-circle me-2" alt="{{ $order->business->business_name }}">
                                    @else
                                        <div class="avatar rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center">
                                            <span class="text-white fw-bold small">{{ substr($order->business->business_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="fw-bold">{{ $order->business->business_name }}</span>
                                        @if($order->business->plan)
                                            <div class="small text-gray">{{ $order->business->plan->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $order->customer_name }}</div>
                            @if($order->customer_phone)
                                <div class="small text-gray">{{ $order->customer_phone }}</div>
                            @endif
                        </td>
                        <td class="fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @switch($order->status)
                                @case('pending')
                                    <span class="badge bg-warning">Pendiente</span>
                                    @break
                                @case('ready')
                                    <span class="badge bg-info">Lista</span>
                                    @break
                                @case('delivered')
                                    <span class="badge bg-success">Entregada</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger">Cancelada</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            <div>{{ $order->created_at->format('d/m/Y') }}</div>
                            <div class="small text-gray">{{ $order->created_at->format('H:i') }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-gray-500">
                                <svg class="icon icon-lg mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="mb-0">No se encontraron órdenes</p>
                            </div>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('search');
    let searchTimeout;

    // Auto-submit para selectores y fechas
    document.querySelectorAll('.auto-submit').forEach(function(element) {
        element.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Auto-submit para búsqueda con debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filterForm.submit();
        }, 500);
    });

    // Submit inmediato al presionar Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            filterForm.submit();
        }
    });
});
</script>
@endpush
