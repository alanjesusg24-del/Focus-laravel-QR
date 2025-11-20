@extends('layouts.superadmin-app')

@section('title', 'Gestión de Planes')

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg></a></li>
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
                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                </svg>
                Crear Plan
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                    <th class="border-bottom">NOMBRE</th>
                    <th class="border-bottom">DESCRIPCIÓN</th>
                    <th class="border-bottom">PRECIO</th>
                    <th class="border-bottom">DURACIÓN</th>
                    <th class="border-bottom">NEGOCIOS</th>
                    <th class="border-bottom">ESTADO</th>
                    <th class="border-bottom">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                    <tr>
                        <td class="fw-bold">#{{ $plan->plan_id }}</td>
                        <td class="fw-bold">{{ $plan->name }}</td>
                        <td><small>{{ Str::limit($plan->description ?? 'Sin descripción', 50) }}</small></td>
                        <td><span class="badge bg-success">${{ number_format($plan->price, 2) }}</span></td>
                        <td>{{ $plan->duration_days }} días</td>
                        <td>
                            <span class="badge bg-info">{{ $plan->businesses_count ?? 0 }}</span>
                        </td>
                        <td>
                            @if($plan->is_active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('superadmin.plans.edit', $plan->plan_id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('superadmin.plans.destroy', $plan->plan_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" {{ $plan->businesses_count > 0 ? 'disabled' : '' }}>
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
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
                                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                                </svg>
                                <p class="mb-0">No hay planes registrados</p>
                                <a href="{{ route('superadmin.plans.create') }}" class="btn btn-sm btn-primary mt-2">Crear Primer Plan</a>
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
