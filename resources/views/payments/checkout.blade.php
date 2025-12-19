{{--
  Company: CETAM
  Project: FQR
  File: checkout.blade.php
  Created on: 14/12/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.business-app')

@section('title', 'Finalizar Compra')

@section('page')
<div class="py-4 animate__animated animate__fadeIn">

    <nav aria-label="breadcrumb" class="d-none d-md-inline-block mb-3">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('business.dashboard.index') }}">
                    <x-icon name="nav.home" class="text-primary" />
                </a>
            </li>
            <li class="breadcrumb-item">
                {{-- Add ‘text-primary’ here to force the color. --}}
                <a href="{{ route('business.payments.index') }}" class="text-decoration-none text-primary">
                    Suscripción
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Pago</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h1 class="h4 mt-1">Finalizar Compra</h1>
        <p class="mb-0 text-primary">
            Completa los datos para activar tu plan <strong class="text-primary">{{ $plan->name }}</strong>.
        </p>
    </div>

    <div class="row">
    
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="h6 fw-bold text-primary mb-4">Datos de Pago</h5>

                    <form action="{{ route('business.payments.process-simulation', ['plan' => $plan->plan_id]) }}" method="POST" id="payment-form">
                        @csrf

                        {{-- Name card --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre en la Tarjeta <span class="text-danger">*</span></label>
                            <input type="text" name="card_name" id="card_name" class="form-control py-2" placeholder="Titular de la Tarjeta" maxlength="15" required>
                            <div class="invalid-feedback">Por favor ingrese el nombre del titular</div>
                        </div>

                        {{-- Card Number --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Número de Tarjeta <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fa-regular fa-credit-card text-muted"></i>
                                </span>
                                <input type="text" name="card_number" id="card_number" class="form-control py-2 border-start-0 ps-0" placeholder="0000 0000 0000 0000" maxlength="20" required>
                            </div>
                            <div class="invalid-feedback">Por favor ingrese un número de tarjeta válido</div>
                        </div>

                        <div class="row">
                            {{-- Expiry --}}
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small">Vencimiento <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="expiry_month" id="expiry_month" class="form-control py-2 text-center" placeholder="MM" maxlength="2" required>
                                    <span class="input-group-text bg-white border-start-0 border-end-0 fw-bold px-1">/</span>
                                    <input type="text" name="expiry_year" id="expiry_year" class="form-control py-2 text-center border-start-0" placeholder="AA" maxlength="2" required>
                                </div>
                                <div class="invalid-feedback">Ingrese fecha válida (MM/AA)</div>
                            </div>

                            {{-- cvc --}}
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small">CVC <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="cvc" id="cvc" class="form-control py-2 border-end-0" placeholder="CVV" maxlength="3" required>
                                    <span class="input-group-text bg-white border-start-0">
                                        <i class="fa-solid fa-lock text-muted small"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback">Ingrese CVC válido</div>
                            </div>
                        </div>
                        <button type="submit" id="real-submit-btn" class="d-none">Enviar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="h6 fw-bold text-primary mb-3">Resumen</h5>
                    
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Plan Seleccionado:</span>
                        <span class="fw-bold text-dark">{{ $plan->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Precio Mensual:</span>
                        <span class="fw-bold text-dark">${{ number_format($plan->price, 2) }}</span>
                    </div>

                    <hr class="text-muted my-3 opacity-25">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold h5 mb-0">Total a Pagar</span>
                        <span class="fw-bold h4 mb-0 text-primary">${{ number_format($plan->price, 2) }}</span>
                    </div>

                    <button type="button" onclick="document.getElementById('real-submit-btn').click()" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        Pagar Ahora
                    </button>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('payment-form');
    const cardName = document.getElementById('card_name');
    const cardNumber = document.getElementById('card_number');
    const expiryMonth = document.getElementById('expiry_month');
    const expiryYear = document.getElementById('expiry_year');
    const cvc = document.getElementById('cvc');

    // Funcion to format card number as 'XXXX XXXX XXXX XXXX'
    function formatCardNumber(value) {
        const v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        const matches = v.match(/\d{4,16}/g);
        const match = matches && matches[0] || '';
        const parts = [];
        const cardName = document.getElementById('card_name');

        for (let i = 0, len = match.length; i < len; i += 4) {
            parts.push(match.substring(i, i + 4));
        }

        if (parts.length) {
            return parts.join(' ');
        } else {
            return value;
        }
    }

    // Input masking: Just numbers and format for card number
    cardNumber.addEventListener('input', function(e) {
        // Remove all non-numeric characters first
        const numericOnly = e.target.value.replace(/[^0-9]/g, '');
        const formatted = formatCardNumber(numericOnly);
        e.target.value = formatted;
    });

    // Input masking: Just numbers for month (MM)
    expiryMonth.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 2);

        // Validate that the month is between 01 and 12
        if (e.target.value.length === 2) {
            const month = parseInt(e.target.value);
            if (month < 1 || month > 12) {
                e.target.value = '';
            }
        }
    });
    cardName.addEventListener('input', function(e) {
        const value = e.target.value;
        e.target.value = value.replace(/[^a-zA-Z\s]/g, ''); // Permitir solo letras y espacios
    });

    // Input masking: Just numbers for year (YY)
    expiryYear.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 2);
    });

    // Input masking: Just numbers for CVC (3 digits)
    cvc.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 3);
    });

    // Prevent non-numeric characters from sticking
    [cardNumber, expiryMonth, expiryYear, cvc].forEach(input => {
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numericOnly = pastedText.replace(/[^0-9]/g, '');

            if (input === cardNumber) {
                input.value = formatCardNumber(numericOnly);
            } else {
                input.value = numericOnly.substring(0, input.maxLength);
            }
        });
    });

    // Form validation upon submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let isValid = true;

        // Validate cardholder name
        if (cardName.value.trim() === '') {
            cardName.classList.add('is-invalid');
            isValid = false;
        } else {
            cardName.classList.remove('is-invalid');
        }

        // Validate card number (16 digits)
        const cardNumberClean = cardNumber.value.replace(/\s+/g, '');
        if (cardNumberClean.length !== 16) {
            cardNumber.classList.add('is-invalid');
            isValid = false;
        } else {
            cardNumber.classList.remove('is-invalid');
        }

        // Validate month (01-12)
        const month = parseInt(expiryMonth.value);
        if (expiryMonth.value.length !== 2 || month < 1 || month > 12) {
            expiryMonth.classList.add('is-invalid');
            isValid = false;
        } else {
            expiryMonth.classList.remove('is-invalid');
        }

        // Validate year (2 digits)
        if (expiryYear.value.length !== 2) {
            expiryYear.classList.add('is-invalid');
            isValid = false;
        } else {
            expiryYear.classList.remove('is-invalid');
        }

        // Validate CVC (3 digits)
        if (cvc.value.length !== 3) {
            cvc.classList.add('is-invalid');
            isValid = false;
        } else {
            cvc.classList.remove('is-invalid');
        }

        // If all fields are valid, submit the form
        if (isValid) {
            // Show loading indicator (optional)
            const submitBtn = document.querySelector('[onclick*="real-submit-btn"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
            }

            form.submit();
        } else {
            // Scroll to the first invalid field
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Clear is-invalid class when the user starts typing
    [cardName, cardNumber, expiryMonth, expiryYear, cvc].forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>
@endpush