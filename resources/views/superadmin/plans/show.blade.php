{{--
  Company: CETAM
  Project: FQR
  File: show.blade.php
  Created on: 15/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.superadmin-app')

@section('title', 'Detalle del Plan')

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.dashboard') }}">
                    <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.plans.index') }}">Planes</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $plan->name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Detalle del Plan: {{ $plan->name }}</h1>
            <p class="mb-0">Información completa del plan</p>
        </div>
        <div>
            <a href="{{ route('superadmin.plans.edit', $plan->plan_id) }}" class="btn btn-primary">
                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                </svg>
                Editar Plan
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- Plan Information -->
    <div class="col-12 col-lg-8 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <h5 class="mb-0">Información del Plan</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Nombre</p>
                        <h6 class="fw-bold">{{ $plan->name }}</h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Precio</p>
                        <h6 class="fw-bold text-success">${{ number_format($plan->price, 2) }}</h6>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <p class="text-muted mb-1">Descripción</p>
                        <p>{{ $plan->description ?? 'Sin descripción' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Duración</p>
                        <p class="fw-bold">{{ $plan->duration_days }} días</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Retención de Datos</p>
                        <p class="fw-bold">{{ $plan->retention_days ?? 'N/A' }} días</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Estado</p>
                        @if($plan->is_active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Negocios con este plan</p>
                        <p class="fw-bold text-info">{{ $plan->businesses_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <h5 class="mb-0">Características</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Módulo de Chat</span>
                        @if($plan->has_chat_module)
                            <svg class="icon icon-sm text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="icon icon-sm text-danger" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Re-alertas</span>
                        @if($plan->has_realerts)
                            <svg class="icon icon-sm text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="icon icon-sm text-danger" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </div>

                @if($plan->has_realerts)
                    <hr>
                    <h6 class="mb-3">Configuración de Re-alertas</h6>

                    <div class="mb-2">
                        <small class="text-muted">Intervalo</small>
                        <p class="mb-1">{{ $plan->realert_interval_minutes ?? 'N/A' }} minutos</p>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">Máximo de Re-alertas</small>
                        <p class="mb-1">{{ $plan->realert_max_count ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">Duración Máxima</small>
                        <p class="mb-0">
                            {{ $plan->realert_days ?? 0 }}d
                            {{ $plan->realert_hours ?? 0 }}h
                            {{ $plan->realert_minutes ?? 0 }}m
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Back Button -->
<div class="row">
    <div class="col-12">
        <a href="{{ route('superadmin.plans.index') }}" class="btn btn-primary">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
            </svg>
            Volver a Planes
        </a>
    </div>
</div>
@endsection
