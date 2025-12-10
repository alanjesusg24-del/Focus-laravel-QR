@extends('layouts.business-app')

@section('title', 'Editar Orden ' . $order->folio_number)

@section('page')
<div class="py-4">
    <div class="row">
        <div class="col-12 col-xl-8 mx-auto">
            <!-- Encabezado de Página -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
                <div class="d-block">
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                            <li class="breadcrumb-item">
                                <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                                    <x-icon name="nav.home" />
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('business.orders.index') }}" class="text-primary">Órdenes</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('business.orders.show', $order) }}" class="text-primary">{{ $order->folio_number }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="h4 mt-1">Editar Orden</h2>
                    <p class="mb-0 text-muted">{{ $order->folio_number }}</p>
                </div>
                <div class="btn-toolbar">
                    <a href="{{ route('business.orders.show', $order) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                        <x-icon name="back" class="me-2"/>
                        Volver a la orden
                    </a>
                </div>
            </div>

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            @endif

            <!-- Tarjeta de información de la orden -->
            <div class="card border-0 shadow mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="h5 mb-2">{{ $order->folio_number }}</h3>
                            <p class="text-gray-600 mb-0">
                                <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                Creada el {{ $order->created_at->format('d/m/Y \a \l\a\s H:i') }}
                            </p>
                        </div>
                        <div class="col-auto">
                            @php
                                $statusClasses = [
                                    'pending' => 'badge-warning',
                                    'ready' => 'badge-success',
                                    'delivered' => 'badge-info',
                                    'cancelled' => 'badge-danger',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pendiente',
                                    'ready' => 'Listo para Recoger',
                                    'delivered' => 'Entregado',
                                    'cancelled' => 'Cancelado',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$order->status] }} fs-6">{{ $statusLabels[$order->status] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de edición -->
            <div class="card border-0 shadow">
                <div class="card-header">
                    <h5 class="mb-0">Editar Orden</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('business.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Folio del Negocio -->
                        <div class="mb-4">
                            <label for="business_folio" class="form-label">
                                Folio del Negocio (opcional)
                            </label>
                            <input
                                type="text"
                                name="business_folio"
                                id="business_folio"
                                class="form-control @error('business_folio') is-invalid @enderror"
                                value="{{ old('business_folio', $order->business_folio) }}"
                                autocomplete="off">

                            @error('business_folio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                Descripción de la Orden (opcional)
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="6"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description', $order->description) }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('business.orders.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"></path>
                                </svg>
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
