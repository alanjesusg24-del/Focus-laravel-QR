{{--
  Company: CETAM
  Project: FQR
  File: index.blade.php
  Created on: 05/08/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.business-app')

@section('title', 'Reportes - Sistema de Órdenes QR')

@section('page')
<div class="py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                        <x-icon name="nav.home" />
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Inicio</li>
            </ol>
            <h2 class="h4">Reportes y Análisis</h2>
            <p class="mb-0">Visualiza el rendimiento de tu negocio</p>
        </div>
    </div>

    {{-- Date Range Filter --}}
    <div class="card border-0 shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('business.dashboard.index') }}" class="row align-items-end g-3">
                <div class="col-12 col-md-4">
                    <label for="start_date" class="form-label">Fecha Inicio</label>
                    <input type="date"
                           class="form-control"
                           id="start_date"
                           name="start_date"
                           value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label for="end_date" class="form-label">Fecha Fin</label>
                    <input type="date"
                           class="form-control"
                           id="end_date"
                           name="end_date"
                           value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        Aplicar Filtro
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Metrics --}}
    <div class="row">
        {{-- Total Orders --}}
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row d-block d-xl-flex align-items-center">
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h6 text-gray-400 mb-0">Total Órdenes</h2>
                                <h3 class="fw-extrabold mb-2">{{ $reportData['total_orders'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completed Orders --}}
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row d-block d-xl-flex align-items-center">
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h6 text-gray-400 mb-0">Completadas</h2>
                                <h3 class="fw-extrabold mb-2">{{ $reportData['completed_orders'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cancelled Orders --}}
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row d-block d-xl-flex align-items-center">
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h6 text-gray-400 mb-0">Canceladas</h2>
                                <h3 class="fw-extrabold mb-2">{{ $reportData['cancelled_orders'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Average Time --}}
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row d-block d-xl-flex align-items-center">
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h6 text-gray-400 mb-0">Tiempo Promedio</h2>
                                <h3 class="fw-extrabold mb-2">{{ $reportData['avg_preparation_time'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Analysis Charts --}}
    <div class="row">
        {{-- Orders per Day --}}
        <div class="col-12 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Órdenes por Día</h2>
                </div>
                <div class="card-body">
                    <div id="ordersPerDayChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Statistics --}}
    <div class="row">
        {{-- Orders per Hour of the Day --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Órdenes por Hora del Día</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-flush align-items-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">Hora</th>
                                    <th class="border-0">Órdenes</th>
                                    <th class="border-0">Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData['orders_by_hour'] as $hour => $count)
                                <tr>
                                    <td class="fw-bold border-0">{{ sprintf('%02d:00', $hour) }} - {{ sprintf('%02d:59', $hour) }}</td>
                                    <td class="border-0">{{ $count }}</td>
                                    <td class="border-0">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2" style="min-width: 45px;">{{ $reportData['total_orders'] > 0 ? round(($count / $reportData['total_orders']) * 100, 1) : 0 }}%</span>
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar bg-primary"
                                                     style="width: {{ $reportData['total_orders'] > 0 ? ($count / $reportData['total_orders']) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No hay datos disponibles</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Orders per Day of the Week --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Órdenes por Día de la Semana</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">Día</th>
                                    <th class="border-0">Órdenes</th>
                                    <th class="border-0">Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['orders_by_weekday'] as $weekday => $count)
                                <tr>
                                    <td class="fw-bold border-0">{{ $weekday }}</td>
                                    <td class="border-0">{{ $count }}</td>
                                    <td class="border-0">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2" style="min-width: 45px;">{{ $reportData['total_orders'] > 0 ? round(($count / $reportData['total_orders']) * 100, 1) : 0 }}%</span>
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar bg-success"
                                                     style="width: {{ $reportData['total_orders'] > 0 ? ($count / $reportData['total_orders']) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Adoption Metrics --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Adopción de App Móvil</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <div class="text-center">
                                <h3 class="h2 fw-extrabold text-primary mb-2">{{ $reportData['linked_orders'] }}</h3>
                                <p class="text-gray-600 mb-0">Órdenes Ligadas</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <div class="text-center">
                                <h3 class="h2 fw-extrabold text-warning mb-2">{{ $reportData['unlinked_orders'] }}</h3>
                                <p class="text-gray-600 mb-0">Sin Ligar</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="text-center">
                                <h3 class="h2 fw-extrabold text-success mb-2">{{ $reportData['mobile_adoption_rate'] }}%</h3>
                                <p class="text-gray-600 mb-0">Tasa de Adopción</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ordersPerDayData = @json(array_values($reportData['orders_per_day']));
    const ordersPerDayCategories = @json(array_keys($reportData['orders_per_day']));

    console.log('Orders per day data:', ordersPerDayData);
    console.log('Orders per day categories:', ordersPerDayCategories);

    const ordersChartElement = document.querySelector("#ordersPerDayChart");
    if (ordersChartElement && ordersPerDayData.length > 0 && ordersPerDayData.some(val => val > 0)) {
        const ordersPerDayOptions = {
            series: [{
                name: 'Órdenes',
                data: ordersPerDayData
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            colors: ['#4f46e5'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
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
                categories: ordersPerDayCategories,
                labels: {
                    rotate: -45,
                    rotateAlways: ordersPerDayCategories.length > 7
                }
            },
            yaxis: {
                title: {
                    text: 'Órdenes'
                },
                labels: {
                    formatter: function(val) {
                        return Math.floor(val);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " órdenes";
                    }
                }
            }
        };

        try {
            const ordersPerDayChart = new ApexCharts(ordersChartElement, ordersPerDayOptions);
            ordersPerDayChart.render();
        } catch (error) {
            console.error('Error rendering orders chart:', error);
            ordersChartElement.innerHTML = '<div class="text-center py-5 text-danger"><p>Error al cargar gráfica</p></div>';
        }
    } else if (ordersChartElement) {
        ordersChartElement.innerHTML = '<div class="text-center py-5"><i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i><p class="text-muted">No hay órdenes en este período</p></div>';
    }
});
</script>

<style>
.dot {
    width: 8px;
    height: 8px;
}

.list-group-timeline .list-group-item:not(:last-child) {
    border-bottom: 1px solid #f0f1f3;
}

.icon-shape-xs {
    width: 2rem;
    height: 2rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
