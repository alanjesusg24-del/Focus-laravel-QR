@extends('layouts.superadmin-app')

@section('title', 'Pagos y Suscripciones')

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
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters Card -->
<div class="card card-body border-0 shadow mb-4">
    <form method="GET" action="{{ route('superadmin.payments.index') }}" id="filterForm">
        <div class="row align-items-end">
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="ID Stripe...">
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
            <div class="col-md-2 mb-3 mb-md-0">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select auto-submit" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completado</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Fallido</option>
                </select>
            </div>
            <div class="col-md-1 mb-3 mb-md-0">
                <label for="date_from" class="form-label">Desde</label>
                <input type="date" class="form-control auto-submit" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-1 mb-3 mb-md-0">
                <label for="date_to" class="form-label">Hasta</label>
                <input type="date" class="form-control auto-submit" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
        </div>
        @if(request()->hasAny(['search', 'business_id', 'plan_id', 'status', 'date_from', 'date_to']))
            <div class="row mt-3">
                <div class="col-12">
                    <a href="{{ route('superadmin.payments.index') }}" class="btn btn-sm btn-secondary">
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
                    <th class="border-bottom" scope="col">NEGOCIO</th>
                    <th class="border-bottom" scope="col">PLAN</th>
                    <th class="border-bottom" scope="col">MONTO</th>
                    <th class="border-bottom" scope="col">FECHA PAGO</th>
                    <th class="border-bottom" scope="col">PRÓXIMO PAGO</th>
                    <th class="border-bottom" scope="col">ESTADO</th>
                    <th class="border-bottom" scope="col">MÉTODO</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="fw-bold">#{{ $payment->payment_id }}</td>
                        <td>
                            @if($payment->business)
                                <div class="d-flex align-items-center">
                                    @if($payment->business->photo)
                                        <img src="{{ asset('storage/' . $payment->business->photo) }}" class="avatar rounded-circle me-2" alt="{{ $payment->business->business_name }}">
                                    @else
                                        <div class="avatar rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center">
                                            <span class="text-white fw-bold small">{{ substr($payment->business->business_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <span>{{ $payment->business->business_name }}</span>
                                </div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->plan)
                                <span class="badge bg-secondary">{{ $payment->plan->name }}</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <div>{{ $payment->payment_date?->format('d/m/Y') ?? 'N/A' }}</div>
                            @if($payment->payment_date)
                                <div class="small text-gray">{{ $payment->payment_date->format('H:i') }}</div>
                            @endif
                        </td>
                        <td>
                            @if($payment->next_payment_date)
                                <div>{{ $payment->next_payment_date->format('d/m/Y') }}</div>
                                <div class="small text-gray">{{ $payment->next_payment_date->diffForHumans() }}</div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @switch($payment->status)
                                @case('completed')
                                    <span class="badge bg-success">Completado</span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-warning">Pendiente</span>
                                    @break
                                @case('failed')
                                    <span class="badge bg-danger">Fallido</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if($payment->stripe_payment_id)
                                <div class="small">
                                    <svg class="icon icon-xxs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Stripe
                                </div>
                                <div class="small text-gray">{{ substr($payment->stripe_payment_id, 0, 20) }}...</div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
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
