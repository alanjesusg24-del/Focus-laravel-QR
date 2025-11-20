@extends('layouts.business-app')

@section('title', 'Pago Cancelado - Sistema de Órdenes QR')

@section('page')
<div class="py-4">
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card border-0 shadow text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <svg class="icon icon-xxl text-warning mx-auto" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>

                    <h1 class="h2 fw-bold text-warning mb-3">Pago Cancelado</h1>

                    <p class="text-gray-700 mb-4">
                        Tu pago fue cancelado. No se ha realizado ningún cargo a tu cuenta.
                    </p>

                    <p class="text-gray-600 mb-4">
                        Si experimentaste algún problema durante el proceso de pago, por favor contacta a nuestro equipo de soporte.
                    </p>

                    <div class="d-grid gap-3 mt-4">
                        <a href="{{ route('business.payments.index') }}" class="btn btn-primary btn-lg">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                            </svg>
                            Intentar de Nuevo
                        </a>

                        <a href="{{ route('business.dashboard.index') }}" class="btn btn-outline-secondary btn-lg">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Regresar al Dashboard
                        </a>

                        <a href="{{ route('business.support.index') }}" class="btn btn-link text-gray-600">
                            Contactar Soporte
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow mt-4">
                <div class="card-body">
                    <h3 class="h5 fw-bold mb-3">¿Necesitas Ayuda?</h3>
                    <p class="text-gray-600 mb-0">
                        Nuestro equipo de soporte está disponible para ayudarte con cualquier problema o pregunta relacionada con pagos.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
