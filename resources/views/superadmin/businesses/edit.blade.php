@extends('layouts.superadmin-app')

@section('title', 'Editar Negocio - ' . $business->business_name)

@section('page')
<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg></a></li>
            <li class="breadcrumb-item"><a href="{{ route('superadmin.businesses.index') }}">Negocios</a></li>
            <li class="breadcrumb-item"><a href="{{ route('superadmin.businesses.show', $business->business_id) }}">{{ $business->business_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
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

<form method="POST" action="{{ route('superadmin.businesses.update', $business->business_id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-12 col-lg-8 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Información General</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="business_name" class="form-label">Nombre del Negocio *</label>
                        <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name', $business->business_name) }}" required>
                        @error('business_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rfc" class="form-label">RFC</label>
                            <input type="text" class="form-control @error('rfc') is-invalid @enderror" id="rfc" name="rfc" value="{{ old('rfc', $business->rfc) }}" maxlength="13">
                            @error('rfc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $business->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $business->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $business->address) }}" placeholder="Buscar dirección...">
                        <small class="text-muted">Escribe la dirección y selecciona del mapa</small>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mapa de Google Maps -->
                    <div class="mb-3">
                        <label class="form-label">Ubicación en el Mapa</label>
                        <div id="map" class="border rounded" style="height: 400px; width: 100%;"></div>
                        <small class="text-muted d-block mt-2">
                            <svg class="icon icon-xxs text-info me-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            Arrastra el marcador para ajustar la ubicación exacta
                        </small>
                    </div>

                    <!-- Campos ocultos para coordenadas -->
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $business->latitude) }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $business->longitude) }}">

                    <div class="mb-3">
                        <label for="location_description" class="form-label">Descripción de Ubicación</label>
                        <input type="text" class="form-control @error('location_description') is-invalid @enderror" id="location_description" name="location_description" value="{{ old('location_description', $business->location_description) }}" placeholder="Ej: Entre calle X y Y, frente al parque">
                        <small class="text-muted">Referencias adicionales para encontrar el negocio</small>
                        @error('location_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Dejar en blanco para mantener la actual">
                        <small class="text-muted">Mínimo 8 caracteres. Solo completar si desea cambiar la contraseña.</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Foto del Negocio</label>
                        @if($business->photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $business->photo) }}" class="rounded" style="max-width: 150px;" alt="{{ $business->business_name }}">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB</small>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mb-4">
            <div class="card border-0 shadow mb-4">
                <div class="card-header">
                    <h2 class="fs-5 fw-bold mb-0">Configuración del Plan</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="plan_id" class="form-label">Plan *</label>
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
                    </div>

                    <!-- Info del plan seleccionado -->
                    <div id="plan-info" class="alert alert-light border mb-3" style="display: none;">
                        <small class="d-block mb-1"><strong>Precio:</strong> <span id="plan-price">-</span></small>
                        <small class="d-block mb-1"><strong>Chat:</strong> <span id="plan-chat">-</span></small>
                        <small class="d-block"><strong>Re-alertas:</strong> <span id="plan-realerts">-</span></small>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $business->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_active">Negocio Activo</label>
                        <div class="text-muted small mt-1">Los negocios inactivos no pueden acceder al sistema</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('superadmin.businesses.show', $business->business_id) }}" class="btn btn-secondary w-100">
                        Cancelar
                    </a>
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
    // Obtener coordenadas iniciales (default: México City si no hay coordenadas)
    const initialLat = parseFloat(document.getElementById('latitude').value) || 19.432608;
    const initialLng = parseFloat(document.getElementById('longitude').value) || -99.133209;

    const initialPosition = { lat: initialLat, lng: initialLng };

    // Inicializar el mapa
    map = new google.maps.Map(document.getElementById('map'), {
        center: initialPosition,
        zoom: 15,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true
    });

    // Inicializar geocoder
    geocoder = new google.maps.Geocoder();

    // Crear marcador arrastrable
    marker = new google.maps.Marker({
        position: initialPosition,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: 'Ubicación del negocio'
    });

    // Autocomplete para el campo de dirección
    const addressInput = document.getElementById('address');
    autocomplete = new google.maps.places.Autocomplete(addressInput, {
        componentRestrictions: { country: 'mx' },
        fields: ['address_components', 'geometry', 'formatted_address', 'name']
    });

    // Evento cuando se selecciona una dirección del autocomplete
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();

        if (!place.geometry) {
            console.error('No se encontró la geometría del lugar');
            return;
        }

        const location = place.geometry.location;

        // Actualizar mapa y marcador
        map.setCenter(location);
        map.setZoom(17);
        marker.setPosition(location);

        // Actualizar campos ocultos
        updateCoordinates(location.lat(), location.lng());

        // Actualizar dirección formateada
        if (place.formatted_address) {
            addressInput.value = place.formatted_address;
        }
    });

    // Evento cuando se arrastra el marcador
    marker.addListener('dragend', function(event) {
        const newLat = event.latLng.lat();
        const newLng = event.latLng.lng();

        // Actualizar coordenadas
        updateCoordinates(newLat, newLng);

        // Hacer geocoding inverso para actualizar la dirección
        reverseGeocode(newLat, newLng);
    });

    // Evento click en el mapa
    map.addListener('click', function(event) {
        const clickedLat = event.latLng.lat();
        const clickedLng = event.latLng.lng();

        // Mover marcador a la nueva posición
        marker.setPosition(event.latLng);

        // Actualizar coordenadas
        updateCoordinates(clickedLat, clickedLng);

        // Hacer geocoding inverso
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

    // Mostrar indicador de carga
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
            // Si falla el geocoding, usar las coordenadas como dirección
            addressInput.value = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
        }
        addressInput.disabled = false;
    });
}

// Script para mostrar info del plan seleccionado
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

    // Mostrar info del plan actual al cargar
    updatePlanInfo();

    // Actualizar cuando cambie el plan
    planSelect.addEventListener('change', updatePlanInfo);
});
</script>
@endpush
