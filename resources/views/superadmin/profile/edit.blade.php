{{--
  Company: CETAM
  Project: FQR
  File: edit.blade.php
  Created on: 18/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.superadmin-app')

@section('title', 'Editar Perfil')

@section('page')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
    <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
            <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('superadmin.dashboard') }}" class="text-primary">
                        <x-icon name="nav.home" />
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('superadmin.profile.index') }}" class="text-primary">Mi Perfil</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h2 class="h4 mt-1">Editar Perfil</h2>
        <p class="mb-0 text-muted">Actualiza tu información personal y contraseña</p>
    </div>
    
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
        <x-icon name="success" class="me-2" />
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <x-icon name="state.error" class="me-2" />
        <div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Edit Profile Form --}}
<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow">
            <div class="card-header border-bottom">
                <h2 class="fs-5 fw-bold mb-0">Información Personal</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('superadmin.profile.update') }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="full_name" class="form-label text-primary fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name', $superAdmin->full_name) }}" required>
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label text-primary fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $superAdmin->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <h3 class="h5 text-primary fw-bold mb-3">Cambiar Contraseña (Opcional)</h3>
                    <p class="text-gray-600 mb-3">Deja estos campos vacíos si no deseas cambiar tu contraseña.</p>

                    <div class="mb-4">
                        <label for="current_password" class="form-label text-primary fw-bold">Contraseña Actual</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="new_password" class="form-label text-primary fw-bold">Nueva Contraseña</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                        <small class="form-text text-muted">
                            Mínimo 8 caracteres, debe incluir mayúsculas, minúsculas y números
                        </small>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label text-primary fw-bold">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="new_password_confirmation" name="new_password_confirmation">
                        @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-start gap-3 pt-3 border-top">
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                            <x-icon name="action.save" class="me-2" />
                            Guardar 
                        </button>
                        <a href="{{ route('superadmin.profile.index') }}" class="btn btn-gray-500 d-inline-flex align-items-center">
                            
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
