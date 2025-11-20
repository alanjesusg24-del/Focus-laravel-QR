@extends('layouts.superadmin-app')

@section('title', 'Mi Perfil')

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.dashboard') }}">
                    <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Perfil</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Mi Perfil</h1>
            <p class="mb-0">Información de tu cuenta de Super Administrador</p>
        </div>
        <div>
            <a href="{{ route('superadmin.profile.edit') }}" class="btn btn-primary d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                </svg>
                Editar Perfil
            </a>
        </div>
    </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Profile Info Card -->
<div class="row">
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Información Personal</h2>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center mb-4">
                    <div class="col-auto">
                        <div class="avatar-lg bg-danger rounded-circle d-flex align-items-center justify-content-center text-white">
                            <span class="h2 mb-0">{{ substr($superAdmin->full_name, 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col ps-0">
                        <div class="mb-1">
                            <h3 class="h5 mb-0">{{ $superAdmin->full_name }}</h3>
                            <span class="badge bg-danger">Super Administrador</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <div class="form-control-plaintext">{{ $superAdmin->full_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">Email</label>
                            <div class="form-control-plaintext">{{ $superAdmin->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">Fecha de Registro</label>
                            <div class="form-control-plaintext">{{ $superAdmin->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">Última Actualización</label>
                            <div class="form-control-plaintext">{{ $superAdmin->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Estadísticas Rápidas</h2>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <div class="h6 mb-0">Negocios Activos</div>
                        <div class="small text-gray">Total registrados</div>
                    </div>
                    <div class="h4 mb-0 text-primary">{{ \App\Models\Business::where('is_active', true)->count() }}</div>
                </div>
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <div class="h6 mb-0">Órdenes Hoy</div>
                        <div class="small text-gray">Creadas hoy</div>
                    </div>
                    <div class="h4 mb-0 text-success">{{ \App\Models\Order::whereDate('created_at', today())->count() }}</div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="h6 mb-0">Tickets Abiertos</div>
                        <div class="small text-gray">Requieren atención</div>
                    </div>
                    <div class="h4 mb-0 text-warning">{{ \App\Models\SupportTicket::whereIn('status', ['open', 'in_progress'])->count() }}</div>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="card border-0 shadow">
            <div class="card-body">
                <h5 class="card-title">
                    <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    Seguridad
                </h5>
                <p class="card-text small text-gray-700">
                    Como super administrador, tienes acceso completo al sistema. Asegúrate de mantener tu contraseña segura y actualizada.
                </p>
                <a href="{{ route('superadmin.profile.edit') }}" class="btn btn-sm btn-secondary">Cambiar Contraseña</a>
            </div>
        </div>
    </div>
</div>
@endsection
