{{--
    Company: CETAM
    Project: FOCUS-LARAVEL-QR
    File: index.blade.php
    Created on: 28/11/2025
    Created by: [Tu Nombre]
    Approved by: [Nombre del Revisor]

    Changelog:
    - ID: 1 | Date: 28/11/2025
        Modified by: [Tu Nombre]
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
                            <a href="#"><x-icon name="home" /></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Pagos</li>
                    </ol>
                </nav>
                <h2 class="h4 mt-2">Planes de Membresía</h2>
                <p class="text-muted">Elige el plan que mejor se adapte a tus necesidades.</p>
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

        {{-- Plans Grid --}}
        <div class="row justify-content-center mb-5">
            @foreach($plans as $plan)
                <div class="col-12 col-lg-5 mb-4 px-lg-3">
                    {{-- 
                        Card Design:
                        Using Bootstrap utility classes strictly. 
                        Border-primary highlights the active plan.
                    --}}
                    <div class="card border-0 shadow-sm h-100 p-4 {{ $business->plan_id === $plan->plan_id ? 'border border-2 border-primary' : '' }}">
                        <div class="card-body d-flex flex-column">
                            
                            {{-- Plan Header --}}
                            <div class="text-center mb-4">
                                <h3 class="h5 text-uppercase text-muted mb-3">{{ $plan->name }}</h3>
                                <div class="d-flex justify-content-center align-items-baseline">
                                    {{-- Typography: display-4 for price prominence [cite: 734] --}}
                                    <span class="display-4 fw-bold text-dark">${{ number_format($plan->price, 2) }}</span>
                                    <span class="text-muted ms-2">/mes</span>
                                </div>
                                <p class="text-muted small mt-2">{{ $plan->description }}</p>
                            </div>

                            <hr class="text-gray-200 mb-4">

                            {{-- Features List --}}
                            <ul class="list-unstyled mb-5 flex-grow-1">
                                {{-- Feature: Web Panel --}}
                                <li class="d-flex align-items-center mb-3">
                                    <x-icon name="checkCircle" class="text-success me-3" />
                                    <span class="text-gray-700">Acceso al Panel Web (Gestión)</span>
                                </li>

                                {{-- Feature: Data Retention --}}
                                <li class="d-flex align-items-center mb-3">
                                    <x-icon name="checkCircle" class="text-success me-3" />
                                    <span class="text-gray-700">{{ $plan->retention_days }} días de retención de datos</span>
                                </li>

                                {{-- Feature: Unlimited Orders --}}
                                <li class="d-flex align-items-center mb-3">
                                    <x-icon name="checkCircle" class="text-success me-3" />
                                    <span class="text-gray-700">Órdenes ilimitadas</span>
                                </li>

                                 {{-- Feature: QR Generation --}}
                                 <li class="d-flex align-items-center mb-3">
                                    <x-icon name="checkCircle" class="text-success me-3" />
                                    <span class="text-gray-700">Generación de códigos QR</span>
                                </li>
                            </ul>

                            {{-- Action Buttons --}}
                            {{-- 
                                Button Standards:
                                - Primary (Dark #1F2937): Main action "Adquirir Plan".
                                - Secondary (Red/Orange #FB503B): Highlight/Current status "Plan Actual".
                            --}}
                            <div class="mt-auto">
                                <form action="{{ route('business.payments.checkout', $plan) }}" method="GET">
                                    @if($business->plan_id === $plan->plan_id)
                                        <button type="button" class="btn btn-secondary w-100 fw-bold" disabled>
                                            Plan Actual
                                        </button>
                                        <div class="text-center mt-2">
                                            <small class="text-muted">Expira: {{ $business->subscription_end_date ? $business->subscription_end_date->format('d/m/Y') : 'N/A' }}</small>
                                        </div>
                                    @else
                                        <button type="submit" class="btn btn-primary w-100 fw-bold">
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

        {{-- Recent Payments History --}}
        @if($payments->count() > 0)
            <div class="card border-0 shadow-sm mt-5">
                <div class="card-header bg-white border-bottom-0 pt-4">
                    <h5 class="mb-0">Historial de Pagos</h5>
                </div>
                <div class="table-responsive">
                    {{-- Table Standards [cite: 721] --}}
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
                                        <span class="badge bg-success">Pagado</span>
                                    @else
                                        <span class="badge bg-warning">{{ $payment->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection