@extends('layouts.superadmin-app')

@section('title', 'Dashboard - Super Administrador')

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="#">
                    <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Panel de Super Administrador</h1>
            <p class="mb-0">Bienvenido al panel de control general del sistema Order QR</p>
        </div>
    </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Statistics Cards -->
<div class="row">
    <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
            <div class="card-body">
                <div class="row d-block d-xl-flex align-items-center">
                    <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                        <div class="icon-shape icon-shape-primary rounded me-4 me-sm-0">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-12 col-xl-7 px-xl-0">
                        <div class="d-none d-sm-block">
                            <h2 class="h6 text-gray-400 mb-0">Total Negocios</h2>
                            <h3 class="fw-extrabold mb-2">{{ $stats['total_businesses'] }}</h3>
                        </div>
                        <small class="text-gray-500">
                            <svg class="icon icon-xxs text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $stats['active_businesses'] }} Activos
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
            <div class="card-body">
                <div class="row d-block d-xl-flex align-items-center">
                    <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                        <div class="icon-shape icon-shape-secondary rounded me-4 me-sm-0">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-12 col-xl-7 px-xl-0">
                        <div class="d-none d-sm-block">
                            <h2 class="h6 text-gray-400 mb-0">Total Órdenes</h2>
                            <h3 class="fw-extrabold mb-2">{{ number_format($stats['total_orders']) }}</h3>
                        </div>
                        <small class="text-gray-500">
                            <svg class="icon icon-xxs text-warning" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $stats['pending_orders'] }} Pendientes
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
            <div class="card-body">
                <div class="row d-block d-xl-flex align-items-center">
                    <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                        <div class="icon-shape icon-shape-tertiary rounded me-4 me-sm-0">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-12 col-xl-7 px-xl-0">
                        <div class="d-none d-sm-block">
                            <h2 class="h6 text-gray-400 mb-0">Ingresos Totales</h2>
                            <h3 class="fw-extrabold mb-2">${{ number_format($stats['total_payments'], 2) }}</h3>
                        </div>
                        <small class="text-success">
                            <svg class="icon icon-xxs text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                            </svg>
                            Desde el inicio
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
            <div class="card-body">
                <div class="row d-block d-xl-flex align-items-center">
                    <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                        <div class="icon-shape icon-shape-danger rounded me-4 me-sm-0">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-12 col-xl-7 px-xl-0">
                        <div class="d-none d-sm-block">
                            <h2 class="h6 text-gray-400 mb-0">Tickets Abiertos</h2>
                            <h3 class="fw-extrabold mb-2">{{ $stats['open_tickets'] }}</h3>
                        </div>
                        <small class="text-gray-500">
                            <svg class="icon icon-xxs text-info" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $stats['pending_tickets'] }} Pendientes
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Revenue Chart -->
    <div class="col-12 col-xl-8 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h2 class="fs-5 fw-bold mb-0">Ingresos por Mes</h2>
                <span class="badge bg-primary">Últimos 6 meses</span>
            </div>
            <div class="card-body">
                <div id="revenueChart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Orders by Status Chart -->
    <div class="col-12 col-xl-4 mb-4">
        <div class="card border-0 shadow h-100">
            <div class="card-header border-bottom">
                <h2 class="fs-5 fw-bold mb-0">Distribución de Órdenes</h2>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div id="ordersStatusChart" style="min-height: 300px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Top Businesses by Revenue & Orders -->
