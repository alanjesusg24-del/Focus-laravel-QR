@extends('layouts.superadmin-app')

@section('title', 'Tickets de Soporte')

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
            <li class="breadcrumb-item active" aria-current="page">Tickets de Soporte</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Tickets de Soporte</h1>
            <p class="mb-0">Gestiona todos los tickets de soporte del sistema</p>
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
    <form method="GET" action="{{ route('superadmin.tickets.index') }}" id="filterForm">
        <div class="row align-items-end">
            <div class="col-md-4 mb-3 mb-md-0">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Asunto o descripción...">
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
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select auto-submit" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abierto</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resuelto</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Cerrado</option>
                </select>
            </div>
            <div class="col-md-2 mb-3 mb-md-0">
                <label for="priority" class="form-label">Prioridad</label>
                <select class="form-select auto-submit" id="priority" name="priority">
                    <option value="">Todas</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baja</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Media</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>
        </div>
        @if(request()->hasAny(['search', 'business_id', 'status', 'priority']))
            <div class="row mt-3">
                <div class="col-12">
                    <a href="{{ route('superadmin.tickets.index') }}" class="btn btn-sm btn-secondary">
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

<!-- Tickets Table -->
<div class="card border-0 shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="fs-5 fw-bold mb-0">Lista de Tickets ({{ $tickets->total() }})</h2>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom" scope="col">ID</th>
                    <th class="border-bottom" scope="col">NEGOCIO</th>
                    <th class="border-bottom" scope="col">ASUNTO</th>
                    <th class="border-bottom" scope="col">PRIORIDAD</th>
                    <th class="border-bottom" scope="col">ESTADO</th>
                    <th class="border-bottom" scope="col">FECHA</th>
                    <th class="border-bottom" scope="col">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="fw-bold">#{{ $ticket->support_ticket_id }}</td>
                        <td>
                            @if($ticket->business)
                                <div class="d-flex align-items-center">
                                    @if($ticket->business->photo)
                                        <img src="{{ asset('storage/' . $ticket->business->photo) }}" class="avatar rounded-circle me-2" alt="{{ $ticket->business->business_name }}">
                                    @else
                                        <div class="avatar rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center">
                                            <span class="text-white fw-bold small">{{ substr($ticket->business->business_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <span>{{ $ticket->business->business_name }}</span>
                                </div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $ticket->subject }}</div>
                            <div class="small text-gray">{{ Str::limit($ticket->description, 50) }}</div>
                        </td>
                        <td>
                            @switch($ticket->priority)
                                @case('high')
                                    <span class="badge bg-danger">Alta</span>
                                    @break
                                @case('medium')
                                    <span class="badge bg-warning">Media</span>
                                    @break
                                @case('low')
                                    <span class="badge bg-info">Baja</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($ticket->priority) }}</span>
                            @endswitch
                        </td>
                        <td>
                            @switch($ticket->status)
                                @case('open')
                                    <span class="badge bg-primary">Abierto</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge bg-warning">En Progreso</span>
                                    @break
                                @case('resolved')
                                    <span class="badge bg-success">Resuelto</span>
                                    @break
                                @case('closed')
                                    <span class="badge bg-secondary">Cerrado</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            <div>{{ $ticket->created_at->format('d/m/Y') }}</div>
                            <div class="small text-gray">{{ $ticket->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <a href="{{ route('superadmin.tickets.show', $ticket->support_ticket_id) }}" class="btn btn-sm btn-primary">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-gray-500">
                                <svg class="icon icon-lg mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="mb-0">No se encontraron tickets</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            <nav aria-label="Page navigation">
                {{ $tickets->links() }}
            </nav>
            <div class="fw-normal small mt-4 mt-lg-0">
                Mostrando <b>{{ $tickets->firstItem() }}</b> a <b>{{ $tickets->lastItem() }}</b> de <b>{{ $tickets->total() }}</b> registros
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
