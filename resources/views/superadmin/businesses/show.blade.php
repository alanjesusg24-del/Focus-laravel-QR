@extends('layouts.superadmin-app')

@section('title', 'Detalles del Negocio - ' . $business->business_name)

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
            <li class="breadcrumb-item"><a href="{{ route('superadmin.businesses.index') }}">Negocios</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $business->business_name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">{{ $business->business_name }}</h1>
            <p class="mb-0">Información detallada del negocio</p>
        </div>
        <div>
            <a href="{{ route('superadmin.businesses.edit', $business->business_id) }}" class="btn btn-warning d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                </svg>
                Editar Negocio
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Statistics -->
<div class="row">
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
                        <h2 class="h6 text-gray-400 mb-0">Total Órdenes</h2>
                        <h3 class="fw-extrabold mb-2">{{ $stats['total_orders'] }}</h3>
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
                        <div class="icon-shape icon-shape-warning rounded me-4 me-sm-0">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-12 col-xl-7 px-xl-0">
                        <h2 class="h6 text-gray-400 mb-0">Pendientes</h2>
                        <h3 class="fw-extrabold mb-2">{{ $stats['pending_orders'] }}</h3>
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
                        <div class="icon-shape icon-shape-success rounded me-4 me-sm-0">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-12 col-xl-7 px-xl-0">
                        <h2 class="h6 text-gray-400 mb-0">Entregadas</h2>
                        <h3 class="fw-extrabold mb-2">{{ $stats['delivered_orders'] }}</h3>
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
                        <h2 class="h6 text-gray-400 mb-0">Total Pagado</h2>
                        <h3 class="fw-extrabold mb-2">${{ number_format($stats['total_payments'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Business Information -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card border-0 shadow h-100">
            <div class="card-header">
                <h2 class="fs-5 fw-bold mb-0">Información del Negocio</h2>
            </div>
            <div class="card-body">
                @if($business->photo)
                    <div class="text-center mb-3">
                        <img src="{{ asset('storage/' . $business->photo) }}" class="rounded" style="max-width: 150px;" alt="{{ $business->business_name }}">
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label text-gray fw-bold">Nombre del Negocio</label>
                    <p>{{ $business->business_name }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-gray fw-bold">RFC</label>
                    <p>{{ $business->rfc ?? 'N/A' }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-gray fw-bold">Email</label>
                    <p>{{ $business->email }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-gray fw-bold">Teléfono</label>
                    <p>{{ $business->phone ?? 'N/A' }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-gray fw-bold">Dirección</label>
                    <p>{{ $business->address ?? 'N/A' }}</p>
                </div>

                @if($business->latitude && $business->longitude)
                    <div class="mb-3">
                        <label class="form-label text-gray fw-bold">Ubicación en el Mapa</label>
                        <div id="map" class="border rounded" style="height: 300px; width: 100%;"></div>
                        <small class="text-muted d-block mt-2">
                            <svg class="icon icon-xxs text-info me-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            Coordenadas: {{ number_format($business->latitude, 6) }}, {{ number_format($business->longitude, 6) }}
                        </small>
                    </div>
                @endif

                @if($business->location_description)
                    <div class="mb-3">
                        <label class="form-label text-gray fw-bold">Descripción de Ubicación</label>
                        <p>{{ $business->location_description }}</p>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label text-gray fw-bold">Estado</label>
                    <p>
                        @if($business->is_active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-gray fw-bold">Fecha de Registro</label>
                    <p>{{ $business->registration_date?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Information -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card border-0 shadow h-100">
            <div class="card-header">
                <h2 class="fs-5 fw-bold mb-0">Información del Plan</h2>
            </div>
            <div class="card-body">
                @if($business->plan)
                    <div class="mb-3">
                        <label class="form-label text-gray fw-bold">Plan Actual</label>
                        <p><span class="badge bg-secondary">{{ $business->plan->name }}</span></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-gray fw-bold">Precio Mensual</label>
                        <p>${{ number_format($business->plan->price, 2) }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-gray fw-bold">Duración</label>
                        <p>{{ $business->plan->duration_days }} días</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-gray fw-bold">Último Pago</label>
                        <p>{{ $business->last_payment_date?->format('d/m/Y') ?? 'Sin pagos' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-gray fw-bold">Características</label>
                        <ul class="list-unstyled">
                            @if($business->plan->has_qr_codes)
                                <li><svg class="icon icon-xs text-success me-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Códigos QR</li>
                            @endif
                            @if($business->plan->has_notifications)
                                <li><svg class="icon icon-xs text-success me-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Notificaciones</li>
                            @endif
                            @if($business->plan->has_reports)
                                <li><svg class="icon icon-xs text-success me-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Reportes</li>
                            @endif
                            @if($business->has_chat_module)
                                <li><svg class="icon icon-xs text-success me-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Módulo de Chat</li>
                            @endif
                        </ul>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <p class="mb-0">Este negocio no tiene un plan asignado</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="card border-0 shadow mb-4">
    <div class="card-header">
        <h2 class="fs-5 fw-bold mb-0">Órdenes Recientes</h2>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom">ID</th>
                    <th class="border-bottom">CÓDIGO</th>
                    <th class="border-bottom">CLIENTE</th>
                    <th class="border-bottom">TOTAL</th>
                    <th class="border-bottom">ESTADO</th>
                    <th class="border-bottom">FECHA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td>#{{ $order->order_id }}</td>
                        <td class="fw-bold">{{ $order->order_code }}</td>
                        <td>{{ $order->customer_name ?? 'N/A' }}</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge bg-warning">Pendiente</span>
                            @elseif($order->status === 'in_process')
                                <span class="badge bg-info">En Proceso</span>
                            @elseif($order->status === 'delivered')
                                <span class="badge bg-success">Entregada</span>
                            @else
                                <span class="badge bg-danger">Cancelada</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No hay órdenes registradas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Payments -->
<div class="card border-0 shadow">
    <div class="card-header">
        <h2 class="fs-5 fw-bold mb-0">Pagos Recientes</h2>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th class="border-bottom">ID</th>
                    <th class="border-bottom">MONTO</th>
                    <th class="border-bottom">MÉTODO</th>
                    <th class="border-bottom">ESTADO</th>
                    <th class="border-bottom">FECHA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPayments as $payment)
                    <tr>
                        <td>#{{ $payment->payment_id }}</td>
                        <td class="fw-bold">${{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                        <td>
                            @if($payment->status === 'completed')
                                <span class="badge bg-success">Completado</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning">Pendiente</span>
                            @else
                                <span class="badge bg-danger">Fallido</span>
                            @endif
                        </td>
                        <td>{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">No hay pagos registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@if($business->latitude && $business->longitude)
@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap" async defer></script>
<script>
function initMap() {
    const position = {
        lat: {{ $business->latitude }},
        lng: {{ $business->longitude }}
    };

    const map = new google.maps.Map(document.getElementById('map'), {
        center: position,
        zoom: 16,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true
    });

    new google.maps.Marker({
        position: position,
        map: map,
        title: '{{ $business->business_name }}'
    });
}
</script>
@endpush
@endif