<div class="row">
    <div class="col-12 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header border-bottom">
                <h2 class="fs-5 fw-bold mb-0">Top Negocios por Ingresos</h2>
            </div>
            <div class="card-body">
                @forelse($topBusinessesByRevenue as $business)
                    <div class="row mb-3 align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <div class="icon-shape icon-sm icon-shape-primary rounded me-3">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $business->business_name }}</h6>
                                    <small class="text-gray-500">{{ $business->email }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-success">${{ number_format($business->total_revenue, 2) }}</span>
                        </div>
                    </div>
                    @if(!$loop->last)
                    <hr class="my-2">
                    @endif
                @empty
                    <div class="text-center py-4">
                        <p class="text-gray-500 mb-0">No hay datos de ingresos disponibles</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Businesses by Orders -->
    <div class="col-12 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header border-bottom">
                <h2 class="fs-5 fw-bold mb-0">Top Negocios por Órdenes</h2>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-bottom" scope="col">#</th>
                            <th class="border-bottom" scope="col">NEGOCIO</th>
                            <th class="border-bottom" scope="col">ÓRDENES</th>
                            <th class="border-bottom" scope="col">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topBusinessesByOrders as $index => $business)
                            <tr>
                                <td class="fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($business->photo)
                                            <img src="{{ asset('storage/' . $business->photo) }}" class="avatar rounded-circle me-2" alt="{{ $business->business_name }}">
                                        @else
                                            <div class="avatar rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <span class="text-white fw-bold small">{{ substr($business->business_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="fw-bold">{{ Str::limit($business->business_name, 20) }}</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary">{{ number_format($business->orders_count) }}</span></td>
                                <td>
                                    @if($business->is_active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-500">No hay datos disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Breakdown & Support Tickets -->
<div class="row">
    <!-- Order Status Breakdown -->
    <div class="col-12 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Órdenes por Estado</h2>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Pendientes</h3>
                            <p class="small pe-4 mb-0">Órdenes esperando procesamiento</p>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark">{{ $stats['pending_orders'] }}</span>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Listas</h3>
                            <p class="small pe-4 mb-0">Órdenes preparadas</p>
                        </div>
                        <div>
                            <span class="badge bg-info">{{ $stats['in_process_orders'] }}</span>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Entregadas</h3>
                            <p class="small pe-4 mb-0">Órdenes completadas exitosamente</p>
                        </div>
                        <div>
                            <span class="badge bg-success">{{ $stats['delivered_orders'] }}</span>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <div>
                            <h3 class="h6 mb-1">Canceladas</h3>
                            <p class="small pe-4 mb-0">Órdenes canceladas</p>
                        </div>
                        <div>
                            <span class="badge bg-danger">{{ $stats['cancelled_orders'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Tickets Stats -->
    <div class="col-12 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Estado de Tickets de Soporte</h2>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('superadmin.tickets.index') }}" class="btn btn-sm btn-primary">Ver Todos</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="card bg-warning-soft border-0 mb-0">
                            <div class="card-body py-4">
                                <h2 class="h3 fw-extrabold mb-0 text-warning">{{ $stats['open_tickets'] }}</h2>
                                <span class="text-gray-600">Abiertos</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-success-soft border-0 mb-0">
                            <div class="card-body py-4">
                                <h2 class="h3 fw-extrabold mb-0 text-success">{{ $stats['resolved_tickets'] }}</h2>
                                <span class="text-gray-600">Resueltos</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($stats['total_tickets'] > 0)
                <div class="progress" style="height: 12px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ round($stats['open_tickets'] / $stats['total_tickets'] * 100) }}%">
                        {{ round($stats['open_tickets'] / $stats['total_tickets'] * 100) }}%
                    </div>
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ round($stats['resolved_tickets'] / $stats['total_tickets'] * 100) }}%">
                        {{ round($stats['resolved_tickets'] / $stats['total_tickets'] * 100) }}%
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted">{{ $stats['open_tickets'] }} abiertos</small>
                    <small class="text-muted">{{ $stats['resolved_tickets'] }} resueltos</small>
                </div>
                @else
                <div class="text-center text-gray-500">
                    <p class="mb-0">No hay tickets registrados</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Businesses -->
<div class="card border-0 shadow mb-4">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
        <h2 class="fs-5 fw-bold mb-0">Negocios Registrados Recientemente</h2>
        <a href="{{ route('superadmin.businesses.index') }}" class="btn btn-sm btn-primary">Ver Todos</a>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom" scope="col">NEGOCIO</th>
                    <th class="border-bottom" scope="col">EMAIL</th>
                    <th class="border-bottom" scope="col">PLAN</th>
                    <th class="border-bottom" scope="col">FECHA REGISTRO</th>
                    <th class="border-bottom" scope="col">ESTADO</th>
                    <th class="border-bottom" scope="col">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBusinesses as $business)
                    <tr>
                        <td class="fw-bold">{{ $business->business_name }}</td>
                        <td>{{ $business->email }}</td>
                        <td><span class="badge bg-secondary">{{ $business->plan->plan_name ?? 'Sin plan' }}</span></td>
                        <td>{{ $business->registration_date?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td>
                            @if($business->is_active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('superadmin.businesses.show', $business->business_id) }}" class="btn btn-sm btn-info">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No hay negocios registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Support Tickets -->
<div class="card border-0 shadow">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
        <h2 class="fs-5 fw-bold mb-0">Tickets de Soporte Recientes</h2>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom" scope="col">ID</th>
                    <th class="border-bottom" scope="col">NEGOCIO</th>
                    <th class="border-bottom" scope="col">ASUNTO</th>
                    <th class="border-bottom" scope="col">ESTADO</th>
                    <th class="border-bottom" scope="col">FECHA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTickets as $ticket)
                    <tr>
                        <td class="fw-bold">#{{ $ticket->ticket_id }}</td>
                        <td>{{ $ticket->business->business_name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($ticket->subject, 50) }}</td>
                        <td>
                            @if($ticket->status === 'open')
                                <span class="badge bg-warning">Abierto</span>
                            @elseif($ticket->status === 'pending')
                                <span class="badge bg-info">Pendiente</span>
                            @else
                                <span class="badge bg-success">Cerrado</span>
                            @endif
                        </td>
                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">No hay tickets de soporte</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ApexCharts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueData = @json($monthlyRevenue);
    const revenueCategories = revenueData.map(item => {
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
    });
    const revenueValues = revenueData.map(item => parseFloat(item.total));

    const revenueChartEl = document.querySelector('#revenueChart');
    if (revenueChartEl && revenueValues.length > 0) {
        const revenueOptions = {
            series: [{
                name: 'Ingresos',
                data: revenueValues
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#262B40'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                }
            },
            xaxis: {
                categories: revenueCategories
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return '$' + val.toFixed(2);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return '$' + val.toFixed(2);
                    }
                }
            }
        };
        const revenueChart = new ApexCharts(revenueChartEl, revenueOptions);
        revenueChart.render();
    }

    // Orders Status Donut Chart
    const ordersStatusEl = document.querySelector('#ordersStatusChart');
    if (ordersStatusEl) {
        const statusOptions = {
            series: [
                {{ $stats['pending_orders'] }},
                {{ $stats['in_process_orders'] }},
                {{ $stats['delivered_orders'] }},
                {{ $stats['cancelled_orders'] }}
            ],
            chart: {
                type: 'donut',
                height: 300
            },
            labels: ['Pendientes', 'Listas', 'Entregadas', 'Canceladas'],
            colors: ['#ffc107', '#17a2b8', '#28a745', '#dc3545'],
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%'
                    }
                }
            }
        };
        const statusChart = new ApexCharts(ordersStatusEl, statusOptions);
        statusChart.render();
    }
});
</script>
@endsection
