@extends('layouts.superadmin-app')

@section('title', 'Reportes y Estadísticas')

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
            <li class="breadcrumb-item active" aria-current="page">Reportes</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Reportes y Estadísticas</h1>
            <p class="mb-0">Dashboard de métricas generales del sistema</p>
        </div>
    </div>
</div>

<!-- Stats Cards Row 1 -->
<div class="row">
    <div class="col-12 col-sm-6 col-xl-4 mb-4">
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
                            <h2 class="h6 text-gray-400 mb-0">Negocios Totales</h2>
                            <h3 class="fw-extrabold mb-2">{{ number_format($totalBusinesses) }}</h3>
                        </div>
                        <small class="d-flex align-items-center text-gray-500">
                            <svg class="icon icon-xxs text-success me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $activeBusinesses }} activos
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4 mb-4">
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
                            <h2 class="h6 text-gray-400 mb-0">Órdenes Totales</h2>
                            <h3 class="fw-extrabold mb-2">{{ number_format($totalOrders) }}</h3>
                        </div>
                        <small class="text-gray-500">
                            Del sistema completo
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4 mb-4">
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
                            <h3 class="fw-extrabold mb-2">${{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                        <small class="text-gray-500">
                            Pagos completados
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Orders by Status -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Órdenes por Estado</h2>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-warning">Pendientes</span>
                                    <span class="fw-bold">{{ $ordersByStatus['pending'] ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalOrders > 0 ? (($ordersByStatus['pending'] ?? 0) / $totalOrders * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-info">Listas</span>
                                    <span class="fw-bold">{{ $ordersByStatus['ready'] ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalOrders > 0 ? (($ordersByStatus['ready'] ?? 0) / $totalOrders * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-success">Entregadas</span>
                                    <span class="fw-bold">{{ $ordersByStatus['delivered'] ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalOrders > 0 ? (($ordersByStatus['delivered'] ?? 0) / $totalOrders * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="w-100">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-danger">Canceladas</span>
                                    <span class="fw-bold">{{ $ordersByStatus['cancelled'] ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $totalOrders > 0 ? (($ordersByStatus['cancelled'] ?? 0) / $totalOrders * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Tickets -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Tickets de Soporte</h2>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="card bg-warning-soft border-0">
                            <div class="card-body py-4">
                                <h2 class="h3 fw-extrabold mb-0">{{ $openTickets }}</h2>
                                <span class="text-gray-600">Abiertos</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card bg-success-soft border-0">
                            <div class="card-body py-4">
                                <h2 class="h3 fw-extrabold mb-0">{{ $resolvedTickets }}</h2>
                                <span class="text-gray-600">Resueltos</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalTickets > 0 ? ($openTickets / $totalTickets * 100) : 0 }}%">
                        {{ $totalTickets > 0 ? round($openTickets / $totalTickets * 100) : 0 }}%
                    </div>
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalTickets > 0 ? ($resolvedTickets / $totalTickets * 100) : 0 }}%">
                        {{ $totalTickets > 0 ? round($resolvedTickets / $totalTickets * 100) : 0 }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue by Month -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Ingresos por Mes (Últimos 6 meses)</h2>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($revenueByMonth as $revenue)
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <div class="h6 fw-bold text-gray-400 mb-1">{{ \Carbon\Carbon::parse($revenue->month . '-01')->format('M Y') }}</div>
                                <div class="h4 fw-extrabold text-success">${{ number_format($revenue->total, 2) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-gray-500">
                            <p>No hay datos de ingresos disponibles</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Businesses -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Top 5 Negocios con Más Órdenes</h2>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-bottom" scope="col">#</th>
                            <th class="border-bottom" scope="col">NEGOCIO</th>
                            <th class="border-bottom" scope="col">TOTAL ÓRDENES</th>
                            <th class="border-bottom" scope="col">PLAN</th>
                            <th class="border-bottom" scope="col">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topBusinesses as $index => $business)
                            <tr>
                                <td class="fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($business->photo)
                                            <img src="{{ asset('storage/' . $business->photo) }}" class="avatar rounded-circle me-3" alt="{{ $business->business_name }}">
                                        @else
                                            <div class="avatar rounded-circle bg-secondary me-3 d-flex align-items-center justify-content-center">
                                                <span class="text-white fw-bold">{{ substr($business->business_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="fw-bold">{{ $business->business_name }}</span>
                                    </div>
                                </td>
                                <td class="fw-bold text-primary">{{ number_format($business->orders_count) }}</td>
                                <td>
                                    @if($business->plan)
                                        <span class="badge bg-secondary">{{ $business->plan->name }}</span>
                                    @else
                                        <span class="text-muted">Sin plan</span>
                                    @endif
                                </td>
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
                                <td colspan="5" class="text-center py-4 text-gray-500">
                                    No hay datos disponibles
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
