@extends('layouts.superadmin-app')

@section('title', 'Crear Plan')

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('superadmin.plans.index') }}">Planes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Crear Nuevo Plan</h1>
            <p class="mb-0">Agrega un nuevo plan de suscripción</p>
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

<form method="POST" action="{{ route('superadmin.plans.store') }}" novalidate>
    @csrf

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">

            <!-- Información Básica -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom">
                    <h2 class="fs-5 fw-bold mb-0">Información Básica</h2>
                </div>
                <div class="card-body">
                    <!-- Nombre del Plan -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Nombre del Plan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Duración -->
                    <div class="mb-4">
                        <label for="duration_months" class="form-label fw-bold">Duración (meses) <span class="text-danger">*</span></label>
                        <select class="form-select @error('duration_days') is-invalid @enderror" id="duration_months" onchange="updateDurationDays()">
                            <option value="1" {{ old('duration_days', 30) == 30 ? 'selected' : '' }}>1 mes (30 días)</option>
                            <option value="2" {{ old('duration_days') == 60 ? 'selected' : '' }}>2 meses (60 días)</option>
                            <option value="3" {{ old('duration_days') == 90 ? 'selected' : '' }}>3 meses (90 días)</option>
                            <option value="6" {{ old('duration_days') == 180 ? 'selected' : '' }}>6 meses (180 días)</option>
                            <option value="12" {{ old('duration_days') == 365 ? 'selected' : '' }}>12 meses (365 días)</option>
                        </select>
                        <input type="hidden" id="duration_days" name="duration_days" value="{{ old('duration_days', 30) }}">
                        @error('duration_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Descripción <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Retención de datos -->
                    <div class="mb-4">
                        <label for="retention_months" class="form-label fw-bold">Retención de datos (meses) <span class="text-danger">*</span></label>
                        <select class="form-select @error('retention_days') is-invalid @enderror" id="retention_months" onchange="updateRetentionDays()">
                            <option value="1" {{ old('retention_days', 30) == 30 ? 'selected' : '' }}>1 mes (incluido)</option>
                            <option value="2" {{ old('retention_days') == 60 ? 'selected' : '' }}>2 meses (+$20)</option>
                            <option value="3" {{ old('retention_days') == 90 ? 'selected' : '' }}>3 meses (+$30)</option>
                            <option value="6" {{ old('retention_days') == 180 ? 'selected' : '' }}>6 meses (+$50)</option>
                            <option value="12" {{ old('retention_days') == 365 ? 'selected' : '' }}>12 meses (+$80)</option>
                        </select>
                        <input type="hidden" id="retention_days" name="retention_days" value="{{ old('retention_days', 30) }}">
                        <small class="text-muted">El precio base incluye 1 mes de retención. Meses adicionales tienen costo extra.</small>
                        @error('retention_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Estado del Plan -->
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Plan Activo</label>
                        </div>
                        <small class="form-text text-muted">Los planes inactivos no se pueden seleccionar en nuevas suscripciones</small>
                    </div>
                </div>
            </div>

            <!-- Módulo de Chat -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <h2 class="fs-5 fw-bold mb-0">Módulo de Chat</h2>
                    <span class="text-success fw-bold" id="chat-price-badge">+$50</span>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="has_chat_module" name="has_chat_module" value="1" {{ old('has_chat_module') ? 'checked' : '' }} onchange="calculatePrice()">
                        <label class="form-check-label fw-bold" for="has_chat_module">Habilitar módulo de chat</label>
                    </div>
                    <small class="text-muted" id="chat-price-detail">$50 por 1 mes</small>
                </div>
            </div>

            <!-- Sistema de Re-Alertas -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <h2 class="fs-5 fw-bold mb-0">Sistema de Re-Alertas</h2>
                    <span class="text-info fw-bold" id="realerts-price-badge">Variable</span>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="has_realerts" name="has_realerts" value="1" {{ old('has_realerts') ? 'checked' : '' }} onchange="toggleRealertFields()">
                        <label class="form-check-label fw-bold" for="has_realerts">Habilitar re-alertas automáticas</label>
                    </div>

                    <div id="realert-fields" style="display: {{ old('has_realerts') ? 'block' : 'none' }};" class="mt-4">

                        <!-- Días -->
                        <div class="mb-4">
                            <label for="realert_days" class="form-label fw-bold">Días</label>
                            <input type="number" class="form-control" id="realert_days" name="realert_days" value="{{ old('realert_days', 0) }}" min="0" max="30" onchange="updateIntervalMinutes()">
                        </div>

                        <!-- Horas -->
                        <div class="mb-4">
                            <label for="realert_hours" class="form-label fw-bold">Horas</label>
                            <input type="number" class="form-control" id="realert_hours" name="realert_hours" value="{{ old('realert_hours', 0) }}" min="0" max="23" onchange="updateIntervalMinutes()">
                        </div>

                        <!-- Minutos -->
                        <div class="mb-4">
                            <label for="realert_minutes" class="form-label fw-bold">Minutos</label>
                            <input type="number" class="form-control" id="realert_minutes" name="realert_minutes" value="{{ old('realert_minutes', 15) }}" min="1" max="59" onchange="updateIntervalMinutes()">
                        </div>

                        <!-- Máximo de alertas -->
                        <div class="mb-4">
                            <label for="realert_max_count" class="form-label fw-bold">Máximo de alertas</label>
                            <input type="number" class="form-control" id="realert_max_count" name="realert_max_count" value="{{ old('realert_max_count', 4) }}" min="1" max="20">
                            <small class="form-text text-muted">Número máximo de re-alertas antes de detener las notificaciones</small>
                        </div>

                        <input type="hidden" id="realert_interval_minutes" name="realert_interval_minutes" value="{{ old('realert_interval_minutes', 15) }}">

                        <div class="mb-0">
                            <small class="text-success" id="interval-summary">Intervalo: <strong>15 minutos</strong> entre cada re-alerta</small>
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
                            <div class="small text-white-50 mt-1" id="price-per-month">
                                $200/mes por 1 mes
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="display-4 fw-bold text-white" id="total-price-display">$200</div>
                            <div class="small text-white-50">total del plan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campo oculto para el precio calculado -->
            <input type="hidden" id="price" name="price" value="{{ old('price', 200) }}">

            <!-- Botones de Acción -->
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Crear Plan</button>
                        <a href="{{ route('superadmin.plans.index') }}" class="btn btn-primary btn-lg">Cancelar</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

@push('scripts')
<script>
    // Pricing constants
    const BASE_PRICE_PER_MONTH = 200;
    const CHAT_PRICE_PER_MONTH = 50;

    // Update duration in days based on months selected
    function updateDurationDays() {
        const months = parseInt(document.getElementById('duration_months').value);
        let days;

        if (months === 12) {
            days = 365;
        } else if (months === 6) {
            days = 180;
        } else {
            days = months * 30;
        }

        document.getElementById('duration_days').value = days;
        calculatePrice();
    }

    // Update retention in days based on months selected
    function updateRetentionDays() {
        const months = parseInt(document.getElementById('retention_months').value);
        let days;

        if (months === 12) {
            days = 365;
        } else if (months === 6) {
            days = 180;
        } else {
            days = months * 30;
        }

        document.getElementById('retention_days').value = days;
        calculatePrice(); // Recalcular precio cuando cambia la retención
    }

    // Calculate retention price (1 month is included, additional months have cost)
    function calculateRetentionPrice(retentionMonths) {
        if (retentionMonths === 1) {
            return 0; // Incluido en el precio base
        } else if (retentionMonths === 2) {
            return 20;
        } else if (retentionMonths === 3) {
            return 30;
        } else if (retentionMonths === 6) {
            return 50;
        } else if (retentionMonths === 12) {
            return 80;
        }
        return 0;
    }

    // Calculate chat price with volume discount
    function calculateChatPrice(months) {
        if (months === 1) {
            return 50;
        } else if (months === 2) {
            return 80; // $40/mes (20% descuento)
        } else if (months === 3) {
            return 105; // $35/mes (30% descuento)
        } else if (months === 6) {
            return 180; // $30/mes (40% descuento)
        } else if (months === 12) {
            return 300; // $25/mes (50% descuento)
        }
        return months * CHAT_PRICE_PER_MONTH;
    }

    // Calculate re-alerts base price based on frequency
    function calculateRealertsBasePrice(intervalMinutes) {
        if (intervalMinutes >= 1440) { // 1 día o más
            return 10; // $10 por mes
        } else if (intervalMinutes >= 60) { // 1 hora o más
            return 20; // $20 por mes
        } else if (intervalMinutes >= 30) { // 30 minutos o más
            return 30; // $30 por mes
        } else if (intervalMinutes >= 15) { // 15 minutos o más
            return 40; // $40 por mes
        } else if (intervalMinutes >= 5) { // 5-14 minutos
            return 50; // $50 por mes
        } else { // Menos de 5 minutos
            return 70; // $70 por mes (muy frecuente)
        }
    }

    // Calculate re-alerts price with volume discount
    function calculateRealertsPrice(intervalMinutes, months) {
        const basePrice = calculateRealertsBasePrice(intervalMinutes);

        if (months === 1) {
            return basePrice;
        } else if (months === 2) {
            return Math.round(basePrice * 1.6); // 20% descuento
        } else if (months === 3) {
            return Math.round(basePrice * 2.1); // 30% descuento
        } else if (months === 6) {
            return Math.round(basePrice * 3.6); // 40% descuento
        } else if (months === 12) {
            return Math.round(basePrice * 6); // 50% descuento
        }
        return basePrice * months;
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
        const months = parseInt(document.getElementById('duration_months').value);
        const retentionMonths = parseInt(document.getElementById('retention_months').value);

        // Calculate base price (price per month * months)
        const basePrice = BASE_PRICE_PER_MONTH * months;
        let total = basePrice;
        const breakdown = ['Base: $' + basePrice];

        // Add retention cost (if more than 1 month)
        const retentionPrice = calculateRetentionPrice(retentionMonths);
        if (retentionPrice > 0) {
            total += retentionPrice;
            breakdown.push('Retención extra: $' + retentionPrice);
        }

        // Add chat module with discount
        const hasChatChecked = document.getElementById('has_chat_module').checked;
        let chatPrice = 0;
        if (hasChatChecked) {
            chatPrice = calculateChatPrice(months);
            total += chatPrice;
            breakdown.push('Chat: $' + chatPrice);

            // Update chat badge and detail
            const chatPerMonth = (chatPrice / months).toFixed(2);
            document.getElementById('chat-price-badge').textContent = '+$' + chatPrice;
            document.getElementById('chat-price-detail').textContent = '$' + chatPerMonth + '/mes × ' + months + ' ' + (months === 1 ? 'mes' : 'meses') + ' = $' + chatPrice;
        } else {
            breakdown.push('Chat: $0');
            document.getElementById('chat-price-badge').textContent = '+$50';
            document.getElementById('chat-price-detail').textContent = '$50 por 1 mes';
        }

        // Add re-alerts with discount
        const hasRealertsChecked = document.getElementById('has_realerts').checked;
        let realertsPrice = 0;
        if (hasRealertsChecked) {
            const intervalMinutes = parseInt(document.getElementById('realert_interval_minutes').value) || 15;
            realertsPrice = calculateRealertsPrice(intervalMinutes, months);
            total += realertsPrice;
            breakdown.push('Re-alertas: $' + realertsPrice);

            // Update badge
            const realertsPerMonth = (realertsPrice / months).toFixed(2);
            document.getElementById('realerts-price-badge').textContent = '+$' + realertsPrice;
            document.getElementById('realerts-price-badge').className = 'text-warning fw-bold';
        } else {
            breakdown.push('Re-alertas: $0');
            document.getElementById('realerts-price-badge').textContent = 'Variable';
            document.getElementById('realerts-price-badge').className = 'text-info fw-bold';
        }

        // Update display
        const pricePerMonth = (total / months).toFixed(2);
        document.getElementById('price').value = total.toFixed(2);
        document.getElementById('total-price-display').textContent = '$' + total.toFixed(0);
        document.getElementById('price-breakdown').textContent = breakdown.join(' + ');
        document.getElementById('price-per-month').textContent = '$' + pricePerMonth + '/mes por ' + months + ' ' + (months === 1 ? 'mes' : 'meses');
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
