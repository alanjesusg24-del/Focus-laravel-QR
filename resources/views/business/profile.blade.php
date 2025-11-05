@extends('layouts.business-app')

@section('title', 'Mi Perfil')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
                </ol>
            </nav>
            <h2 class="h4">Mi Perfil</h2>
            <p class="mb-0">Administra la información de tu negocio</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.profile.edit') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                </svg>
                Editar Perfil
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Main Profile Info -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Información del Negocio</h5>
                    @if($business->is_active)
                        <span class="badge bg-success">Cuenta Activa</span>
                    @else
                        <span class="badge bg-danger">Cuenta Inactiva</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-gray-600 small mb-1">Nombre del Negocio</label>
                                <p class="h6 mb-0">{{ $business->business_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-gray-600 small mb-1">RFC</label>
                                <p class="h6 mb-0">{{ $business->rfc }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-gray-600 small mb-1">Email</label>
                                <p class="h6 mb-0">{{ $business->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-gray-600 small mb-1">Teléfono</label>
                                <p class="h6 mb-0">{{ $business->phone }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-0">
                                <label class="text-gray-600 small mb-1">Dirección</label>
                                <p class="h6 mb-0">{{ $business->address ?? 'No especificada' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plan Information -->
            <div class="card border-0 shadow">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Información del Plan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm bg-primary text-white rounded me-3">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <label class="text-gray-600 small mb-0 d-block">Plan Actual</label>
                                    <p class="h6 mb-0">{{ $business->plan->name ?? 'Sin plan' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm bg-success text-white rounded me-3">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <label class="text-gray-600 small mb-0 d-block">Precio Mensual</label>
                                    <p class="h6 mb-0">${{ number_format($business->monthly_price ?? 0, 2) }} MXN</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm {{ $business->has_chat_module ? 'bg-info' : 'bg-secondary' }} text-white rounded me-3">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <label class="text-gray-600 small mb-0 d-block">Módulo de Chat</label>
                                    <p class="mb-0">
                                        @if($business->has_chat_module)
                                            <span class="badge bg-success">Activado</span>
                                        @else
                                            <span class="badge bg-secondary">No activado</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm bg-warning text-white rounded me-3">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <label class="text-gray-600 small mb-0 d-block">Retención de Datos</label>
                                    <p class="h6 mb-0">{{ $business->data_retention_months ?? 1 }} {{ $business->data_retention_months == 1 ? 'mes' : 'meses' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-xl-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('business.profile.edit') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                            </svg>
                            Editar Perfil
                        </a>
                        <a href="{{ route('business.profile.change-password') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            Cambiar Contraseña
                        </a>
                        <a href="{{ route('business.payments.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                            </svg>
                            Gestionar Plan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="card border-0 shadow">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Estado de Cuenta</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-gray-600 small mb-1">Estado Actual</p>
                            @if($business->is_active)
                                <h5 class="text-success mb-0">
                                    <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Activa
                                </h5>
                            @else
                                <h5 class="text-danger mb-0">
                                    <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Inactiva
                                </h5>
                            @endif
                        </div>
                        <div class="icon icon-shape icon-lg {{ $business->is_active ? 'bg-success' : 'bg-danger' }} text-white rounded">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    @if(!$business->is_active)
                    <div class="alert alert-warning mt-3 mb-0">
                        <small>Tu cuenta está inactiva. Contacta a soporte o renueva tu plan.</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
