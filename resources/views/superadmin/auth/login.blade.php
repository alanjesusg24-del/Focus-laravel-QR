@extends('layouts.base')

@section('title', 'Super Admin Login - Order QR System')

@section('content')
<style>
    :root {
        --institutional-blue: #1d4976;
        --institutional-orange: #de5629;
        --institutional-gray: #7b96ab;
        --superadmin-purple: #6f42c1;
        --superadmin-dark: #2c2c54;
    }
    .bg-superadmin-purple { background-color: var(--superadmin-purple) !important; }
    .text-superadmin-purple { color: var(--superadmin-purple) !important; }
    .btn-superadmin-purple {
        background-color: var(--superadmin-purple) !important;
        border-color: var(--superadmin-purple) !important;
        color: white !important;
    }
    .btn-superadmin-purple:hover {
        background-color: #5a32a3 !important;
        border-color: #5a32a3 !important;
    }
    .form-bg-image {
        background: url('/assets/img/illustrations/signin.svg') no-repeat center right;
        background-size: contain;
    }
    @media (max-width: 992px) {
        .form-bg-image {
            background: none;
        }
    }
</style>

<body>
    <main>
        <section class="vh-lg-100 mt-4 mt-lg-0 bg-soft d-flex align-items-center">
            <div class="container">
                <div class="row justify-content-center form-bg-image">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="signin-inner my-3 my-lg-0 bg-white shadow-soft border rounded border-gray-300 p-4 p-lg-5 w-100 fmxw-500">
                            <!-- Logo y Header -->
                            <div class="text-center text-md-center mb-4 mt-md-0">
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="bg-superadmin-purple rounded-circle d-flex align-items-center justify-center" style="width: 64px; height: 64px;">
                                        <svg class="text-white" width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h1 class="mb-0 h3 text-superadmin-purple">Super Admin Panel</h1>
                                <p class="text-gray">Acceso restringido - Solo administradores</p>
                            </div>

                            <!-- Success Message -->
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <strong>Exito!</strong> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Error Messages -->
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="fas fa-bullhorn me-1"></span>
                                    <strong>Error:</strong> {{ $errors->first() }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Login Form -->
                            <form method="POST" action="{{ route('superadmin.login') }}" class="mt-4">
                                @csrf

                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="form-label">Correo Electronico</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                            </svg>
                                        </span>
                                        <input
                                            type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            placeholder="admin@example.com"
                                            required
                                            autofocus
                                        >
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-4">
                                    <label for="password" class="form-label">Contrasena</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon2">
                                            <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                        <input
                                            type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            id="password"
                                            name="password"
                                            placeholder=""""""""""
                                            required
                                        >
                                    </div>
                                </div>

                                <!-- Remember Me -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Recordarme
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-superadmin-purple">Iniciar Sesion</button>
                                </div>
                            </form>

                            <!-- Credenciales de Prueba -->
                            <div class="alert alert-warning mt-4 mb-0" role="alert">
                                <div class="d-flex align-items-start">
                                    <svg class="icon icon-xs me-2 mt-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="small">
                                        <strong>Acceso Restringido</strong><br>
                                        Solo personal autorizado puede acceder a este panel.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('layouts.footer2')
@endsection
