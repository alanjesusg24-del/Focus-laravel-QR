{{--
  Company: CETAM
  Project: FQR
  File: index.blade.php
  Created on: 03/12/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno

  Changelog:
  - ID: 1 | Date: 04/12/2025
    Modified by: Dafne Vanessa Castillo Moreno
    Description: Standardization of ticket view (same design as Orders), search engine and filter corrections.
--}}
@extends('layouts.business-app')

@section('title', 'Tickets de Soporte - Order QR System')

@section('page')
<div class="py-4">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <x-icon name="success" class="me-2"/> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    {{-- Error Alert --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <x-icon name="error" class="me-2"/> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                            <x-icon name="home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Soporte</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Tickets de Soporte</h2>
            <p class="mb-0 text-muted">Administre sus solicitudes de ayuda y soporte técnico</p>
        </div>
        
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.support.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <x-icon name="action.create" class="icon-xs me-2"/> Nuevo Ticket
            </a>
        </div>
    </div>

    {{-- Table Settings: Search and Filter --}}
    <div class="table-settings mb-4">
        <div class="row align-items-center justify-content-between">
            
            <div class="col-12 d-flex align-items-center flex-wrap gap-3">
                
                <div class="fmxw-300">
                    <input type="text" id="search-tickets" class="form-control" placeholder="Buscar ticket...">
                </div>

                <div class="d-flex align-items-center">
                    <span class="small fw-bold text-gray-600 me-2">Filtrar por estado:</span>
                    <form method="GET" action="{{ route('business.support.index') }}">
                        <select name="status" id="status" class="form-select" onchange="this.form.submit()" style="min-width: 160px;">
                            <option value="">Todos los estados</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abierto</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tickets Table --}}
    <div class="card border-0 shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-bottom" scope="col">#</th>
                            <th class="border-bottom" scope="col">Asunto</th>
                            <th class="border-bottom" scope="col">Estado</th>
                            <th class="border-bottom" scope="col">Respuesta</th>
                            <th class="border-bottom" scope="col">Creado</th>
                            <th class="border-bottom text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr class="ticket-row">
                            
                            {{-- 1. Sequential ID --}}
                            <td class="fw-bolder text-gray-500">
                                {{ ($tickets->currentPage() - 1) * $tickets->perPage() + $loop->iteration }}
                            </td>

                            <td class="text-gray-900 fw-bold">
                                <a href="{{ route('business.support.show', $ticket->support_ticket_id) }}" class="text-primary text-decoration-none">
                                    {{ Str::limit($ticket->subject, 50) }}
                                </a>
                            </td>
                            
                            {{-- 2. Status (Colored Text) --}}
                            <td>
                                @php
                                    $statusClasses = [
                                        'open'        => 'text-info',
                                        'in_progress' => 'text-warning',
                                        'closed'      => 'text-gray-500',
                                    ];
                                    $statusLabels = [
                                        'open'        => 'Abierto',
                                        'in_progress' => 'En Progreso',
                                        'closed'      => 'Cerrado',
                                    ];
                                    $class = $statusClasses[$ticket->status] ?? 'text-muted';
                                    $label = $statusLabels[$ticket->status] ?? ucfirst($ticket->status);
                                @endphp
                                <span class="fw-bold {{ $class }}">{{ $label }}</span>
                            </td>

                            {{-- 3. Response --}}
                            <td>
                                @if($ticket->response)
                                    <div class="d-flex align-items-center">
                                        <x-icon name="success" class="text-success me-2 icon-xs" />
                                        <span class="text-gray-700 small">Respondido</span>
                                    </div>
                                    @if($ticket->responded_at)
                                        <small class="text-muted d-block ps-4">{{ $ticket->responded_at->format('d/m/Y') }}</small>
                                    @endif
                                @else
                                    <div class="d-flex align-items-center">
                                        <span class="text-gray-500 small">Pendiente</span>
                                    </div>
                                @endif
                            </td>

                            <td class="text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                            
                            {{-- 4. Actions (Centered 3 dots) --}}
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-link text-dark dropdown-toggle m-0 p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('business.support.show', $ticket->support_ticket_id) }}">
                                            <x-icon name="view" class="text-gray-400 me-2"/> Ver detalles
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <p class="text-gray-600 mb-3">No tienes tickets de soporte registrados.</p>
                                <a href="{{ route('business.support.create') }}" class="btn btn-primary btn-sm">
                                    Crear Primer Ticket
                                </a>
                            </td>
                        </tr>
                        @endforelse
                        
                        {{-- Hidden row for no search results --}}
                        <tr id="no-results-row" style="display: none;">
                            <td colspan="6" class="text-center py-4 text-muted">
                                No se encontraron tickets con ese criterio.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($tickets->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            {{ $tickets->links('vendor.pagination.volt-custom') }}
        </div>
        @endif
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-tickets');
        
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('.ticket-row');
                const noResultsRow = document.getElementById('no-results-row');
                let hasVisibleRows = false;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        hasVisibleRows = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (noResultsRow) {
                    noResultsRow.style.display = hasVisibleRows ? 'none' : '';
                }
            });
        }
    });
</script>
@endsection