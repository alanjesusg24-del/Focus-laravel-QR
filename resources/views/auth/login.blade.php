@extends('layouts.base')

@section('title', 'Login - Order QR System')

@section('content')
<style>
    :root {
        --institutional-blue: #1d4976;
        --institutional-orange: #de5629;
        --institutional-gray: #7b96ab;
    }
    .bg-institutional-blue { background-color: var(--institutional-blue) !important; }
    .text-institutional-blue { color: var(--institutional-blue) !important; }
    .btn-institutional-blue {
        background-color: var(--institutional-blue) !important;
        border-color: var(--institutional-blue) !important;
        color: white !important;
    }
    .btn-institutional-blue:hover {
        background-color: #163a5f !important;
        border-color: #163a5f !important;
    }
    .text-institutional-orange { color: var(--institutional-orange) !important; }
    .form-bg-image {
        background: url('/assets/img/illustrations/signin.svg') no-repeat center right;
        background-size: cover;
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
                                    <div class="bg-institutional-blue rounded-circle d-flex align-items-center justify-center" style="width: 64px; height: 64px;">
                                        <svg class="text-white" width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h1 class="mb-0 h3 text-institutional-blue">Order QR System</h1>
                                <p class="text-gray">Ingresa a tu cuenta de negocio</p>
                            </div>

                            <!-- Error Messages -->
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="fas fa-bullhorn me-1"></span>
                                    <strong>Error:</strong> {{ $errors->first() }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Login Form -->
                            <form method="POST" action="{{ route('business.login') }}" class="mt-4">
                                @csrf

                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="form-label">Correo Electrónico</label>
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
                                            placeholder="tu-negocio@ejemplo.com"
                                            required
                                            autofocus
                                        >
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-4">
                                    <label for="password" class="form-label">Contraseña</label>
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
                                            placeholder="••••••••"
                                            required
                                        >
                                    </div>
                                </div>

                                <!-- Remember Me & Forgot Password -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Recordarme
                                        </label>
                                    </div>
                                    <div>
                                        <a href="#" class="small text-institutional-blue">¿Olvidaste tu contraseña?</a>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-institutional-blue">Iniciar Sesión</button>
                                </div>
                            </form>

                            <!-- Register Link -->
                            <div class="d-flex justify-content-center align-items-center mt-4">
                                <span class="fw-normal text-gray">
                                    ¿No tienes cuenta?
                                    <a href="{{ route('business.register') }}" class="fw-bold text-institutional-blue">
                                        Registra tu negocio
                                    </a>
                                </span>
                            </div>

                            <!-- Credenciales de Prueba -->
                            <div class="alert alert-info mt-4 mb-0" role="alert">
                                <div class="d-flex align-items-start">
                                    <svg class="icon icon-xs me-2 mt-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="small">
                                        <strong>🔐 Credenciales de Prueba:</strong><br>
                                        <strong>Email:</strong> contacto@cafeteria.com<br>
                                        <strong>Password:</strong> password
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
