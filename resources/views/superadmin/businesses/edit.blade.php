{{--
  Company: CETAM
  Project: FQR
  File: edit.blade.php
  Created on: 11/12/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.superadmin-app')

@section('title', 'Editar Negocio - ' . $business->business_name)

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg></a></li>
            <li class="breadcrumb-item"><a href="{{ route('superadmin.businesses.index') }}">Negocios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar {{ $business->business_name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between w-100 flex-wrap">
        <div class="mb-3 mb-lg-0">
            <h1 class="h4">Editar Negocio</h1>
            <p class="mb-0">Modifica la información del negocio</p>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Error!</strong> Por favor corrige los siguientes errores:
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('superadmin.businesses.update', $business->business_id) }}" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    {{-- Form in a single column (stacked format) --}}
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow mb-4">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <h2 class="fs-5 fw-bold mb-0">Información del Negocio</h2>
                </div>
                <div class="card-body">
                    {{-- Business Name --}}
                    <div class="mb-4">
                        <label for="business_name" class="form-label fw-bold">Nombre del Negocio <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name', $business->business_name) }}" required>
                        @error('business_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- RFC -->
                    <div class="mb-4">
                        <label for="rfc" class="form-label fw-bold">RFC <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('rfc') is-invalid @enderror" id="rfc" name="rfc" value="{{ old('rfc', $business->rfc) }}" maxlength="13" required>
                        @error('rfc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $business->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="mb-4">
                        <label for="phone" class="form-label fw-bold">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $business->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="mb-4">
                        <label for="address" class="form-label fw-bold">Dirección</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $business->address) }}" placeholder="Buscar dirección...">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Google Maps --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Ubicación en el Mapa</label>
                        <div id="map" class="border rounded" style="height: 400px; width: 100%;"></div>
                    </div>
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $business->latitude) }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $business->longitude) }}">

                    {{-- Plan --}}
                    <div class="mb-4">
                        <label for="plan_id" class="form-label fw-bold">Plan <span class="text-danger">*</span></label>
                        <select class="form-select @error('plan_id') is-invalid @enderror" id="plan_id" name="plan_id" required>
                            <option value="">Seleccionar plan...</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->plan_id }}"
                                        data-price="{{ $plan->price }}"
                                        data-chat="{{ $plan->has_chat_module ? 'Sí' : 'No' }}"
                                        data-realerts="{{ $plan->has_realerts ? 'Sí' : 'No' }}"
                                        {{ old('plan_id', $business->plan_id) == $plan->plan_id ? 'selected' : '' }}>
                                    {{ $plan->name }} - ${{ number_format($plan->price, 2) }}/mes
                                </option>
                            @endforeach
                        </select>
                        @error('plan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Selected Plan Info --}}
                        <div id="plan-info" class="alert alert-light border mt-3" style="display: none;">
                            <small class="d-block mb-1"><strong>Precio:</strong> <span id="plan-price">-</span></small>
                            <small class="d-block mb-1"><strong>Chat:</strong> <span id="plan-chat">-</span></small>
                            <small class="d-block"><strong>Re-alertas:</strong> <span id="plan-realerts">-</span></small>
                        </div>
                    </div>

                    {{-- New Password --}}
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Nueva Contraseña</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Dejar en blanco para mantener la actual">
                        <small class="form-text text-muted">Mínimo 8 caracteres. Solo completar si desea cambiar la contraseña.</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Business Photo --}}
                    <div class="mb-4">
                        <label for="photo" class="form-label fw-bold">Foto del Negocio</label>
                        @if($business->photo)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $business->photo) }}" class="rounded shadow-sm" style="max-width: 200px;" alt="{{ $business->business_name }}">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB</small>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Business Status --}}
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $business->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Negocio Activo</label>
                        </div>
                        <small class="form-text text-muted">Los negocios inactivos no pueden acceder al sistema</small>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"></path>
                            </svg>
                            Guardar Cambios
                        </button>
                        <a href="{{ route('superadmin.businesses.index') }}" class="btn btn-primary btn-lg">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initMap" async defer></script>
