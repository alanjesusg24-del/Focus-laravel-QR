@extends('layouts.business-app')

@section('title', 'Pagar - Sistema de Órdenes QR')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Checkout - Pagar</h2>
            <p class="mb-0">Completa tu pago para activar la suscripción</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.payments.index') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                </svg>
                Volver a Planes
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Resumen de Compra</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Plan:</td>
                                    <td>{{ $plan->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Descripción:</td>
                                    <td>{{ $plan->description ?? 'Plan de suscripción' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Duración:</td>
                                    <td>{{ $plan->duration_days }} días</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Retención de Datos:</td>
                                    <td>{{ $plan->retention_days }} días</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold fs-5">Total:</td>
                                    <td class="fw-bold fs-5 text-primary">${{ number_format($plan->price, 2) }} MXN</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            Serás redirigido a la página segura de pago de MercadoPago para completar tu compra.
                        </div>
                    </div>

                    <form action="{{ route('business.payments.create-checkout-session', ['plan' => $plan->plan_id]) }}" method="POST" id="mercadopago-form">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 py-3" id="mercadopago-submit-btn">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                            </svg>
                            Pagar con MercadoPago
                        </button>
                    </form>

                    <script>
                    // Debugging: ver si el form se envía correctamente
                    document.getElementById('mercadopago-form').addEventListener('submit', function(e) {
                        console.log('Form submitting to:', this.action);
                        console.log('Method:', this.method);
                    });
                    </script>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Información del Negocio</h2>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <div>
                                <h3 class="h6 mb-1">Negocio</h3>
                                <p class="small pe-4 mb-0">{{ $business->business_name }}</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <div>
                                <h3 class="h6 mb-1">Email</h3>
                                <p class="small pe-4 mb-0">{{ $business->email }}</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <div>
                                <h3 class="h6 mb-1">RFC</h3>
                                <p class="small pe-4 mb-0">{{ $business->rfc }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="text-center text-muted small">
                        <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        Pago seguro procesado por MercadoPago
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
