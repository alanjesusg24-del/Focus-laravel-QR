@extends('layouts.superadmin-app')

@section('title', 'Dashboard - Super Administrador')

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="#">
                    <x-icon name="home" />
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
        <x-icon name="success" class="me-2" />
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <x-icon name="error" class="me-2" />
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Statistics Cards removidas para estilo minimalista --}}

<!-- Charts Row -->
<div class="row">
    <!-- Revenue Chart -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h2 class="fs-5 fw-bold mb-0">Ingresos por Mes</h2>
                <span class="fw-bold text-primary">Últimos 6 meses</span>
            </div>
            <div class="card-body">
                <div id="revenueChart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Top Businesses by Orders -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header border-bottom">
                <h2 class="fs-5 fw-bold mb-0">Top Negocios por Órdenes</h2>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-bottom" scope="col">#</th>
                            <th class="border-bottom" scope="col">Negocio</th>
                            <th class="border-bottom" scope="col">Órdenes</th>
                            <th class="border-bottom" scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topBusinessesByOrders as $index => $business)
                            <tr>
                                <td class="text-gray-500">{{ $index + 1 }}</td>
                                <td class="fw-bolder text-gray-500">{{ Str::limit($business->business_name, 25) }}</td>
                                <td class="fw-bold text-primary">{{ number_format($business->orders_count) }}</td>
                                <td>
                                    @if($business->is_active)
                                        <span class="fw-bold text-success">Activo</span>
                                    @else
                                        <span class="fw-bold text-danger">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <p class="text-gray-600 mb-0">No hay datos disponibles</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de órdenes por estado removida para estilo minimalista --}}

<!-- Recent Businesses -->
<div class="card border-0 shadow mb-4">
    <div class="card-header">
        <h2 class="fs-5 fw-bold mb-0">Negocios Registrados Recientemente</h2>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom" scope="col">Negocio</th>
                    <th class="border-bottom" scope="col">Email</th>
                    <th class="border-bottom" scope="col">Plan</th>
                    <th class="border-bottom" scope="col">Fecha Registro</th>
                    <th class="border-bottom" scope="col">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBusinesses as $business)
                    <tr>
                        <td class="fw-bolder text-gray-500">{{ $business->business_name }}</td>
                        <td class="text-gray-900">{{ $business->email }}</td>
                        <td class="fw-bold text-secondary">{{ $business->plan->name ?? 'Sin plan' }}</td>
                        <td class="text-gray-500">{{ $business->registration_date?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td>
                            @if($business->is_active)
                                <span class="fw-bold text-success">Activo</span>
                            @else
                                <span class="fw-bold text-danger">Inactivo</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <p class="text-gray-600 mb-0">No hay negocios registrados</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- DESACTIVADO: Recent Support Tickets --}}
{{-- <div class="card border-0 shadow">
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
</div> --}}

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
            colors: ['#1F2937'],
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

});
</script>
@endsection
