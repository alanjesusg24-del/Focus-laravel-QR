@extends('layouts.superadmin-app')

@section('title', 'Editar Plan - ' . $plan->name)

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg></a></li>
            <li class="breadcrumb-item"><a href="{{ route('superadmin.plans.index') }}">Planes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Editar Plan</h1>
            <p class="mb-0">Modifica la información del plan de suscripción</p>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Error!</strong> Por favor corrige los siguientes errores:
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($plan->businesses()->count() > 0)
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
        <div>
            <strong>Advertencia:</strong> Este plan tiene {{ $plan->businesses()->count() }} negocio(s) asociado(s).
            Los cambios en el precio o características afectarán a los negocios existentes.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('superadmin.plans.update', $plan->plan_id) }}">
    @csrf
    @method('PUT')

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">

            <!-- Información Básica -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Información Básica</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Nombre del Plan *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="duration_days" class="form-label">Duración (días) *</label>
                            <input type="number" class="form-control @error('duration_days') is-invalid @enderror" id="duration_days" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" min="1" required>
                            @error('duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description', $plan->description) }}</textarea>
                        <small class="text-muted">Descripción breve del plan</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="retention_days" class="form-label">Retención de datos (días)</label>
                            <input type="number" class="form-control @error('retention_days') is-invalid @enderror" id="retention_days" name="retention_days" value="{{ old('retention_days', $plan->retention_days) }}" min="0">
                            <small class="text-muted">Días para retener historial</small>
                            @error('retention_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4 pt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">Plan Activo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Módulo de Chat -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <svg class="icon icon-xs text-primary me-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                        </svg>
                        <h2 class="fs-5 fw-bold mb-0">Módulo de Chat</h2>
                    </div>
                    <span class="badge bg-success">+$50/mes</span>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="has_chat_module" name="has_chat_module" value="1" {{ old('has_chat_module', $plan->has_chat_module) ? 'checked' : '' }} onchange="calculatePrice()">
                        <label class="form-check-label fw-bold" for="has_chat_module">Habilitar módulo de chat</label>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <svg class="icon icon-xxs text-info me-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Permite comunicación en tiempo real entre el negocio y sus clientes
                    </small>
                </div>
            </div>

            <!-- Sistema de Re-Alertas -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <svg class="icon icon-xs text-warning me-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                        <h2 class="fs-5 fw-bold mb-0">Sistema de Re-Alertas</h2>
                    </div>
                    <span class="badge bg-info" id="realerts-price-badge">Variable</span>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="has_realerts" name="has_realerts" value="1" {{ old('has_realerts', $plan->has_realerts) ? 'checked' : '' }} onchange="toggleRealertFields()">
                        <label class="form-check-label fw-bold" for="has_realerts">Habilitar re-alertas automáticas</label>
                        <div class="text-muted small mt-1">Notificaciones periódicas para pedidos listos no recogidos</div>
                    </div>

                    <div id="realert-fields" style="display: {{ old('has_realerts', $plan->has_realerts) ? 'block' : 'none' }};">
                        <div class="alert alert-light border mb-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <svg class="icon icon-xs text-info" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="col">
                                    <small class="mb-0"><strong>Ejemplos:</strong> Comida rápida: cada 5 min | Tintorería: cada 1 día | Panadería: cada 30 min</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="realert_days" class="form-label small">Días</label>
                                <input type="number" class="form-control form-control-sm" id="realert_days" name="realert_days" value="{{ old('realert_days', $plan->realert_days ?? 0) }}" min="0" max="30" onchange="updateIntervalMinutes()">
                            </div>
                            <div class="col-md-3">
                                <label for="realert_hours" class="form-label small">Horas</label>
                                <input type="number" class="form-control form-control-sm" id="realert_hours" name="realert_hours" value="{{ old('realert_hours', $plan->realert_hours ?? 0) }}" min="0" max="23" onchange="updateIntervalMinutes()">
                            </div>
                            <div class="col-md-3">
                                <label for="realert_minutes" class="form-label small">Minutos</label>
                                <input type="number" class="form-control form-control-sm" id="realert_minutes" name="realert_minutes" value="{{ old('realert_minutes', $plan->realert_minutes ?? 15) }}" min="1" max="59" onchange="updateIntervalMinutes()">
                            </div>
                            <div class="col-md-3">
                                <label for="realert_max_count" class="form-label small">Máximo alertas</label>
                                <input type="number" class="form-control form-control-sm" id="realert_max_count" name="realert_max_count" value="{{ old('realert_max_count', $plan->realert_max_count ?? 4) }}" min="1" max="20">
                            </div>
                        </div>

                        <input type="hidden" id="realert_interval_minutes" name="realert_interval_minutes" value="{{ old('realert_interval_minutes', $plan->realert_interval_minutes ?? 15) }}">

                        <div class="alert alert-success d-flex align-items-center py-2 mb-0">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <small id="interval-summary">Intervalo: <strong>15 minutos</strong> entre cada re-alerta</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de Precio -->
            <div class="card border-0 shadow mb-4 bg-primary">
                <div class="card-body text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1 text-white">Precio Total del Plan</h3>
                            <div class="small text-white-50" id="price-breakdown">
                                Base: $200 + Chat: $0 + Re-alertas: $0
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="display-4 fw-bold text-white" id="total-price-display">$200</div>
                            <div class="small text-white-50">por mes</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campo oculto para el precio calculado -->
            <input type="hidden" id="price" name="price" value="{{ old('price', $plan->price) }}">

            <!-- Botones de Acción -->
            <div class="card border-0 shadow">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('superadmin.plans.index') }}" class="btn btn-gray-800 w-100">
                        Cancelar
                    </a>
                </div>
            </div>

        </div>
    </div>
