@extends('layouts.superadmin-app')

@section('title', 'Gestión de Planes')

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><x-icon name="home" /></a></li>
            <li class="breadcrumb-item active" aria-current="page">Planes</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Gestión de Planes</h1>
            <p class="mb-0">Administra los planes de suscripción disponibles</p>
        </div>
        <div>
            <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <x-icon name="add" class="me-2" />
                Crear Plan
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <x-icon name="success" class="me-2" />
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <x-icon name="error" class="me-2" />
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="fs-5 fw-bold mb-0">Planes Disponibles ({{ $plans->total() }})</h2>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom">ID</th>
                    <th class="border-bottom">Nombre</th>
                    <th class="border-bottom">Descripción</th>
                    <th class="border-bottom">Precio</th>
                    <th class="border-bottom">Duración</th>
                    <th class="border-bottom">Negocios</th>
                    <th class="border-bottom">Estado</th>
                    <th class="border-bottom text-end" style="padding-right: 1.5rem;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                    <tr>
                        <td class="fw-bold">#{{ $plan->plan_id }}</td>
                        <td class="fw-bolder text-gray-500">{{ $plan->name }}</td>
                        <td class="text-gray-900"><small>{{ Str::limit($plan->description ?? 'Sin descripción', 50) }}</small></td>
                        <td class="fw-bold text-success">${{ number_format($plan->price, 2) }}</td>
                        <td class="text-gray-500">{{ $plan->duration_days }} días</td>
                        <td class="fw-bold text-info">
                            {{ $plan->businesses_count ?? 0 }}
                        </td>
                        <td>
                            @if($plan->is_active)
                                <span class="fw-bold text-success">Activo</span>
                            @else
                                <span class="fw-bold text-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end" style="padding-right: 1.5rem;">
                            <div class="dropdown">
                                <button class="btn btn-link text-dark dropdown-toggle m-0 p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                                <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('superadmin.plans.edit', $plan->plan_id) }}">
                                        <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                        </svg>
                                        Editar
                                    </a>
                                    @if($plan->businesses_count == 0)
                                    <div role="separator" class="dropdown-divider my-1"></div>
                                    <a class="dropdown-item d-flex align-items-center text-danger" href="#"
                                       onclick="event.preventDefault(); if(confirm('¿Estás seguro de eliminar este plan?')) { document.getElementById('delete-form-{{ $plan->plan_id }}').submit(); }">
                                        <svg class="dropdown-icon text-danger me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Eliminar
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
                        <td colspan="8" class="text-center py-4">
                            <div class="text-gray-500">
                                <x-icon name="list" class="fs-1 mb-3 d-block" />
                                <p class="mb-0">No hay planes registrados</p>
                                <a href="{{ route('superadmin.plans.create') }}" class="btn btn-sm btn-primary mt-2">
                                    <x-icon name="add" class="me-1" />
                                    Crear Primer Plan
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($plans->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            <nav aria-label="Page navigation">
                {{ $plans->links() }}
            </nav>
            <div class="fw-normal small mt-4 mt-lg-0">
                Mostrando <b>{{ $plans->firstItem() }}</b> a <b>{{ $plans->lastItem() }}</b> de <b>{{ $plans->total() }}</b> registros
            </div>
        </div>
    @endif
</div>
@endsection
