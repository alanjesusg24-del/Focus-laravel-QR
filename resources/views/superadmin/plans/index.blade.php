{{--
  Company: CETAM
  Project: FQR
  File: index.blade.php
  Created on: 15/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.superadmin-app')

@section('title', 'Gestión de Planes')

@section('page')
<div class="py-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">

        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('superadmin.dashboard') }}" class="text-primary">
                            <x-icon name="nav.home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Planes</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Gestión de Planes</h2>
            <p class="mb-0 text-primary">Administra los planes de suscripción disponibles</p>
        </div>

        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('superadmin.plans.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <x-icon name="action.create" class="me-2"/> Crear Plan
            </a>
        </div>
    </div>

    <div class="table-settings mb-4">
        <div class="row align-items-center justify-content-between">

            <div class="col-12 d-flex align-items-center flex-wrap gap-3">

                {{-- 1. Search --}}
                <div class="input-group" style="max-width: 350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <x-icon name="action.search" class="text-gray-500" />
                    </span>
                    <input type="text"
                           id="search"
                           class="form-control border-start-0 ps-0"
                           placeholder="Buscar por ID, nombre o descripción..."
                           autocomplete="off">
                </div>

                {{-- 2. Filter by Status --}}
                <div class="d-flex align-items-center">
                    <span class="small fw-bold text-gray-600 me-2">Filtrar por estado:</span>
                    <form method="GET" action="{{ route('superadmin.plans.index') }}">
                        <select name="status" onchange="this.form.submit()" class="form-select" style="min-width: 140px;">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <div class="card border-0 shadow mb-4" style="overflow: visible;">
        <div class="card-body" style="overflow: visible; padding: 20px 24px;">
            <div class="table-responsive" style="overflow: visible;">
                <table class="table align-items-center table-flush table-hover">
                    <thead class="thead-light rounded">
                        <tr>
                            <th class="border-bottom" scope="col">ID</th>
                            <th class="border-bottom" scope="col">Nombre</th>
                            <th class="border-bottom" scope="col">Descripción</th>
                            <th class="border-bottom" scope="col">Precio</th>
                            <th class="border-bottom" scope="col">Duración</th>
                            <th class="border-bottom" scope="col">Negocios</th>
                            <th class="border-bottom" scope="col">Estado</th>
                            <th class="border-bottom pe-4" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                        <tr>
                            <td class="text-gray-500">
                                <small>#{{ $plan->plan_id }}</small>
                            </td>
                            <td class="text-gray-900">
                                <span class="fw-bold">{{ $plan->name }}</span>
                            </td>
                            <td class="text-gray-500">
                                <small>{{ Str::limit($plan->description ?? 'Sin descripción', 50) }}</small>
                            </td>
                            <td>
                                <span class="fw-bold text-success">${{ number_format($plan->price, 2) }}</span>
                            </td>
                            <td class="text-gray-500">{{ $plan->duration_days }} días</td>
                            <td>
                                <span class="fw-bold text-info">{{ $plan->businesses_count ?? 0 }}</span>
                            </td>
                            <td>
                                @if($plan->is_active)
                                    <span class="fw-bold text-success">Activo</span>
                                @else
                                    <span class="fw-bold text-danger">Inactivo</span>
                                @endif
                            </td>

                            <td class="position-static ps-3">
                                <div class="dropdown position-static">
                                    <button class="btn btn-link text-dark m-0 p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <x-icon name="nav.menu" class="icon-xs text-dark" />
                                    </button>
                                    <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">

                                        <a class="dropdown-item d-flex align-items-center" href="#"
                                           onclick="event.preventDefault(); confirmEdit({{ $plan->plan_id }}, {{ $plan->businesses_count ?? 0 }}, '{{ $plan->name }}')">
                                            <x-icon name="action.edit" class="text-gray-400 me-2"/> Editar
                                        </a>

                                        @if($plan->businesses_count == 0)
                                            <div role="separator" class="dropdown-divider my-1"></div>

                                            <a class="dropdown-item d-flex align-items-center text-danger" href="#"
                                               onclick="event.preventDefault(); confirmDelete({{ $plan->plan_id }}, '{{ $plan->name }}')">
                                                <x-icon name="action.delete" class="text-danger me-2"/> Eliminar
                                            </a>

                                            <form id="delete-form-{{ $plan->plan_id }}" action="{{ route('superadmin.plans.destroy', $plan->plan_id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center" style="font-size: 3rem;">
                                        <x-icon name="state.info" class="text-gray-300 mb-3"/>

                                        <h6 class="text-gray-500 fw-bold mb-1">No hay planes registrados</h6>
                                        
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            @if($plans->hasPages())
                {{ $plans->links('vendor.pagination.volt-custom') }}
            @else
                {{-- Empty space on the left when there is no pagination --}}
                <div></div>
                {{-- Count message when there is no pagination --}}
                @if($plans->total() > 0)
                    <div class="fw-normal small">
                        Mostrando
                        <span class="fw-bold">{{ $plans->firstItem() }}</span>
                        a
                        <span class="fw-bold">{{ $plans->lastItem() }}</span>
                        de
                        <span class="fw-bold">{{ $plans->total() }}</span>
                        {{ $plans->total() == 1 ? 'entrada' : 'entradas' }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const tbody = document.querySelector('tbody');

    // Real-time client-side search
    if (searchInput && tbody) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const rows = tbody.querySelectorAll('tr');
            let visibleCount = 0;
            let noResultsRow = document.getElementById('no-results-row');

            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) {
                    if (row.id !== 'no-results-row') {
                        row.style.display = 'none';
                    }
                    return;
                }

                // Search in: ID, Plan Name, Description, Price, Duration
                const id = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                const planName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const description = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                const price = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                const duration = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';

                const matches = id.includes(searchTerm) ||
                               planName.includes(searchTerm) ||
                               description.includes(searchTerm) ||
                               price.includes(searchTerm) ||
                               duration.includes(searchTerm);

                row.style.display = matches ? '' : 'none';

                if (matches) visibleCount++;
            });

            if (searchTerm && visibleCount === 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="8" class="text-center py-5">
                            <p class="text-gray-600 mb-0">No se encontraron planes que coincidan con "<strong>${searchTerm}</strong>"</p>
                            <small class="text-muted">Intenta buscar por ID, nombre, descripción, precio o duración</small>
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
});

// Function to confirm edit with sweet alert
function confirmEdit(planId, businessCount, planName) {
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#262B40';

    if (businessCount > 0) {
        Swal.fire({
            icon: 'warning',
            title: '¿Estás seguro?',
            html: `El plan <strong>${planName}</strong> tiene <strong>${businessCount} negocio(s)</strong> asociado(s).<br><br>Los cambios en el precio o características afectarán a los negocios existentes.`,
            showCancelButton: true,
            confirmButtonColor: primaryColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, editar plan',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/superadmin/plans/${planId}/edit`;
            }
        });
    } else {
        // No businesses, redirect directly
        window.location.href = `/superadmin/plans/${planId}/edit`;
    }
}

// Function to confirm delete with sweet alert
function confirmDelete(planId, planName) {
    Swal.fire({
        icon: 'question',
        title: '¿Estás seguro?',
        html: `¿Estás seguro de eliminar el plan <strong>${planName}</strong>?<br><br>Esta acción no se puede deshacer.`,
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + planId).submit();
        }
    });
}
</script>
@endpush
