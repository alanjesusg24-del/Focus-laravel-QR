@extends('layouts.business-app')

@section('title', 'Cambiar Contraseña')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}">
                            <x-icon name="nav.home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('business.profile.index') }}">Mi Perfil</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cambiar Contraseña</li>
                </ol>
            </nav>
            <h2 class="h4">Cambiar Contraseña</h2>
            <p class="mb-0">Actualiza tu contraseña de acceso</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <x-icon name="success" class="me-2" />
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <x-icon name="error" class="me-2" />
            <strong>Error</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card border-0 shadow">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Seguridad de la Cuenta</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('business.profile.update-password') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                                    <input type="password"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           id="current_password"
                                           name="current_password"
                                           required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           minlength="8"
                                           maxlength="12"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">La contraseña debe tener entre 8 y 12 caracteres</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           minlength="8"
                                           maxlength="12"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('business.profile.index') }}" class="btn btn-gray-300">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <x-icon name="action.save" class="me-2" />
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
