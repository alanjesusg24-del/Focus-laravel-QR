@extends('layouts.business-app')

@section('title', 'Pago Exitoso - Sistema de Órdenes QR')

@section('page')
<div class="py-4">
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card border-0 shadow text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <svg class="icon icon-xxl text-success mx-auto" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>

                    <h1 class="h2 fw-bold text-success mb-3">¡Pago Procesado Exitosamente!</h1>

                    <p class="text-gray-700 mb-4">
                        Tu pago ha sido procesado correctamente. Tu cuenta está activa y ahora puedes acceder a todas las funcionalidades del sistema.
                    </p>

                    @if(session('success'))
                        <div class="alert alert-info" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="d-grid gap-3 mt-4">
                        <a href="{{ route('business.dashboard.index') }}" class="btn btn-primary btn-lg">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Ir al Dashboard
                        </a>

                        <a href="{{ route('business.orders.index') }}" class="btn btn-outline-primary btn-lg">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                            </svg>
                            Crear Órdenes
                        </a>

                        <a href="{{ route('business.payments.history') }}" class="btn btn-link text-gray-600">
                            Ver Historial de Pagos
                        </a>
                    </div>
                </div>
            </div>

            @if(config('services.mercadopago.mode') === 'sandbox')
            <div class="alert alert-warning mt-4" role="alert">
                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <strong>Modo de Prueba:</strong> Este pago ha sido simulado para fines de desarrollo y pruebas.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
