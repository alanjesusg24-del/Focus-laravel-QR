{{--
    Company: CETAM
    Project: FQR
    File: index.blade.php
    Created on: 18/11/2025
    Created by: Dafne Vanessa Castillo Moreno
    Approved by: Dafne Vanessa Castillo Moreno

    Changelog:
    - ID: 1 | Date: 28/11/2025
      Modified by: Dafne Vanessa Castillo Moreno
      Description: Implementation of payment plans view following CETAM standards.
--}}

@extends('layouts.business-app')

@section('title', 'Planes de Suscripción')

@section('page')
    <div class="py-4">
        {{-- Page Header --}}
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
            <div class="d-block mb-4 mb-md-0">
                <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                    <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item">
                            <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                                <x-icon name="nav.home" />
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Suscripción</li>
                    </ol>
                </nav>
                <h2 class="h4 mt-2">Planes de Membresía</h2>
                @php
                    $hasPaid = $business->last_payment_date !== null;
                    $currentPlanName = 'Sin plan activo';
                    $nextPaymentDate = null;
                    $daysUntilExpiration = null;
                    $isExpired = false;

                    if ($hasPaid && $business->last_payment_date) {
                        // Calculate expiration date (30 days after the last payment)
                        $nextPaymentDate = \Carbon\Carbon::parse($business->last_payment_date)->addDays(30);
                        $daysUntilExpiration = now()->diffInDays($nextPaymentDate, false);
                        $isExpired = $daysUntilExpiration < 0;

                        if ($business->plan) {
                            $currentPlanName = $business->plan->name;
                        } elseif ($business->plan_id) {
                            $foundPlan = $plans->firstWhere('plan_id', $business->plan_id);
                            if ($foundPlan) {
                                $currentPlanName = $foundPlan->name;
                            }
                        }
                    }
                @endphp

                @if($hasPaid)
                    <p class="mb-0 text-primary">
                        Tu plan actual es <strong class="text-primary">"{{ $currentPlanName }}"</strong>.
                    </p>
                    @if($nextPaymentDate)
                        @if($isExpired)
                            <p class="mb-0 text-danger small">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>Plan vencido el {{ $nextPaymentDate->format('d/m/Y') }}</strong> - Renueva para seguir usando el sistema
                            </p>
                        @elseif($daysUntilExpiration <= 7)
                            <p class="mb-0 text-warning small">
                                <i class="fas fa-clock me-1"></i>
                                Tu plan vence el {{ $nextPaymentDate->format('d/m/Y') }} ({{ abs($daysUntilExpiration) }} días restantes)
                            </p>
                        @else
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-calendar me-1"></i>
                                Próxima renovación: {{ $nextPaymentDate->format('d/m/Y') }}
                            </p>
                        @endif
                    @endif
                @else
                    <p class="mb-0 text-warning">
                        <strong>No tienes un plan activo.</strong> Selecciona un plan para comenzar a usar el sistema.
                    </p>
                @endif
            </div>
        </div>

        {{-- Subscription Status Alert --}}

        <div class="row justify-content-center mb-5">
        @foreach($plans as $plan)
            @php
                $hasPaid = $business->last_payment_date !== null;
                $isCurrent = $hasPaid && ($business->plan_id === $plan->plan_id);
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
                                    <button type="submit" class="btn btn-secondary text-white w-100 fw-bold d-inline-flex align-items-center justify-content-center">
                                        <x-icon name="money.card" class="me-2 " />
                                        Renovar Plan
                                    </button>
                                @elseif($hasPaid)
                                    <button type="submit" class="btn btn-primary w-100 fw-bold d-inline-flex align-items-center justify-content-center">
                                        <x-icon name="money.card" class="me-2" />
                                        Cambiar a este Plan
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary w-100 fw-bold d-inline-flex align-items-center justify-content-center">
                                        <x-icon name="money.card" class="me-2" />
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
@endsection