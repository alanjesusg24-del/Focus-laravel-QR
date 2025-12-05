@extends('layouts.superadmin-app')

@section('title', 'Pagos y Suscripciones')

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.dashboard') }}">
                    <x-icon name="home" />
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Pagos y Suscripciones</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Pagos y Suscripciones</h1>
            <p class="mb-0">Administra todos los pagos y suscripciones del sistema</p>
        </div>
    </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <x-icon name="success" class="me-2" />
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters Card -->
<div class="card card-body border-0 shadow mb-4">
    <form method="GET" action="{{ route('superadmin.payments.index') }}" id="filterForm">
        <div class="row align-items-end">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre del negocio...">
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="plan_id" class="form-label">Plan</label>
                <select class="form-select auto-submit" id="plan_id" name="plan_id">
                    <option value="">Todos los planes</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->plan_id }}" {{ request('plan_id') == $plan->plan_id ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select auto-submit" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completado</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Fallido</option>
                </select>
            </div>
        </div>
        @if(request()->hasAny(['search', 'plan_id', 'status']))
            <div class="row mt-3">
                <div class="col-12">
                    <a href="{{ route('superadmin.payments.index') }}" class="btn btn-sm btn-primary">
                        <x-icon name="close" class="me-1" />
                        Limpiar filtros
                    </a>
                </div>
            </div>
        @endif
    </form>
</div>

<!-- Payments Table -->
<div class="card border-0 shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="fs-5 fw-bold mb-0">Lista de Pagos ({{ $payments->total() }})</h2>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom" scope="col">ID</th>
                    <th class="border-bottom" scope="col">Negocio</th>
                    <th class="border-bottom" scope="col">Plan</th>
                    <th class="border-bottom" scope="col">Monto</th>
                    <th class="border-bottom" scope="col">Fecha Pago</th>
                    <th class="border-bottom" scope="col">Próximo Pago</th>
                    <th class="border-bottom" scope="col">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="text-gray-500">#{{ $payment->payment_id }}</td>
                        <td class="fw-bolder text-gray-500">
                            {{ $payment->business->business_name ?? 'N/A' }}
                        </td>
                        <td class="fw-bold text-secondary">
                            {{ $payment->plan->name ?? 'N/A' }}
                        </td>
                        <td class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                        <td class="text-gray-500">
                            {{ $payment->payment_date?->format('d/m/Y') ?? 'N/A' }}
                        </td>
                        <td class="text-gray-500">
                            @if($payment->payment_date)
                                {{ $payment->payment_date->addMonth()->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @switch($payment->status)
                                @case('completed')
                                    <span class="fw-bold text-success">Completado</span>
                                    @break
                                @case('pending')
                                    <span class="fw-bold text-warning">Pendiente</span>
                                    @break
                                @case('failed')
                                    <span class="fw-bold text-danger">Fallido</span>
                                    @break
                                @default
                                    <span class="fw-bold text-secondary">{{ ucfirst($payment->status) }}</span>
                            @endswitch
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-gray-500">
                                <svg class="icon icon-lg mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="mb-0">No se encontraron pagos</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            <nav aria-label="Page navigation">
                {{ $payments->links() }}
            </nav>
            <div class="fw-normal small mt-4 mt-lg-0">
                Mostrando <b>{{ $payments->firstItem() }}</b> a <b>{{ $payments->lastItem() }}</b> de <b>{{ $payments->total() }}</b> registros
            </div>
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

    // Auto-submit para selectores
    document.querySelectorAll('.auto-submit').forEach(function(element) {
        element.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Submit solo al presionar Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterForm.submit();
        }
    });
});
</script>
@endpush
