





@extends('layouts.business-app')

@section('title', 'Reportes - Sistema de Órdenes QR')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item">
                            <a href="#"><x-icon name="home" /></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
            <h2 class="h4">Reportes y Análisis</h2>
            <p class="mb-0">Visualiza el rendimiento de tu negocio</p>
        </div>
    </div>

    <!-- Filtro de Rango de Fechas -->
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

    <!-- Métricas Principales -->
    <div class="row">
        <!-- Total de Órdenes -->
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

        <!-- Órdenes Completadas -->
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

        <!-- Órdenes Canceladas -->
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

        <!-- Tiempo Promedio -->
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

    <!-- Gráficas de Análisis -->
    <div class="row">
        <!-- Órdenes por Día -->
        <div class="col-12 col-lg-7 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Órdenes por Día</h2>
                </div>
                <div class="card-body">
                    <div id="ordersPerDayChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Distribución por Estado -->
        <div class="col-12 col-lg-5 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Distribución por Estado</h2>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="statusDistributionChart" style="min-height: 350px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Adicionales -->
    <div class="row">
        <!-- Órdenes por Hora del Día -->
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

        <!-- Órdenes por Día de la Semana -->
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

    <!-- Métricas de Adopción Móvil -->
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
    const statusDistributionData = @json(array_values($reportData['status_distribution']));
    const statusDistributionLabels = @json(array_keys($reportData['status_distribution']));

    console.log('Orders per day data:', ordersPerDayData);
    console.log('Orders per day categories:', ordersPerDayCategories);
    console.log('Status distribution data:', statusDistributionData);
    console.log('Status distribution labels:', statusDistributionLabels);

    // Gráfica de Órdenes por Día
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

    // Gráfica de Distribución por Estado
    const statusChartElement = document.querySelector("#statusDistributionChart");
    const totalStatusOrders = statusDistributionData.reduce((a, b) => a + b, 0);

    console.log('Total status orders:', totalStatusOrders);

    if (statusChartElement && totalStatusOrders > 0) {
        // Filtrar solo estados con órdenes
        const filteredData = [];
        const filteredLabels = [];
        const filteredColors = [];
        const labelMap = {
            'pending': 'Pendientes',
            'ready': 'Listas',
            'delivered': 'Entregadas',
            'cancelled': 'Canceladas'
        };

        // Colores según estándares CETAM
        const colorMap = {
            'pending': '#FBA918',    // Ámbar (Warning)
            'ready': '#10B981',      // Verde (Success)
            'delivered': '#3B82F6',  // Azul (Info)
            'cancelled': '#EF4444'   // Rojo (Danger)
        };

        statusDistributionLabels.forEach((label, index) => {
            if (statusDistributionData[index] > 0) {
                filteredData.push(statusDistributionData[index]);
                filteredLabels.push(labelMap[label] || label);
                filteredColors.push(colorMap[label] || '#6B7280');
            }
        });

        const statusDistributionOptions = {
            series: filteredData,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: filteredLabels,
            colors: filteredColors,
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            }
        };

        try {
            const statusDistributionChart = new ApexCharts(statusChartElement, statusDistributionOptions);
            statusDistributionChart.render();
        } catch (error) {
            console.error('Error rendering status chart:', error);
            statusChartElement.innerHTML = '<div class="text-center py-5 text-danger"><p>Error al cargar gráfica</p></div>';
        }
    } else if (statusChartElement) {
        statusChartElement.innerHTML = '<div class="text-center py-5"><i class="fas fa-chart-pie fa-3x text-gray-300 mb-3"></i><p class="text-muted">No hay órdenes en este período</p></div>';
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
