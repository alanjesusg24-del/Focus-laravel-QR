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
                                    <x-icon name="edit" />
                                </a>
                                <form action="{{ route('superadmin.plans.destroy', $plan->plan_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" {{ $plan->businesses_count > 0 ? 'disabled' : '' }}>
                                        <x-icon name="delete" />
                                    </button>
                                </form>
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
