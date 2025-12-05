{{--
    Company: CETAM
    Project: FOCUS-LARAVEL-QR
    File: index.blade.php
    Created on: 28/11/2025
    Created by: Vanessa
    Approved by: Alan

    Changelog:
    - ID: 1 | Date: 28/11/2025
        Modified by: Vanessa
        Description: Implementation of payment plans view following FoodFlow design and CETAM standards.
--}}

@extends('layouts.business-app')

@section('title', 'Subscription Plans')

@section('page')
    <div class="py-4">
        {{-- Session Notifications --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <x-icon name="checkCircle" class="me-2" />
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <x-icon name="warning" class="me-2" />
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
            <div class="d-block mb-4 mb-md-0">
                <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                    <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item">
                            {{-- Enlace al Dashboard con color Primario forzado --}}
                            <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                                <x-icon name="home" />
                            </a>
                        </li>
                        {{-- Muestra la ubicación actual --}}
                        <li class="breadcrumb-item active" aria-current="page">Pagos</li>
                    </ol>
                </nav>
                <h2 class="h4 mt-2">Planes de Membresía</h2>
                <p class="mb-0 text-muted">Elige el plan que mejor se adapte a tus necesidades.</p>
            </div>
        </div>

        {{-- Subscription Status Alert --}}
        @if(!$business->subscription_active)
            <div class="alert alert-warning d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
                <x-icon name="warning" class="me-2" />
                <div>
                    <strong>Suscription Inactiva.</strong> Selecciona un plan para continuar.
                </div>
            </div>
        @endif

        <div class="row justify-content-center mb-5">
        @foreach($plans as $plan)
            @php
                $isCurrent = $business->plan_id === $plan->plan_id;
                $isTopTier = $plan->price >= 899; 
            @endphp

            <div class="col-12 col-lg-4 mb-4 px-lg-3"> 
                
                <div class="card h-100 shadow-lg border border-2 border-primary">
                    
                    <div class="card-header text-center py-3 bg-primary">
                        <h4 class="h5 mb-0 text-white fw-bold">{{ $plan->name }}</h4>
                    </div>

                    <div class="card-body d-flex flex-column p-4">
                        
                        <div class="text-center mb-2">
                            <div class="d-flex justify-content-center align-items-baseline">
                                <span class="display-4 fw-bold text-primary">${{ number_format($plan->price, 2) }}</span>
                                <span class="text-muted ms-2">/mes</span>
                            </div>
                            <p class="text-muted small mt-2 mb-0">{{ $plan->description }}</p>
                        </div>
                        <hr class="text-gray-200 mb-2">

                        <ul class="list-unstyled text-start mb-4 flex-grow-1">
                            
                            <li class="d-flex align-items-center mb-3 small">
                                <x-icon name="state.success" class="text-success me-2" />
                                <span class="text-primary">Acceso al Panel Web</span>
                            </li>

                            <li class="d-flex align-items-center mb-3 small">
                                <x-icon name="state.success" class="text-success me-2" />
                                <span class="text-primary">{{ $plan->retention_days }} días de retención</span>
                            </li>

                            <li class="d-flex align-items-center mb-3 small">
                                <x-icon name="state.success" class="text-success me-2" />
                                <span class="text-primary">Órdenes ilimitadas</span>
                            </li>

                            <li class="d-flex align-items-center mb-3 small">
                                @if($plan->has_chat_module ?? false)
                                    <x-icon name="state.success" class="text-success me-2" />
                                    <span class="text-primary fw-bold">Módulo de Chat Incluido</span>
                                @else
                                    <x-icon name="state.error" class="text-danger me-2" />
                                    <span class="text-danger">Sin Módulo de Chat</span>
                                @endif
                            </li>
                        </ul>

                        <div class="mt-auto pt-3 border-top">
                            <form action="{{ route('business.payments.checkout', $plan) }}" method="GET">
                                @if($isCurrent)
                                    <button type="button" class="btn btn-secondary w-100 fw-bold" disabled>
                                        Plan Actual
                                    </button>
                                    
                                @else
                                    <button type="submit" class="btn btn-primary w-100 fw-bold d-inline-flex align-items-center justify-content-center">
                                        <x-icon name="creditCard" class="me-2" />
                                        Adquirir Plan
                                    </button>
                                @endif
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

        
        @if($payments->count() > 0)
            <div class="card border-0 shadow-sm mt-5 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="mb-0 fw-bold text-primary">Historial de Pagos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Plan</th>
                                    <th class="border-bottom" scope="col">Fecha</th>
                                    <th class="border-bottom" scope="col">Monto</th>
                                    <th class="border-bottom" scope="col">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)
                            <tr>
                                <td class="fw-bold">{{ $payment->plan->name }}</td>
                                <td class="text-muted">{{ $payment->payment_date->format('d M, Y') }}</td>
                                <td class="fw-bold">${{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    @if($payment->status == 'completed')
                                        <span class="fw-bold text-success">Pagado</span>
                                    
                                    @elseif($payment->status == 'pending')
                                        <span class="fw-bold text-warning">Pendiente</span>
                                    
                                    @else
                                        <span class="fw-bold text-warning">{{ $payment->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection