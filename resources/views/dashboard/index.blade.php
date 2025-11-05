@extends('layouts.business-app')

@section('title', 'Dashboard - Order QR System')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Dashboard</h2>
            <p class="mb-0">Resumen de tu negocio y órdenes</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.orders.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Orden
            </a>
        </div>
    </div>

    <!-- Chat Module Notification (si está activado) -->
    @if(auth()->guard('business')->user()->has_chat_module ?? false)
    <div class="alert alert-info d-flex align-items-center" role="alert">
        <svg class="icon icon-xs me-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
        </svg>
        <div>
            <strong>Módulo de Chat Activado</strong> - Haz clic en el botón flotante de chat en la esquina inferior derecha para interactuar con tus clientes en tiempo real. (+$150 MXN/mes)
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total de Órdenes -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row d-block d-xl-flex align-items-center">
                        <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                            <div class="icon-shape icon-shape-primary rounded me-4 me-sm-0">
                                <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h6 text-gray-400 mb-0">Total Órdenes</h2>
                                <h3 class="fw-extrabold mb-2">{{ $orderStats['total'] ?? 0 }}</h3>
                            </div>
                            <small class="d-flex align-items-center text-gray-500">
                                Últimos {{ $days ?? 30 }} días
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Órdenes Pendientes -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row d-block d-xl-flex align-items-center">
                        <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                            <div class="icon-shape icon-shape-warning rounded me-4 me-sm-0">
                                <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h6 text-gray-400 mb-0">Pendientes</h2>
                                <h3 class="fw-extrabold mb-2">{{ $orderStats['pending'] ?? 0 }}</h3>
                            </div>
                            <small class="d-flex align-items-center text-gray-500">
                                En preparación
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Órdenes Listas -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row d-block d-xl-flex align-items-center">
                        <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                            <div class="icon-shape icon-shape-success rounded me-4 me-sm-0">
                                <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h6 text-gray-400 mb-0">Listas</h2>
                                <h3 class="fw-extrabold mb-2">{{ $orderStats['ready'] ?? 0 }}</h3>
                            </div>
                            <small class="d-flex align-items-center text-gray-500">
                                Para recoger
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tiempo Promedio -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row d-block d-xl-flex align-items-center">
                        <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                            <div class="icon-shape icon-shape-danger rounded me-4 me-sm-0">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h6 text-gray-400 mb-0">Tiempo Promedio</h2>
                                <h3 class="fw-extrabold mb-2">
                                    {{ isset($orderStats['avg_preparation_time']) && $orderStats['avg_preparation_time'] ? number_format($orderStats['avg_preparation_time'], 0) : '--' }}
                                </h3>
                            </div>
                            <small class="d-flex align-items-center text-gray-500">
                                Minutos
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Órdenes Activas Table -->
    <div class="card border-0 shadow mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">Órdenes Activas</h2>
                </div>
                <div class="col text-end">
                    <a href="{{ route('business.orders.index') }}" class="btn btn-sm btn-secondary">Ver todas</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="border-bottom" scope="col">Folio</th>
                        <th class="border-bottom" scope="col">Descripción</th>
                        <th class="border-bottom" scope="col">Estado</th>
                        <th class="border-bottom" scope="col">Creada</th>
                        <th class="border-bottom" scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeOrders ?? [] as $order)
                    <tr>
                        <td class="fw-bolder text-gray-500">{{ $order->folio_number }}</td>
                        <td class="text-gray-900">{{ Str::limit($order->description, 50) }}</td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge bg-warning">Pendiente</span>
                            @elseif($order->status === 'ready')
                                <span class="badge bg-success">Listo</span>
                            @endif
                        </td>
                        <td class="text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('business.orders.show', $order) }}" class="btn btn-sm btn-primary">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            No hay órdenes activas en este momento
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Estado del Plan y Órdenes Recientes -->
    <div class="row">
        <!-- Estado del Plan -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="fs-5 fw-bold mb-0">Tu Plan Actual</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Plan:</h3>
                            </div>
                            <div>
                                <span class="fw-bold">{{ $business->plan->name ?? 'N/A' }}</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Precio:</h3>
                            </div>
                            <div>
                                <span class="fw-bold">${{ number_format($business->plan->price ?? 0, 2) }} MXN</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <div>
                                <h3 class="h6 mb-1">Último Pago:</h3>
                            </div>
                            <div>
                                <span class="fw-bold">
                                    {{ $business->last_payment_date ? $business->last_payment_date->format('d/m/Y') : 'Pendiente' }}
                                </span>
                            </div>
                        </li>
                    </ul>

                    @if($paymentExpired ?? false)
                        <div class="alert alert-danger mt-3">
                            <strong>¡Tu pago ha vencido!</strong>
                        </div>
                        <a href="{{ route('business.payments.index') }}" class="btn btn-danger w-100">
                            Renovar Ahora
                        </a>
                    @else
                        <a href="{{ route('business.payments.index') }}" class="btn btn-outline-primary w-100 mt-3">
                            Ver Historial de Pagos
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Órdenes Recientes -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="fs-5 fw-bold mb-0">Órdenes Recientes</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush list my--3">
                        @foreach($recentOrders ?? [] as $order)
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="icon-shape icon-shape-sm icon-shape-primary rounded">
                                        <svg class="icon icon-xs text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="col ms--2">
                                    <h4 class="h6 mb-0">{{ $order->folio_number }}</h4>
                                    <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('business.orders.show', $order) }}" class="btn btn-sm btn-link text-primary">Ver</a>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
