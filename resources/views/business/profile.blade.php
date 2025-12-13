{{--
  Company: CETAM
  Project: FQR
  File: profile.blade.php
  Created on: 05/11/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.business-app')

@section('title', 'Mi Perfil')

@section('page')
<div class="py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('business.dashboard.index') }}">
                    <x-icon name="home" />
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Mi Perfil</h1>
            <p class="mb-0">Información de tu negocio</p>
        </div>
        <div>
            <a href="{{ route('business.profile.edit') }}" class="btn btn-primary d-inline-flex align-items-center">
                <x-icon name="edit" class="me-2" />
                Editar Perfil
            </a>
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

<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h2 class="fs-5 fw-bold mb-0">Información del Negocio</h2>
                @if($business->is_active)
                    <span class="fw-bold text-success">Cuenta Activa</span>
                @else
                    <span class="fw-bold text-danger">Cuenta Inactiva</span>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-gray-600 fw-bold" style="width: 200px;">Nombre del Negocio</td>
                                <td class="text-gray-900">{{ $business->business_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-bold">RFC</td>
                                <td class="text-gray-900">{{ $business->rfc }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-bold">Email</td>
                                <td class="text-gray-900">{{ $business->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-bold">Teléfono</td>
                                <td class="text-gray-900">{{ $business->phone }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-bold">Dirección</td>
                                <td class="text-gray-900">{{ $business->address ?? 'No especificada' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header border-bottom">
                <h2 class="fs-5 fw-bold mb-0">Plan y Configuración</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-gray-600 fw-bold" style="width: 200px;">Plan Actual</td>
                                <td class="text-gray-900">{{ $business->plan->name ?? 'Sin plan' }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-bold">Precio Mensual</td>
                                <td class="text-gray-900">${{ number_format($business->monthly_price ?? 0, 2) }} MXN</td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-bold">Módulo de Chat</td>
                                <td>
                                    @if($business->has_chat_module)
                                        <span class="fw-bold text-success">Activado</span>
                                    @else
                                        <span class="fw-bold text-secondary">No activado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-bold">Retención de Datos</td>
                                <td class="text-gray-900">{{ $business->data_retention_months ?? 1 }} {{ $business->data_retention_months == 1 ? 'mes' : 'meses' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($business->latitude && $business->longitude)
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header border-bottom">
                <h2 class="fs-5 fw-bold mb-0">Ubicación</h2>
            </div>
            <div class="card-body">
                <div id="map" style="height: 350px; width: 100%; border-radius: 0.5rem;"></div>
            </div>
        </div>
    </div>
</div>
@endif

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
                    stylers: [{ visibility: 'off' }]
                },
                {
                    featureType: 'poi.business',
                    stylers: [{ visibility: 'off' }]
                },
                {
                    featureType: 'transit',
                    elementType: 'labels.icon',
                    stylers: [{ visibility: 'off' }]
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
