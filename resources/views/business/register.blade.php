@extends('layouts.base')

@section('title', 'Registrar Negocio - Order QR System')

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
    .plan-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid #dee2e6;
    }
    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
    }
    .plan-card.selected {
        border: 2px solid var(--institutional-blue) !important;
        background-color: #e3f2fd;
    }
    .plan-card input[type="radio"] {
        display: none;
    }
</style>

<body>
    <main>
        <section class="min-vh-100 py-5 bg-soft d-flex align-items-center">
            <div class="container">
                <p class="text-center">
                    <a href="{{ route('business.login') }}" class="d-flex align-items-center justify-content-center">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Volver al inicio de sesion
                    </a>
                </p>
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 my-4">
                            <!-- Header -->
                            <div class="text-center text-md-center mb-4 mt-md-0">
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="bg-institutional-blue rounded-circle d-flex align-items-center justify-center" style="width: 64px; height: 64px;">
                                        <svg class="text-white" width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h1 class="mb-0 h3 text-institutional-blue">Registrar Negocio</h1>
                                <p class="text-gray">Unete al sistema Order QR</p>
                            </div>

                            <!-- Error Messages -->
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="fas fa-bullhorn me-1"></span>
                                    <strong>Error:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Registration Form -->
                            <form method="POST" action="{{ route('business.register') }}" class="mt-4" id="registerForm">
                                @csrf

                                <!-- Business Information -->
                                <div class="mb-4">
                                    <h5 class="text-institutional-blue mb-3">Informacion del Negocio</h5>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="business_name" class="form-label">Nombre del Negocio <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Correo Electronico <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Telefono <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rfc" class="form-label">RFC <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control text-uppercase" id="rfc" name="rfc" value="{{ old('rfc') }}" minlength="12" maxlength="13" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label">Contrasena <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                                            <small class="text-muted">Minimo 8 caracteres</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirmation" class="form-label">Confirmar Contrasena <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plan Selection -->
                                <div class="mb-4">
                                    <h5 class="text-institutional-blue mb-3">Selecciona tu Plan</h5>
                                    <p class="text-muted small mb-4">Elige el plan que mejor se adapte a las necesidades de tu negocio. Los planes son configurados por el administrador.</p>

                                    @if($plans->count() > 0)
                                    <div class="row">
                                        @foreach($plans as $plan)
                                        <div class="col-md-4 mb-3">
                                            <label class="plan-card card h-100 mb-0" onclick="selectPlan({{ $plan->plan_id }})">
                                                <input type="radio" name="plan_id" value="{{ $plan->plan_id }}" {{ old('plan_id') == $plan->plan_id ? 'checked' : '' }} required>
                                                <div class="card-body">
                                                    <div class="text-center mb-3">
                                                        <h5 class="text-institutional-blue mb-1">{{ $plan->name }}</h5>
                                                        @if($plan->description)
                                                        <small class="text-muted">{{ $plan->description }}</small>
                                                        @endif
                                                    </div>

                                                    <div class="text-center mb-3">
                                                        <h3 class="text-institutional-blue mb-0">
                                                            ${{ number_format($plan->price, 2) }}
                                                        </h3>
                                                        <small class="text-muted">por {{ $plan->duration_days }} dias</small>
                                                    </div>

                                                    <ul class="list-unstyled small">
                                                        <li class="mb-2">
                                                            <svg class="icon icon-xs {{ $plan->has_chat_module ? 'text-success' : 'text-muted' }} me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Modulo de chat
                                                            @if(!$plan->has_chat_module)
                                                                <span class="badge bg-secondary ms-1">No incluido</span>
                                                            @endif
                                                        </li>
                                                        <li class="mb-2">
                                                            <svg class="icon icon-xs text-success me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            <strong>{{ $plan->retention_days }}</strong>
                                                            {{ $plan->retention_days == 1 ? 'dia' : 'dias' }} de retencion
                                                        </li>
                                                        @if($plan->has_realerts)
                                                        <li class="mb-2">
                                                            <svg class="icon icon-xs text-success me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Sistema de re-alertas
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="alert alert-warning">
                                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        No hay planes disponibles en este momento. Por favor contacta al administrador.
                                    </div>
                                    @endif
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required {{ old('terms') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="terms">
                                            Acepto los <a href="#" class="text-institutional-blue">terminos y condiciones</a> <span class="text-danger">*</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-institutional-blue btn-lg">
                                        <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Registrar Negocio
                                    </button>
                                </div>

                                <div class="d-flex justify-content-center align-items-center mt-4">
                                    <span class="fw-normal text-gray">
                                        ¿Ya tienes cuenta?
                                        <a href="{{ route('business.login') }}" class="fw-bold text-institutional-blue">
                                            Inicia sesion aqui
                                        </a>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function selectPlan(planId) {
            // Remove selected class from all cards
            document.querySelectorAll('.plan-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');

            // Check the radio button
            const radio = event.currentTarget.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        }

        // On page load, select the checked plan if any
        document.addEventListener('DOMContentLoaded', function() {
            const checkedRadio = document.querySelector('input[name="plan_id"]:checked');
            if (checkedRadio) {
                checkedRadio.closest('.plan-card').classList.add('selected');
            }

            // Password confirmation validation
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');

            passwordConfirmation.addEventListener('input', function() {
                if (password.value !== passwordConfirmation.value) {
                    passwordConfirmation.setCustomValidity('Las contrasenas no coinciden');
                } else {
                    passwordConfirmation.setCustomValidity('');
                }
            });

            // RFC to uppercase
            const rfcInput = document.getElementById('rfc');
            rfcInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>
@endsection