<script>
let map, marker, geocoder, autocomplete;

function initMap() {
    // Get initial coordinates (default: MMexico City if no coordinates)
    const initialLat = parseFloat(document.getElementById('latitude').value) || 19.432608;
    const initialLng = parseFloat(document.getElementById('longitude').value) || -99.133209;

    const initialPosition = { lat: initialLat, lng: initialLng };

    // Initialize the map
    map = new google.maps.Map(document.getElementById('map'), {
        center: initialPosition,
        zoom: 15,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
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

    // Initialize geocoder
    geocoder = new google.maps.Geocoder();

    // Create draggable marker
    marker = new google.maps.Marker({
        position: initialPosition,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: 'Ubicación del negocio'
    });

    // Autocomplete for the address field
    const addressInput = document.getElementById('address');
    autocomplete = new google.maps.places.Autocomplete(addressInput, {
        componentRestrictions: { country: 'mx' },
        fields: ['address_components', 'geometry', 'formatted_address', 'name']
    });

    // Event when an address is selected from the autocomplete
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();

        if (!place.geometry) {
            console.error('No se encontró la geometría del lugar');
            return;
        }

        const location = place.geometry.location;

        // Refresh map and marker
        map.setCenter(location);
        map.setZoom(17);
        marker.setPosition(location);

        // Refresh coordinates
        updateCoordinates(location.lat(), location.lng());

        // Refresh address field
        if (place.formatted_address) {
            addressInput.value = place.formatted_address;
        }
    });

    // Event when the marker is dragged
    marker.addListener('dragend', function(event) {
        const newLat = event.latLng.lat();
        const newLng = event.latLng.lng();

        // Refresh coordinates
        updateCoordinates(newLat, newLng);

        // Perform reverse geocoding to update the address
        reverseGeocode(newLat, newLng);
    });

    // Event when the map is clicked
    map.addListener('click', function(event) {
        const clickedLat = event.latLng.lat();
        const clickedLng = event.latLng.lng();

        // Move marker to the new position
        marker.setPosition(event.latLng);

        // Refresh coordinates
        updateCoordinates(clickedLat, clickedLng);

        // Perform reverse geocoding to update the address
        reverseGeocode(clickedLat, clickedLng);
    });
}

function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(7);
    document.getElementById('longitude').value = lng.toFixed(7);
}

function reverseGeocode(lat, lng) {
    const latlng = { lat: lat, lng: lng };
    const addressInput = document.getElementById('address');

    // Show loading indicator
    addressInput.value = 'Obteniendo dirección...';
    addressInput.disabled = true;

    geocoder.geocode({ location: latlng }, function(results, status) {
        if (status === 'OK') {
            if (results[0]) {
                addressInput.value = results[0].formatted_address;
            } else {
                addressInput.value = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
            }
        } else {
            console.error('Geocoding falló: ' + status);
            // If geocoding fails, use the coordinates as the address.
            addressInput.value = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
        }
        addressInput.disabled = false;
    });
}

// Script to display information about the selected plan
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan_id');
    const planInfo = document.getElementById('plan-info');
    const planPrice = document.getElementById('plan-price');
    const planChat = document.getElementById('plan-chat');
    const planRealerts = document.getElementById('plan-realerts');

    function updatePlanInfo() {
        const selectedOption = planSelect.options[planSelect.selectedIndex];

        if (selectedOption.value) {
            const price = selectedOption.dataset.price;
            const chat = selectedOption.dataset.chat;
            const realerts = selectedOption.dataset.realerts;

            planPrice.textContent = '$' + parseFloat(price).toFixed(2) + '/mes';
            planChat.textContent = chat;
            planRealerts.textContent = realerts;

            planInfo.style.display = 'block';
        } else {
            planInfo.style.display = 'none';
        }
    }

    // Show current plan info on load
    updatePlanInfo();

    // Update when the plan changes
    planSelect.addEventListener('change', updatePlanInfo);
});
</script>
@endpush
