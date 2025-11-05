@extends('layouts.business-app')

@section('title', 'Orden ' . $order->folio_number)

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('business.orders.index') }}">Órdenes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $order->folio_number }}</li>
                </ol>
            </nav>
            <h2 class="h4">Detalle de Orden</h2>
            <p class="mb-0">Orden {{ $order->folio_number }}</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.orders.index') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a órdenes
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-12 col-xl-8 mb-4">
            <!-- Order Header Card -->
            <div class="card border-0 shadow mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="h5 mb-2">{{ $order->folio_number }}</h3>
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

            <!-- Description Card -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Descripción</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->description ?? 'Sin descripción' }}</p>
                </div>
            </div>

            <!-- Status Timeline Card -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Historial de Estados</h5>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-one-side">
                        <!-- Created -->
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

                        <!-- Ready -->
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

                        <!-- Delivered -->
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

                        <!-- Cancelled -->
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

        <!-- Sidebar -->
        <div class="col-12 col-xl-4">
            <!-- QR Code Card -->
            @if($order->qr_code_url)
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Código QR</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $order->qr_code_url }}" alt="QR Code" class="img-fluid rounded border mb-3" style="max-width: 250px;">
                    <a href="{{ route('business.orders.downloadQr', $order) }}" class="btn btn-outline-primary btn-sm w-100">
                        <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Descargar QR
                    </a>
                </div>
            </div>
            @endif

            <!-- Pickup Token Card -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Token de Recogida</h5>
                </div>
                <div class="card-body text-center">
                    <div class="bg-gray-200 rounded p-3 mb-2">
                        <h2 class="h3 mb-0 font-monospace text-primary">{{ $order->pickup_token }}</h2>
                    </div>
                    <small class="text-muted">El cliente debe mostrar este código al recoger</small>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    @if($order->status === 'pending')
                    <form action="{{ route('business.orders.markAsReady', $order) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Marcar como Listo
                        </button>
                    </form>
                    @endif

                    @if($order->status === 'ready')
                    <button type="button" class="btn btn-info w-100 mb-2" onclick="startQRScanner()">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z" clip-rule="evenodd"></path>
                            <path d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2v2a1 1 0 11-2 0v-3zM7 11a1 1 0 100-2H4a1 1 0 100 2h3zM17 13a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM16 17a1 1 0 100-2h-3a1 1 0 100 2h3z"></path>
                        </svg>
                        Escanear QR para Entregar
                    </button>
                    <button type="button" class="btn btn-outline-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#deliverModal">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                        Ingresar Token Manualmente
                    </button>
                    @endif

                    @if(in_array($order->status, ['pending', 'ready']))
                    <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Cancelar Orden
                    </button>
                    @endif

                    <a href="{{ route('business.orders.edit', $order) }}" class="btn btn-outline-gray-800 w-100">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                        Editar Descripción
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Mark as Delivered -->
<div class="modal fade" id="deliverModal" tabindex="-1" aria-labelledby="deliverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deliverModalLabel">Marcar como Entregado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.orders.markAsDelivered', $order) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-gray-600 mb-3">Ingresa el token de recogida que el cliente te proporcionó:</p>
                    <div class="mb-3">
                        <label for="pickup_token" class="form-label">Token de Recogida</label>
                        <input type="text" class="form-control font-monospace" id="pickup_token" name="pickup_token" required placeholder="Ingresa el token...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Confirmar Entrega</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Cancel Order -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancelar Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

<!-- Modal: QR Scanner -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="qrScannerModalLabel">
                    <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z" clip-rule="evenodd"></path>
                        <path d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2v2a1 1 0 11-2 0v-3zM7 11a1 1 0 100-2H4a1 1 0 100 2h3zM17 13a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM16 17a1 1 0 100-2h-3a1 1 0 100 2h3z"></path>
                    </svg>
                    Escanear Código QR del Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div id="scanner-status" class="mb-4">
                    <div class="spinner-border text-info mb-3" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted">Esperando escaneo del lector QR...</p>
                    <p class="small text-gray-500">Escanea el código QR que el cliente muestra en su celular</p>
                </div>
                <form id="qr-scanner-form" action="{{ route('business.orders.markAsDelivered', $order) }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="pickup_token" id="scanned-token">
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center py-5">
                <div class="icon-shape icon-lg bg-success text-white rounded-circle mx-auto mb-3">
                    <svg class="icon icon-lg" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="h4 text-success mb-2">¡Orden Entregada!</h3>
                <p class="text-muted mb-4">La orden ha sido marcada como entregada exitosamente</p>
                <button type="button" class="btn btn-success" onclick="window.location.reload()">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
let scannedData = '';
let isProcessing = false;
const orderStatus = '{{ $order->status }}';
const expectedToken = '{{ $order->pickup_token }}';

// Audio de confirmación
const successSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGmi78Oijmw==');