</form>

@push('scripts')
<script>
    // Pricing constants
    const BASE_PRICE = 200;
    const CHAT_PRICE = 50;

    // Calculate re-alerts price based on frequency
    function calculateRealertsPrice(intervalMinutes) {
        if (intervalMinutes >= 1440) { // 1 día o más
            return 10; // $10 por día o más
        } else if (intervalMinutes >= 60) { // 1 hora o más
            return 20; // $20 por hora
        } else if (intervalMinutes >= 30) { // 30 minutos o más
            return 30; // $30 por cada 30-59 minutos
        } else if (intervalMinutes >= 15) { // 15 minutos o más
            return 40; // $40 por cada 15-29 minutos
        } else if (intervalMinutes >= 5) { // 5-14 minutos
            return 50; // $50 por cada 5-14 minutos
        } else { // Menos de 5 minutos
            return 70; // $70 por menos de 5 minutos (muy frecuente)
        }
    }

    // Update interval from days, hours, minutes
    function updateIntervalMinutes() {
        const days = parseInt(document.getElementById('realert_days').value) || 0;
        const hours = parseInt(document.getElementById('realert_hours').value) || 0;
        const minutes = parseInt(document.getElementById('realert_minutes').value) || 0;

        const totalMinutes = (days * 1440) + (hours * 60) + minutes;

        // Update hidden field
        document.getElementById('realert_interval_minutes').value = totalMinutes;

        // Update summary text
        const summaryEl = document.getElementById('interval-summary');
        let summaryText = 'Intervalo: <strong>';

        if (days > 0) summaryText += days + (days === 1 ? ' día' : ' días');
        if (hours > 0) summaryText += (days > 0 ? ', ' : '') + hours + (hours === 1 ? ' hora' : ' horas');
        if (minutes > 0 || totalMinutes === 0) summaryText += (days > 0 || hours > 0 ? ', ' : '') + minutes + (minutes === 1 ? ' minuto' : ' minutos');

        summaryText += '</strong> entre cada re-alerta';
        summaryEl.innerHTML = summaryText;

        // Recalculate price
        calculatePrice();
    }

    // Toggle re-alert fields
    function toggleRealertFields() {
        const checkbox = document.getElementById('has_realerts');
        const fields = document.getElementById('realert-fields');

        if (checkbox.checked) {
            fields.style.display = 'block';
            updateIntervalMinutes();
        } else {
            fields.style.display = 'none';
        }

        calculatePrice();
    }

    // Calculate total price
    function calculatePrice() {
        let total = BASE_PRICE;
        const breakdown = ['Base: $' + BASE_PRICE];

        // Add chat module
        const hasChatChecked = document.getElementById('has_chat_module').checked;
        if (hasChatChecked) {
            total += CHAT_PRICE;
            breakdown.push('Chat: $' + CHAT_PRICE);
        } else {
            breakdown.push('Chat: $0');
        }

        // Add re-alerts
        const hasRealertsChecked = document.getElementById('has_realerts').checked;
        if (hasRealertsChecked) {
            const intervalMinutes = parseInt(document.getElementById('realert_interval_minutes').value) || 15;
            const realertsPrice = calculateRealertsPrice(intervalMinutes);
            total += realertsPrice;
            breakdown.push('Re-alertas: $' + realertsPrice);

            // Update badge
            document.getElementById('realerts-price-badge').textContent = '+$' + realertsPrice + '/mes';
            document.getElementById('realerts-price-badge').className = 'badge bg-warning';
        } else {
            breakdown.push('Re-alertas: $0');
            document.getElementById('realerts-price-badge').textContent = 'Variable';
            document.getElementById('realerts-price-badge').className = 'badge bg-info';
        }

        // Update display
        document.getElementById('price').value = total.toFixed(2);
        document.getElementById('total-price-display').textContent = '$' + total.toFixed(0);
        document.getElementById('price-breakdown').textContent = breakdown.join(' + ');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateIntervalMinutes();
        toggleRealertFields();
        calculatePrice();
    });
</script>
@endpush
@endsection
