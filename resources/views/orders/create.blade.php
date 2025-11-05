@extends('layouts.business-app')

@section('title', 'Crear Orden - Order QR System')

@section('page')
<div class="py-4">
    <div class="row">
        <div class="col-12 col-xl-8 mx-auto">
            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="h4">Crear Nueva Orden</h2>
                <p class="mb-0">Completa los datos para generar una orden con código QR</p>
            </div>

            <div class="card border-0 shadow">
                <div class="card-body">
                    <form action="{{ route('business.orders.store') }}" method="POST">
                        @csrf

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                Descripción de la Orden <span class="text-danger">*</span>
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="4"
                                required
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Ej: 2 cafés americanos, 1 latte grande, 1 bagel...">{{ old('description') }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="form-text text-muted">Máximo 500 caracteres</small>
                        </div>

                        <!-- ID de usuario móvil (opcional) -->
                        <div class="mb-4">
                            <label for="mobile_user_id" class="form-label">
                                ID de Usuario Móvil <span class="text-muted">(Opcional)</span>
                            </label>
                            <input
                                type="number"
                                name="mobile_user_id"
                                id="mobile_user_id"
                                value="{{ old('mobile_user_id') }}"
                                class="form-control @error('mobile_user_id') is-invalid @enderror"
                                placeholder="Deja en blanco si el usuario escaneará el QR después">

                            @error('mobile_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="form-text text-muted">Si conoces el ID del usuario, la orden se vinculará automáticamente</small>
                        </div>

                        <!-- Info adicional -->
                        <div class="alert alert-info d-flex align-items-start" role="alert">
                            <svg class="icon icon-sm me-3 mt-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h6 class="alert-heading mb-2">Sobre el código QR:</h6>
                                <ul class="mb-0 small">
                                    <li>Se generará automáticamente al crear la orden</li>
                                    <li>El cliente podrá escanearlo para vincular la orden a su app</li>
                                    <li>Se generará un token de recogida único de 16 caracteres</li>
                                </ul>
                            </div>
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
