{{--
    Company: CETAM
    Project: SuperAdmin System
    File: index.blade.php
    Created on: 11/12/2025
    Description: Vista principal para la gestión y listado de negocios registrados.
--}}
@extends('layouts.superadmin-app')

@section('title', 'Gestión de Negocios')

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
                    <li class="breadcrumb-item active" aria-current="page">Negocios</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Gestión de Negocios</h2>
            <p class="mb-0 text-primary">Administra todos los negocios registrados en el sistema</p>
        </div>
    </div>

    <div class="table-settings mb-4">
        <div class="row align-items-center justify-content-between">

            <div class="col-12 d-flex align-items-center flex-wrap gap-3">

                <div class="input-group" style="max-width: 350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <x-icon name="action.search" class="text-gray-500" />
                    </span>
                    <input type="text"
                           id="search"
                           class="form-control border-start-0 ps-0"
                           placeholder="Buscar por ID, nombre, RFC, email o teléfono..."
                           autocomplete="off">
                </div>

                <div class="d-flex align-items-center">
                    <span class="small fw-bold text-gray-600 me-2">Filtrar por estado</span>
                    <form method="GET" action="{{ route('superadmin.businesses.index') }}">
                        <input type="hidden" name="plan_id" value="{{ request('plan_id') }}">
                        <select name="status" onchange="this.form.submit()" class="form-select" style="min-width: 140px;">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </form>
                </div>

                <div class="d-flex align-items-center">
                    <span class="small fw-bold text-gray-600 me-2">Filtrar por plan:</span>
                    <form method="GET" action="{{ route('superadmin.businesses.index') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <select name="plan_id" onchange="this.form.submit()" class="form-select" style="min-width: 180px;">
                            <option value="">Todos los planes</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->plan_id }}" {{ request('plan_id') == $plan->plan_id ? 'selected' : '' }}>
                                    {{ $plan->name }}
                                </option>
                            @endforeach
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
                            <th class="border-bottom" scope="col">Negocio</th>
                            <th class="border-bottom" scope="col">Email</th>
                            <th class="border-bottom" scope="col">Teléfono</th>
                            <th class="border-bottom" scope="col">Plan</th>
                            <th class="border-bottom" scope="col">Registro</th>
                            <th class="border-bottom" scope="col">Estado</th>
                            <th class="border-bottom text-center pe-4" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($businesses as $business)
                        <tr>
                            <td class="text-gray-500">
                                <small>#{{ $business->business_id }}</small>
                            </td>
                            <td class="text-gray-900">
                                <span class="fw-bold">{{ $business->business_name }}</span>
                                @if($business->rfc)
                                    <div class="small text-gray-500">RFC: {{ $business->rfc }}</div>
                                @endif
                            </td>
                            <td class="text-gray-500">{{ $business->email }}</td>
                            <td class="text-gray-500">{{ $business->phone ?? '-' }}</td>
                            <td>
                                @if($business->plan)
                                    <span class="fw-bold text-secondary">{{ $business->plan->name }}</span>
                                    <div class="small text-gray-500">${{ number_format($business->plan->price, 2) }}/mes</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-gray-500">{{ $business->registration_date?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>
                                @if($business->is_active)
                                    <span class="fw-bold text-success">Activo</span>
                                @else
                                    <span class="fw-bold text-danger">Inactivo</span>
                                @endif
                            </td>

                            <td class="text-center position-static">
                                <div class="dropdown position-static">
                                    <button class="btn btn-link text-dark m-0 p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <x-icon name="nav.menu" class="icon-xs text-dark" />
                                    </button>
                                    <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">

                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('superadmin.businesses.edit', $business->business_id) }}">
                                            <x-icon name="action.edit" class="text-gray-400 me-2"/> Editar
                                        </a>

                                        <div role="separator" class="dropdown-divider my-1"></div>

                                        <a class="dropdown-item d-flex align-items-center {{ $business->is_active ? 'text-danger' : 'text-success' }}" href="#"
                                           onclick="event.preventDefault(); document.getElementById('toggle-form-{{ $business->business_id }}').submit();">

                                            @if($business->is_active)
                                                <x-icon name="state.error" class="text-danger me-2"/> Desactivar
                                            @else
                                                <x-icon name="state.success" class="text-success me-2"/> Activar
                                            @endif
                                        </a>

                                        <form id="toggle-form-{{ $business->business_id }}" action="{{ route('superadmin.businesses.toggle', $business->business_id) }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center" style="font-size: 3rem;">
                                        <x-icon name="state.info" class="text-gray-300 mb-3"/>

                                        <h6 class="text-gray-500 fw-bold mb-1">No se encontraron negocios</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            @if($businesses->hasPages())
                {{ $businesses->links('vendor.pagination.volt-custom') }}
            @else
                {{-- Espacio vacío a la izquierda cuando no hay paginación --}}
                <div></div>
                {{-- Mensaje de conteo cuando no hay paginación --}}
                @if($businesses->total() > 0)
                    <div class="fw-normal small">
                        Mostrando
                        <span class="fw-bold">{{ $businesses->firstItem() }}</span>
                        a
                        <span class="fw-bold">{{ $businesses->lastItem() }}</span>
                        de
                        <span class="fw-bold">{{ $businesses->total() }}</span>
                        {{ $businesses->total() == 1 ? 'entrada' : 'entradas' }}
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

    // Búsqueda en tiempo real del lado del cliente
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

                // Buscar en: ID, Nombre, RFC, Email, Teléfono
                const id = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                const businessNameCell = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const email = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                const phone = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';

                const matches = id.includes(searchTerm) ||
                               businessNameCell.includes(searchTerm) ||
                               email.includes(searchTerm) ||
                               phone.includes(searchTerm);

                row.style.display = matches ? '' : 'none';

                if (matches) visibleCount++;
            });

            if (searchTerm && visibleCount === 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="8" class="text-center py-5">
                            <p class="text-gray-600 mb-0">No se encontraron negocios que coincidan con "<strong>${searchTerm}</strong>"</p>
                            <small class="text-muted">Intenta buscar por ID, nombre, RFC, email o teléfono</small>
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
</script>
@endpush
