{{--
    Company: CETAM
    Project: FQR
    File: edit.blade.php
    Created on: 25/11/2025
    Created by: Dafne Vanessa Castillo Moreno
    Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.business-app')

@section('title', 'Editar Orden ' . $order->folio_number)

@section('page')
<div class="py-4">
    <div class="row">
        <div class="col-12">
            
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
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="h4 mt-1">Editar Orden</h2>
                    <p class="mb-0 text-muted">{{ $order->folio_number }}</p>
                </div>
            </div>

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            @endif

            <div class="card border-0 shadow mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="h5 mb-2">{{ $order->folio_number }}</h3>
                            <p class="text-gray-600 mb-0 d-flex align-items-center">
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

            <div class="card border-0 shadow">
                <div class="card-body">
                    <form action="{{ route('business.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="business_folio" class="form-label">
                                Folio del Negocio <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="business_folio"
                                id="business_folio"
                                class="form-control @error('business_folio') is-invalid @enderror"
                                value="{{ old('business_folio', $order->business_folio) }}"
                                autocomplete="off">

                            @error('business_folio')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">
                                Descripción de la Orden (opcional)
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="6"
                                class="form-control">{{ old('description', $order->description) }}</textarea>

                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-start gap-3 pt-3 border-top">

                            <button type="submit" class="btn btn-primary">
                                <x-icon name="action.save" class="w-4 h-4 me-2" />
                                Guardar
                            </button>

                            <a href="{{ route('business.orders.index') }}" class="btn btn-gray-300">
                                Cancelar
                            </a>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection