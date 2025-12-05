@extends('layouts.business-app')

@section('title', 'Mi Perfil')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        {{-- Enlace con color Primario forzado --}}
                        <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                            <x-icon name="home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
                </ol>
            </nav>
            <h2 class="h4">Mi Perfil</h2>
            <p class="mb-0 text-muted">Administra la información de tu negocio</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.profile.edit') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <x-icon name="edit" class="me-2" />
                Editar Perfil
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <x-icon name="success" class="me-2" />
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Main Profile Info -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Información del Negocio</h5>
                    @if($business->is_active)
                        <span class="badge bg-success">Cuenta Activa</span>
                    @else
                        <span class="badge bg-danger">Cuenta Inactiva</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-gray-600 small mb-1">Nombre del Negocio</label>
                                <p class="h6 mb-0">{{ $business->business_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-gray-600 small mb-1">RFC</label>
                                <p class="h6 mb-0">{{ $business->rfc }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-gray-600 small mb-1">Email</label>
                                <p class="h6 mb-0">{{ $business->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-gray-600 small mb-1">Teléfono</label>
                                <p class="h6 mb-0">{{ $business->phone }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-0">
                                <label class="text-gray-600 small mb-1">Dirección</label>
                                <p class="h6 mb-0">{{ $business->address ?? 'No especificada' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plan Information -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Información del Plan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm text-white rounded me-3" style="background-color: #1F2937;">
                                    <x-icon name="list" />
                                </div>
                                <div>
                                    <label class="text-gray-600 small mb-0 d-block">Plan Actual</label>
                                    <p class="h6 mb-0">{{ $business->plan->name ?? 'Sin plan' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm text-white rounded me-3" style="background-color: #10B981;">
                                    <x-icon name="dollar" />
                                </div>
                                <div>
                                    <label class="text-gray-600 small mb-0 d-block">Precio Mensual</label>
                                    <p class="h6 mb-0">${{ number_format($business->monthly_price ?? 0, 2) }} MXN</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm text-white rounded me-3" style="background-color: {{ $business->has_chat_module ? '#3B82F6' : '#6B7280' }};">
                                    <x-icon name="comments" />
                                </div>
                                <div>
                                    <label class="text-gray-600 small mb-0 d-block">Módulo de Chat</label>
                                    <p class="mb-0">
                                        @if($business->has_chat_module)
                                            <span class="badge" style="background-color: #10B981;">Activado</span>
                                        @else
                                            <span class="badge bg-secondary">No activado</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm text-white rounded me-3" style="background-color: #FBA918;">
                                    <x-icon name="clock" />
                                </div>
                                <div>
                                    <label class="text-gray-600 small mb-0 d-block">Retención de Datos</label>
                                    <p class="h6 mb-0">{{ $business->data_retention_months ?? 1 }} {{ $business->data_retention_months == 1 ? 'mes' : 'meses' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Location Map -->
            <div class="card border-0 shadow">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Ubicación del Negocio</h5>
                </div>
                <div class="card-body">
                    @if($business->latitude && $business->longitude)
                        <div id="map" style="height: 350px; width: 100%; border-radius: 0.5rem;" class="mb-3"></div>
                        @if($business->location_description)
                        <div class="d-flex align-items-start">
                            <x-icon name="mapPin" class="text-gray-600 me-2 mt-1" />
                            <p class="text-gray-700 mb-0">{{ $business->location_description }}</p>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="icon icon-shape icon-lg bg-gray-200 text-gray-600 rounded-circle mb-3 mx-auto">
                                <x-icon name="mapPin" />
                            </div>
                            <p class="text-gray-600 mb-3">No has configurado tu ubicación aún</p>
                            <a href="{{ route('business.profile.edit') }}" class="btn btn-sm btn-primary">
                                <x-icon name="mapPin" class="me-1" />
                                Configurar Ubicación
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-xl-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('business.profile.edit') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <x-icon name="edit" class="me-2" />
                            Editar Perfil
                        </a>
                        <a href="{{ route('business.profile.change-password') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <x-icon name="lock" class="me-2" />
                            Cambiar Contraseña
                        </a>
                        <a href="{{ route('business.payments.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <x-icon name="creditCard" class="me-2" />
                            Gestionar Plan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Business Photo -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Foto del Negocio</h5>
                </div>
                <div class="card-body text-center">
                    @if($business->photo)
                        <img src="{{ asset('storage/' . $business->photo) }}"
                             alt="{{ $business->business_name }}"
                             class="img-fluid rounded shadow mb-3"
                             style="max-height: 250px; width: 100%; object-fit: cover;">
                    @else
                        <div class="py-5">
                            <div class="icon icon-shape icon-xl bg-gray-200 text-gray-600 rounded-circle mb-3 mx-auto">
                                <x-icon name="image" />
                            </div>
                            <p class="text-gray-600 mb-0">Sin foto</p>
                        </div>
                    @endif
                    <a href="{{ route('business.profile.edit') }}" class="btn btn-sm btn-outline-primary">
                        <x-icon name="edit" class="me-1" />
                        Cambiar Foto
                    </a>
                </div>
            </div>

            <!-- Account Status -->
            <div class="card border-0 shadow">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Estado de Cuenta</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-gray-600 small mb-1">Estado Actual</p>
                            @if($business->is_active)
                                <h5 class="mb-0" style="color: #10B981;">
                                    <x-icon name="checkCircle" class="me-1" />
                                    Activa
                                </h5>
                            @else
                                <h5 class="mb-0" style="color: #EF4444;">
                                    <x-icon name="error" class="me-1" />
                                    Inactiva
                                </h5>
                            @endif
                        </div>
                        <div class="icon icon-shape icon-lg text-white rounded" style="background-color: {{ $business->is_active ? '#10B981' : '#EF4444' }};">
                            <x-icon name="user" />
                        </div>
                    </div>
                    @if(!$business->is_active)
                    <div class="alert alert-warning mt-3 mb-0">
                        <small>Tu cuenta está inactiva. Contacta a soporte o renueva tu plan.</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($business->latitude && $business->longitude)
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
<script>
    function initMap() {
        const businessLocation = {
            lat: {{ $business->latitude }},
            lng: {{ $business->longitude }}
        };

        const map = new google.maps.Map(document.getElementById('map'), {
            center: businessLocation,
            zoom: 15,
            disableDefaultUI: false,
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false,
            gestureHandling: 'cooperative',
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'on' }]
                }
            ]
        });

        const marker = new google.maps.Marker({
            position: businessLocation,
            map: map,
            title: '{{ $business->business_name }}',
            draggable: false, 
            animation: google.maps.Animation.DROP
        });

        const infoWindow = new google.maps.InfoWindow({
            content: '<div style="padding: 10px;"><strong>{{ $business->business_name }}</strong><br>{{ $business->address ?? "Mi negocio" }}</div>'
        });

        marker.addListener('click', function() {
            infoWindow.open(map, marker);
        });
    }

    if (typeof google !== 'undefined') {
        initMap();
    } else {
        window.addEventListener('load', initMap);
    }
</script>
@endif
@endpush
