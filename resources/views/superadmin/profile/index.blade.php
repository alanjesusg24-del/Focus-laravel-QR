{{--
  Company: CETAM
  Project: FQR
  File: index.blade.php
  Created on: 18/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.superadmin-app')

@section('title', 'Mi Perfil')

@section('page')
<div class="py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.dashboard') }}">
                    <x-icon name="nav.home" />
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Mi Perfil</h1>
            <p class="mb-0">Información de tu cuenta de Super Administrador</p>
        </div>
    </div>
</div>

<div class="row">

        <div class="col-12 col-xl-4 order-xl-2">
            <div class="card shadow border-0 text-center mb-3">
                <div class="card-body py-3">

                    <div class="mx-auto mb-4 position-relative avatar-container">
                        @php
                            $fullName = $superAdmin->full_name ?? 'SA';
                            $words = explode(' ', trim($fullName));
                            $initials = '';
                            if (count($words) >= 2) {
                                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                            } else {
                                $initials = strtoupper(substr($fullName, 0, 2));
                            }
                        @endphp
                        <div class="avatar-xl rounded-circle border border-gray-300 shadow w-100 h-100 d-flex align-items-center justify-content-center bg-primary text-white fs-1 fw-bold">
                            {{ $initials }}
                        </div>
                    </div>

                    <h4 class="h3 fw-bold text-primary">{{ $superAdmin->full_name }}</h4>
                    <p class="text-gray-500 mb-3">{{ $superAdmin->email }}</p>
                    <span class="badge bg-danger-soft text-danger">Super Administrador</span>
                </div>
            </div>
        </div>


        <div class="col-12 col-xl-8 order-xl-1">

      
            <div class="card card-body border-0 shadow mb-4">
                <h2 class="h5 mb-4 text-primary fw-bold">Información Personal</h2>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="full_name" class="form-label text-primary fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="full_name"
                               value="{{ $superAdmin->full_name }}"
                               disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label text-primary fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email"
                               class="form-control"
                               id="email"
                               value="{{ $superAdmin->email }}"
                               disabled>
                    </div>
                </div>

    
                <div class="d-flex justify-content-start mt-3">
                    <a href="{{ route('superadmin.profile.edit') }}" class="btn btn-primary d-inline-flex align-items-center">
                        <x-icon name="action.edit" class="me-2" />
                        Editar
                    </a>
                </div>
            </div>
            <div class="card card-body border-0 shadow mb-4">
                <h2 class="h5 mb-4 text-primary fw-bold">Información del Sistema</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-primary fw-bold">Rol</label>
                        <div class="mt-1 fw-bold text-dark">
                            Super Administrador
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-primary fw-bold">ID de Usuario</label>
                        <div class="mt-1 fw-bold text-dark">
                            #{{ $superAdmin->super_admin_id }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-primary fw-bold">Fecha de Registro</label>
                        <div class="mt-1 text-gray-600">
                            {{ $superAdmin->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-primary fw-bold">Última Actualización</label>
                        <div class="mt-1 text-gray-600">
                            {{ $superAdmin->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
