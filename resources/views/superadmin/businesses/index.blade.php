@extends('layouts.superadmin-app')

@section('title', 'Gestión de Negocios')

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
            <li class="breadcrumb-item active" aria-current="page">Negocios</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Gestión de Negocios</h1>
            <p class="mb-0">Administra todos los negocios registrados en el sistema</p>
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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters Card -->
<div class="card card-body border-0 shadow mb-4">
    <form method="GET" action="{{ route('superadmin.businesses.index') }}" id="filterForm">
        <div class="row align-items-end">
            <div class="col-md-5 mb-3 mb-md-0">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nombre, email, RFC...">
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select auto-submit" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
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
        </div>
        @if(request()->hasAny(['search', 'status', 'plan_id']))
            <div class="row mt-3">
                <div class="col-12">
                    <a href="{{ route('superadmin.businesses.index') }}" class="btn btn-sm btn-secondary">
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

<!-- Businesses Table -->
<div class="card border-0 shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="fs-5 fw-bold mb-0">Lista de Negocios ({{ $businesses->total() }})</h2>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom" scope="col">ID</th>
                    <th class="border-bottom" scope="col">NEGOCIO</th>
                    <th class="border-bottom" scope="col">EMAIL</th>
                    <th class="border-bottom" scope="col">TELÉFONO</th>
                    <th class="border-bottom" scope="col">PLAN</th>
                    <th class="border-bottom" scope="col">REGISTRO</th>
                    <th class="border-bottom" scope="col">ESTADO</th>
                    <th class="border-bottom" scope="col">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($businesses as $business)
                    <tr>
                        <td class="fw-bold">#{{ $business->business_id }}</td>
                        <td class="fw-bold">
                            <div class="d-flex align-items-center">
                                @if($business->photo)
                                    <img src="{{ asset('storage/' . $business->photo) }}" class="avatar rounded-circle me-3" alt="{{ $business->business_name }}">
                                @else
                                    <div class="avatar rounded-circle bg-secondary me-3 d-flex align-items-center justify-content-center">
                                        <span class="text-white fw-bold">{{ substr($business->business_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="d-block">
                                    <span class="fw-bold">{{ $business->business_name }}</span>
                                    @if($business->rfc)
                                        <div class="small text-gray">RFC: {{ $business->rfc }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $business->email }}</td>
                        <td>{{ $business->phone ?? 'N/A' }}</td>
                        <td>
                            @if($business->plan)
                                <span class="badge bg-secondary">{{ $business->plan->name }}</span>
                                <div class="small text-gray">${{ number_format($business->plan->price, 2) }}/mes</div>
                            @else
                                <span class="badge bg-warning">Sin plan</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $business->registration_date?->format('d/m/Y') ?? 'N/A' }}</div>
                            <div class="small text-gray">{{ $business->registration_date?->diffForHumans() }}</div>
                        </td>
                        <td>
                            @if($business->is_active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('superadmin.businesses.show', $business->business_id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('superadmin.businesses.edit', $business->business_id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('superadmin.businesses.toggle', $business->business_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $business->is_active ? 'btn-secondary' : 'btn-success' }}" title="{{ $business->is_active ? 'Desactivar' : 'Activar' }}">
                                        @if($business->is_active)
                                            <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-gray-500">
                                <svg class="icon icon-lg mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="mb-0">No se encontraron negocios</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($businesses->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            <nav aria-label="Page navigation">
                {{ $businesses->links() }}
            </nav>
            <div class="fw-normal small mt-4 mt-lg-0">
                Mostrando <b>{{ $businesses->firstItem() }}</b> a <b>{{ $businesses->lastItem() }}</b> de <b>{{ $businesses->total() }}</b> registros
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

    // Auto-submit para selectores (Estado y Plan)
    document.querySelectorAll('.auto-submit').forEach(function(select) {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Auto-submit para búsqueda con debounce (esperar 500ms después de dejar de escribir)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filterForm.submit();
        }, 500);
    });

    // Submit inmediato al presionar Enter en búsqueda
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
