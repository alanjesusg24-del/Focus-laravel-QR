@extends('layouts.base')

@section('title', 'Login - Order QR System')

@section('content')
<style>
    /* CETAM Institutional Colors ya definidos globalmente en cetam-colors.css */
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
                                    <div class="bg-cetam-primary rounded-circle d-flex align-items-center justify-center" style="width: 64px; height: 64px;">
                                        <svg class="text-white" width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h1 class="mb-0 h3 text-cetam-primary">Order QR System</h1>
                                <p class="text-gray">Ingresa a tu cuenta</p>
                            </div>

                            <!-- Success Message -->
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <x-icon name="success" class="me-2" />
                                    <strong>¡Éxito!</strong> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Error Messages -->
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <x-icon name="warning" class="me-2" />
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
                                            <x-icon name="envelope" class="text-gray-600" />
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
                                            <x-icon name="lock" class="text-gray-600" />
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
                                        <a href="#" class="small text-cetam-primary">¿Olvidaste tu contraseña?</a>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-cetam-primary">Iniciar Sesión</button>
                                </div>
                            </form>

                            <!-- Register Link -->
                            <div class="d-flex justify-content-center align-items-center mt-4">
                                <span class="fw-normal text-gray">
                                    ¿No tienes cuenta?
                                    <a href="{{ route('business.register') }}" class="fw-bold text-cetam-primary">
                                        Registra tu negocio
                                    </a>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('layouts.footer2')
@endsection
