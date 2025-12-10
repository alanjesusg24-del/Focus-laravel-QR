@extends('layouts.business-app')

@section('title', 'Crear Orden - Sistema de Órdenes QR')

@section('page')
<div class="py-4">
    <div class="row">
        <div class="col-12 col-xl-8 mx-auto">
            <!-- Encabezado de Página -->
            <div class="mb-4">
                <h2 class="h4">Crear Nueva Orden</h2>
                <p class="mb-0">Completa los datos para generar una orden con código QR</p>
            </div>

            <div class="card border-0 shadow">
                <div class="card-body">
                    <form action="{{ route('business.orders.store') }}" method="POST">
                        @csrf

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
                                value="{{ old('business_folio') }}"
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
                                rows="4"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>

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
                                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Crear Orden
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