// Si la orden está lista, activar detección automática de escaneo
@if($order->status === 'ready')
document.addEventListener('DOMContentLoaded', function() {
    // Activar listener global para detectar escaneos automáticamente
    document.addEventListener('keypress', handleAutoQRInput);

    // Mostrar indicador visual de que está listo para escanear
    showReadyIndicator();
});

function showReadyIndicator() {
    // Agregar badge indicando que está listo para escanear
    const actionsCard = document.querySelector('.card-body');
    if (actionsCard) {
        const indicator = document.createElement('div');
        indicator.id = 'scan-ready-indicator';
        indicator.className = 'alert alert-info d-flex align-items-center mb-3';
        indicator.innerHTML = `
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z" clip-rule="evenodd"></path>
                <path d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2v2a1 1 0 11-2 0v-3zM7 11a1 1 0 100-2H4a1 1 0 100 2h3zM17 13a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM16 17a1 1 0 100-2h-3a1 1 0 100 2h3z"></path>
            </svg>
            <div class="flex-grow-1">
                <strong>Listo para entregar</strong><br>
                <small>Escanea el QR del cliente para marcar como entregado automáticamente</small>
            </div>
        `;
        actionsCard.insertBefore(indicator, actionsCard.firstChild);
    }
}

function handleAutoQRInput(e) {
    // Ignorar si hay un modal abierto o un input enfocado
    if (document.querySelector('.modal.show') ||
        document.activeElement.tagName === 'INPUT' ||
        document.activeElement.tagName === 'TEXTAREA' ||
        isProcessing) {
        return;
    }

    // Los lectores QR típicamente envían Enter al final
    if (e.key === 'Enter') {
        if (scannedData.length > 0) {
            processScannedToken(scannedData.trim());
            scannedData = '';
        }
    } else {
        scannedData += e.key;
    }
}
@endif

function startQRScanner() {
    scannedData = '';

    // Mostrar modal de escáner
    const modal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
    modal.show();
}

function processScannedToken(scannedValue) {
    if (isProcessing) return;
    isProcessing = true;

    // Cerrar modal de escáner si está abierto
    const scannerModal = bootstrap.Modal.getInstance(document.getElementById('qrScannerModal'));
    if (scannerModal) {
        scannerModal.hide();
    }

    // Extraer el token del valor escaneado
    // Puede ser una URL como: https://app.example.com/order/scan/QR_TOKEN
    // O simplemente el QR_TOKEN directo
    let qrToken = scannedValue;

    // Si es una URL, extraer el token (última parte de la ruta)
    if (scannedValue.includes('/')) {
        const parts = scannedValue.split('/');
        qrToken = parts[parts.length - 1];
    }

    // Limpiar cualquier parámetro de query string o espacios
    qrToken = qrToken.split('?')[0].trim();

    console.log('QR Token escaneado:', qrToken);
    console.log('QR Token de esta orden:', '{{ $order->qr_token }}');

    // Verificar si el qr_token escaneado corresponde a esta orden
    if (qrToken === '{{ $order->qr_token }}') {
        // QR correcto - enviar formulario con el pickup_token
        document.getElementById('scanned-token').value = expectedToken;

        // Reproducir sonido de éxito
        successSound.play().catch(e => console.log('Audio no disponible'));

        // Mostrar modal de éxito
        showSuccessAnimation();

        // Enviar formulario después de un breve delay
        setTimeout(() => {
            document.getElementById('qr-scanner-form').submit();
        }, 1500);
    } else {
        // QR incorrecto - solo mostrar si se estaba usando el modal
        isProcessing = false;
        if (scannerModal) {
            playErrorSound();
            showError('Código QR incorrecto. El QR no corresponde a esta orden.');
            setTimeout(() => {
                startQRScanner();
            }, 2000);
        }
        // Si es detección automática, simplemente resetear sin mostrar error
        scannedData = '';
    }
}

function showSuccessAnimation() {
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
}

function showError(message) {
    alert(message);
}

function playErrorSound() {
    // Beep de error
    const errorSound = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU' +
        'vT19eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eX');
    errorSound.play().catch(e => console.log('Audio no disponible'));
}

// Auto-submit del formulario manual de entrega con confirmación
document.querySelector('#deliverModal form')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const token = document.getElementById('pickup_token').value;
    const expectedToken = '{{ $order->pickup_token }}';

    if (token === expectedToken) {
        successSound.play().catch(e => console.log('Audio no disponible'));
        showSuccessAnimation();
        setTimeout(() => {
            this.submit();
        }, 1500);
    } else {
        playErrorSound();
        alert('Token incorrecto. Por favor verifica el código.');
    }
});

// Reproducir sonido cuando se marca como listo
@if(session('success') && str_contains(session('success'), 'lista'))
successSound.play().catch(e => console.log('Audio no disponible'));
@endif
</script>

<style>
.icon-shape {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 4rem;
    height: 4rem;
}

#qrScannerModal .modal-body {
    min-height: 200px;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

#scanner-status .spinner-border {
    animation: pulse 1.5s ease-in-out infinite;
}
</style>
@endsection
