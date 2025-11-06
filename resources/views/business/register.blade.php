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
        <section class="vh-lg-100 mt-5 mt-lg-0 bg-soft d-flex align-items-center">
            <div class="container">
                <p class="text-center">
                    <a href="{{ route('business.login') }}" class="d-flex align-items-center justify-content-center">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Volver al inicio de sesión
                    </a>
                </p>
                <div class="row justify-content-center form-bg-image">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-700">
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
                                <p class="text-gray">Únete al sistema Order QR</p>
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
                            <form method="POST" action="{{ route('business.register') }}" class="mt-4">
                                @csrf

                                <div class="row">
                                    <!-- Business Name -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="business_name">Nombre del Negocio *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control @error('business_name') is-invalid @enderror"
                                                    id="business_name" name="business_name"
                                                    value="{{ old('business_name') }}"
                                                    placeholder="Mi Negocio SA" required autofocus>
                                            </div>
                                            @error('business_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- RFC -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="rfc">RFC *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control @error('rfc') is-invalid @enderror"
                                                    id="rfc" name="rfc"
                                                    value="{{ old('rfc') }}"
                                                    placeholder="ABC123456XYZ" maxlength="13" required>
                                            </div>
                                            @error('rfc')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="email">Email *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                                    </svg>
                                                </span>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                    id="email" name="email"
                                                    value="{{ old('email') }}"
                                                    placeholder="contacto@minegocio.com" required>
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="phone">Teléfono *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                                    </svg>
                                                </span>
                                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                    id="phone" name="phone"
                                                    value="{{ old('phone') }}"
                                                    placeholder="5512345678" required>
                                            </div>
                                            @error('phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="password">Contraseña *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password"
                                                    placeholder="Mínimo 8 caracteres" required>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Plan Selection (Hidden - Solo hay uno) -->
                                    <input type="hidden" name="plan_id" value="1">
                                </div>

                                <!-- Módulos y Configuración de Precio -->
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <h6 class="text-institutional-blue fw-bold">Configuración del Plan</h6>
                                        <p class="text-sm text-gray-600 mb-0">Precio base: <span class="fw-bold">$299.00 MXN/mes</span></p>
                                    </div>

                                    <!-- Módulo de Chat -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            id="has_chat_module" name="has_chat_module"
                                                            {{ old('has_chat_module') ? 'checked' : '' }}
                                                            onchange="updatePrice()">
                                                        <label class="form-check-label fw-bold" for="has_chat_module">
                                                            Módulo de Chat
                                                        </label>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mb-0 mt-2">
                                                        Incluye chat en tiempo real con clientes.
                                                        <br><span class="text-institutional-orange fw-bold">+$150.00 MXN/mes</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Retención de Datos -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="data_retention_months" class="fw-bold">Retención de Datos</label>
                                            <select class="form-select @error('data_retention_months') is-invalid @enderror"
                                                id="data_retention_months" name="data_retention_months"
                                                onchange="updatePrice()" required>
                                                <option value="1" {{ old('data_retention_months', 1) == 1 ? 'selected' : '' }}>
                                                    1 mes (incluido)
                                                </option>
                                                <option value="3" {{ old('data_retention_months') == 3 ? 'selected' : '' }}>
                                                    3 meses (+$100.00 MXN)
                                                </option>
                                                <option value="6" {{ old('data_retention_months') == 6 ? 'selected' : '' }}>
                                                    6 meses (+$250.00 MXN)
                                                </option>
                                                <option value="12" {{ old('data_retention_months') == 12 ? 'selected' : '' }}>
                                                    12 meses (+$550.00 MXN)
                                                </option>
                                            </select>
                                            <small class="text-muted">Tiempo que se guardarán tus órdenes y datos</small>
                                        </div>
                                    </div>

                                    <!-- Resumen de Precio -->
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">Precio Total Mensual:</span>
                                                <span class="h5 mb-0 text-institutional-blue" id="total_price">$299.00 MXN</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" required>
                                        <label class="form-check-label fw-normal mb-0" for="terms">
                                            Acepto los <a href="#" class="fw-bold text-institutional-blue">términos y condiciones</a>
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-institutional-blue">Registrar Negocio</button>
                                </div>
                            </form>

                            <!-- Login Link -->
                            <div class="d-flex justify-content-center align-items-center mt-4">
                                <span class="fw-normal">
                                    ¿Ya tienes cuenta?
                                    <a href="{{ route('business.login') }}" class="fw-bold text-institutional-blue">Inicia sesión aquí</a>
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

@section('scripts')
<script>
    function updatePrice() {
        const basePrice = 299.00;
        const chatModulePrice = 150.00;
        const retentionPrices = {
            1: 0,
            3: 100.00,
            6: 250.00,
            12: 550.00
        };

        let total = basePrice;

        // Add chat module if checked
        if (document.getElementById('has_chat_module').checked) {
            total += chatModulePrice;
        }

        // Add retention cost
        const retention = parseInt(document.getElementById('data_retention_months').value);
        total += retentionPrices[retention];

        // Update display
        document.getElementById('total_price').textContent = '$' + total.toFixed(2) + ' MXN';
    }

    // Initialize price on page load
    document.addEventListener('DOMContentLoaded', function() {
        updatePrice();
    });
</script>
@endsection
