{{--
    Company: CETAM
    Project: FQR
    File: show.blade.php
    Created on: 25/11/2025
    Created by: Alan Jesus Garcia Nava
    Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.business-app')

@section('title', 'Orden ' . $order->folio_number)

@section('page')
<div class="py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}" class="text-dark">
                            <x-icon name="home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.orders.index') }}" class="text-dark">Órdenes</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $order->folio_number }}</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Detalle de Orden</h2>
            <p class="mb-0 text-muted">Orden {{ $order->folio_number }}</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.orders.index') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <x-icon name="back" class="me-2"/>
                Atras
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    <div class="row">
        {{-- Main Content --}}
        <div class="col-12 col-xl-8 mb-4">
            {{-- Order Header Card --}}
            <div class="card border-0 shadow mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            @if($order->business_folio)
                                <h3 class="h4 mb-1 text-primary">{{ $order->business_folio }}</h3>
                                <p class="text-muted small mb-2">
                                    <x-icon name="nav.dashboard" class="me-1"/> Folio Sistema: {{ $order->folio_number }}
                                </p>
                            @else
                                <h3 class="h5 mb-2">{{ $order->folio_number }}</h3>
                            @endif
                            <p class="text-gray-600 mb-0">
                                <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                Creada el {{ $order->created_at->format('d/m/Y \a \l\a\s H:i') }}
                            </p>
                        </div>
                        <div class="col-auto">
                            @php
                                $statusClasses = [
                                    'pending' => 'badge-warning',
                                    'ready' => 'badge-success',
                                    'delivered' => 'badge-info',
                                    'cancelled' => 'badge-danger',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pendiente',
                                    'ready' => 'Listo para Recoger',
                                    'delivered' => 'Entregado',
                                    'cancelled' => 'Cancelado',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$order->status] }} fs-6">{{ $statusLabels[$order->status] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description Card --}}
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Descripción</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->description ?? 'Sin descripción' }}</p>
                </div>
            </div>

            {{-- Status Timeline Card --}}
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Historial de Estados</h5>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-one-side">
                        {{-- Created --}}
                        <div class="timeline-block mb-3">
                            <span class="timeline-step badge-success">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Orden Creada</h6>
                                    <small class="text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- Ready --}}
                        @if($order->ready_at)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step badge-success">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Orden Lista</h6>
                                    <small class="text-gray-500">{{ $order->ready_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Delivered --}}
                        @if($order->delivered_at)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step badge-info">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Orden Entregada</h6>
                                    <small class="text-gray-500">{{ $order->delivered_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Cancelled --}}
                        @if($order->cancelled_at)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step badge-danger">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Orden Cancelada</h6>
                                    <small class="text-gray-500">{{ $order->cancelled_at->format('d/m/Y H:i') }}</small>
                                </div>
                                @if($order->cancellation_reason)
                                <p class="text-danger small mt-1 mb-0">Motivo: {{ $order->cancellation_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-xl-4">
            @if($order->qr_code_url && !$order->mobile_user_id)
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="mb-0 fw-bold text-primary">Código QR</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $order->qr_code_url }}" alt="Código QR" class="img-fluid rounded border mb-3 shadow-sm" style="max-width: 250px;">
                    <a href="{{ route('business.orders.downloadQr', $order) }}" class="btn btn-outline-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center">
                        <x-icon name="action.download" class="me-2" />
                        Descargar QR
                    </a>
                </div>
            </div>
            @elseif($order->mobile_user_id)
            <div class="card border-0 shadow mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        {{-- Link/chain icon --}}
                        <x-icon name="link" class="me-2 text-white" /> 
                        Orden Ligada
                    </h5>
                </div>
                <div class="card-body text-center py-4">
                    {{-- Large Success Icon --}}
                    <x-icon name="success" class="text-success mb-3 display-1" style="font-size: 3rem;" />
                    <p class="text-muted mb-0 fw-bold">
                        Esta orden está ligada a la app móvil del cliente
                    </p>
                </div>
            </div>
            @endif


            {{-- Actions Card --}}
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    @if($order->status === 'pending' && $order->mobile_user_id)
                    <form action="{{ route('business.orders.markAsReady', $order) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 d-inline-flex align-items-center justify-content-center">
                            <x-icon name="success" class="me-2"/> Marcar como Listo
                        </button>
                    </form>
                    @elseif($order->status === 'pending' && !$order->mobile_user_id)
                    <div class="alert alert-warning d-flex align-items-center mb-2" role="alert">
                        <x-icon name="warning" class="me-2 flex-shrink-0"/>
                        <small>El cliente debe escanear el QR primero</small>
                    </div>
                    @endif
                    @if($order->status === 'ready')
                    <form action="{{ route('business.orders.markAsDelivered', $order) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-info w-100 d-inline-flex align-items-center justify-content-center">
                            <x-icon name="success" class="me-2"/> Entregar Orden
                        </button>
                    </form>
                    @endif
                    {{-- Common actions --}}
                    @if(in_array($order->status, ['pending', 'ready']))
                    <button type="button" class="btn btn-danger w-100 mb-2 d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <x-icon name="action.cancel" class="me-2"/> Cancelar Orden
                    </button>
                    @endif
                    <a href="{{ route('business.orders.edit', $order) }}" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center">
                        <x-icon name="edit" class="me-2"/> Editar Descripción
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancelar Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('business.orders.cancel', $order) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-gray-600 mb-3">¿Estás seguro de cancelar esta orden?</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Motivo de Cancelación</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required placeholder="Explica el motivo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Cancelar Orden</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
