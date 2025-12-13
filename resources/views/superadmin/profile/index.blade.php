{{--
    Company: CETAM
    Project: SuperAdmin System
    File: profile/index.blade.php
    Created on: 11/12/2025
    Description: Vista de perfil del super administrador.
--}}
@extends('layouts.superadmin-app')

@section('title', 'Mi Perfil')

@section('page')
<div class="py-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">

        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('superadmin.dashboard') }}" class="text-primary">
                            <x-icon name="nav.home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Perfil</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Mi Perfil</h2>
            <p class="mb-0 text-primary">Información de tu cuenta de Super Administrador</p>
        </div>

        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('superadmin.profile.edit') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <x-icon name="action.edit" class="me-2"/> Editar Perfil
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <x-icon name="state.success" class="me-2" />
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <x-icon name="state.error" class="me-2" />
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom">
                    <h2 class="fs-5 fw-bold mb-0">Información Personal</h2>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4 pb-4 border-bottom">
                        <div class="col-auto">
                            <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center text-white">
                                <span class="h2 mb-0">{{ substr($superAdmin->full_name, 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="col ps-0">
                            <div class="mb-1">
                                <h3 class="h5 mb-0 text-gray-900">{{ $superAdmin->full_name }}</h3>
                                <span class="fw-bold text-primary">Super Administrador</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-gray-600 small">Nombre Completo</label>
                            <div class="text-gray-900">{{ $superAdmin->full_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-gray-600 small">Email</label>
                            <div class="text-gray-900">{{ $superAdmin->email }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-gray-600 small">Fecha de Registro</label>
                            <div class="text-gray-500">{{ $superAdmin->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-gray-600 small">Última Actualización</label>
                            <div class="text-gray-500">{{ $superAdmin->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
