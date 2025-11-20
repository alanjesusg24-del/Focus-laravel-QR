@extends('layouts.business-app')

@section('title', 'Editar Perfil')

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
                    <li class="breadcrumb-item"><a href="{{ route('business.profile.index') }}">Mi Perfil</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
            <h2 class="h4">Editar Perfil</h2>
            <p class="mb-0">Actualiza la información de tu negocio</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.profile.index') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al Perfil
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card border-0 shadow">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Información del Negocio</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('business.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="business_name" class="form-label">Nombre del Negocio <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('business_name') is-invalid @enderror"
                                           id="business_name"
                                           name="business_name"
                                           value="{{ old('business_name', $business->business_name) }}"
                                           required>
                                    @error('business_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                    <input type="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone', $business->phone) }}"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Hidden address field - auto-filled by geocoding -->
                            <input type="hidden" id="address" name="address" value="{{ old('address', $business->address) }}">

                            <!-- Photo Upload Field -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Foto del Negocio</label>

                                    @if($business->photo)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $business->photo) }}"
                                             alt="Foto actual del negocio"
                                             class="img-thumbnail"
                                             style="max-width: 300px; max-height: 200px; object-fit: cover;"
                                             id="current-photo">
                                    </div>
                                    @endif

                                    <input type="file"
                                           class="form-control @error('photo') is-invalid @enderror"
                                           id="photo"
                                           name="photo"
                                           accept="image/*"
                                           onchange="previewPhoto(event)">
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Sube una foto de tu negocio (máx. 5MB). Formato: JPG, JPEG, PNG</small>

                                    <div class="mt-2" id="photo-preview" style="display: none;">
                                        <img id="preview-image" class="img-thumbnail" style="max-width: 300px; max-height: 200px; object-fit: cover;">
                                    </div>
                                </div>
                            </div>

                            <!-- Google Maps Location Selector -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Ubicación del Negocio</label>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" id="searchBtn" class="btn btn-outline-primary">
                                                <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                                </svg>
                                                Buscar
                                            </button>
                                            <button type="button" id="getLocationBtn" class="btn btn-primary">
                                                <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Mi Ubicación
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Search Box -->
                                    <div id="searchBox" class="mb-2" style="display: none;">
                                        <div class="input-group">
                                            <input type="text"
                                                   id="searchInput"
                                                   class="form-control"
                                                   placeholder="Busca tu negocio (ej. Cafetería Central, Av. Juárez 123...)">
                                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('searchBox').style.display='none'">
                                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <small class="text-muted">Escribe tu dirección o el nombre de tu negocio y selecciona de las sugerencias</small>
                                    </div>

                                    <!-- Selected Address Display -->
                                    <div id="selectedAddress" class="alert alert-info d-none mb-2">
                                        <div class="d-flex align-items-start">
                                            <svg class="icon icon-sm me-2 mt-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div class="flex-grow-1">
                                                <strong>Ubicación seleccionada:</strong>
                                                <div id="addressText" class="mt-1"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Map Container -->
                                    <div id="map" style="width: 100%; height: 400px; border-radius: 0.375rem; border: 1px solid #d1d5db;" class="mb-3"></div>

                                    <!-- Hidden inputs for coordinates -->
                                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $business->latitude) }}">
                                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $business->longitude) }}">

                                    <!-- Location Description -->
                                    <label for="location_description" class="form-label mt-2">Referencias Adicionales</label>
                                    <input type="text"
                                           class="form-control @error('location_description') is-invalid @enderror"
                                           id="location_description"
                                           name="location_description"
                                           value="{{ old('location_description', $business->location_description) }}"
                                           placeholder="Referencias adicionales (ej. Frente a la farmacia, esquina con...)">
                                    @error('location_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <strong>Instrucciones:</strong>
                                        1) Haz clic en "Buscar" y escribe tu dirección, o
                                        2) Haz clic directamente en el mapa donde está tu negocio, o
                                        3) Arrastra el marcador rojo a la ubicación exacta
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('business.profile.index') }}" class="btn btn-light">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>

<script>
    // Photo Preview Function
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (file) {
            // Check file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. El tamaño máximo es 5MB.');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('photo-preview');
                const previewImage = document.getElementById('preview-image');
                previewImage.src = e.target.result;
                preview.style.display = 'block';

                // Hide current photo if exists
                const currentPhoto = document.getElementById('current-photo');
                if (currentPhoto) {
                    currentPhoto.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // Google Maps Initialization
    let map;
    let marker;
    let autocomplete;
    let geocoder;

    function initMap() {
        // Get existing coordinates or use default (Mexico City)
        const existingLat = parseFloat(document.getElementById('latitude').value) || 19.4326;
        const existingLng = parseFloat(document.getElementById('longitude').value) || -99.1332;
        const hasExistingLocation = document.getElementById('latitude').value && document.getElementById('longitude').value;

        // Initialize geocoder
        geocoder = new google.maps.Geocoder();

        // Initialize map
        const mapCenter = { lat: existingLat, lng: existingLng };
        map = new google.maps.Map(document.getElementById('map'), {
            center: mapCenter,
            zoom: hasExistingLocation ? 15 : 12,
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true
        });

        // Initialize Places Autocomplete
        const searchInput = document.getElementById('searchInput');
        autocomplete = new google.maps.places.Autocomplete(searchInput, {
            componentRestrictions: { country: 'mx' }, // Restringir a México
            fields: ['formatted_address', 'geometry', 'name']
        });

        // Listen for place selection
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                alert('No se encontraron detalles para: ' + place.name);
                return;
            }

            // Center map on selected place
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            // Place marker
            placeMarker(place.geometry.location);
            updateCoordinates(place.geometry.location.lat(), place.geometry.location.lng());
        });

        // Add existing marker if coordinates exist
        if (hasExistingLocation) {
            marker = new google.maps.Marker({
                position: mapCenter,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                title: 'Arrastra para ajustar la ubicación'
            });

            // Update coordinates when marker is dragged
            marker.addListener('dragend', function(event) {
                updateCoordinates(event.latLng.lat(), event.latLng.lng());
            });

            // Get initial address if exists
            if (hasExistingLocation) {
                getAddressFromCoordinates(existingLat, existingLng);
            }
        }

        // Add click listener to map
        map.addListener('click', function(event) {
            placeMarker(event.latLng);
            updateCoordinates(event.latLng.lat(), event.latLng.lng());
        });
    }

    function placeMarker(location) {
        // Remove existing marker if any
        if (marker) {
            marker.setMap(null);
        }

        // Create new marker
        marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: 'Arrastra para ajustar la ubicación'
        });

        // Update coordinates when marker is dragged
        marker.addListener('dragend', function(event) {
            updateCoordinates(event.latLng.lat(), event.latLng.lng());
        });
    }

    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);

        // Get address from coordinates (Reverse Geocoding)
        getAddressFromCoordinates(lat, lng);
    }

    // Reverse Geocoding - Get address from coordinates
    function getAddressFromCoordinates(lat, lng) {
        const latlng = { lat: lat, lng: lng };

        geocoder.geocode({ location: latlng }, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    // Update address field with formatted address
                    document.getElementById('address').value = results[0].formatted_address;

                    // Show selected address
                    document.getElementById('addressText').textContent = results[0].formatted_address;
                    document.getElementById('selectedAddress').classList.remove('d-none');
                }
            }
        });
    }

    // Toggle search box
    document.getElementById('searchBtn').addEventListener('click', function() {
        const searchBox = document.getElementById('searchBox');
        searchBox.style.display = searchBox.style.display === 'none' ? 'block' : 'none';
        if (searchBox.style.display === 'block') {
            document.getElementById('searchInput').focus();
        }
    });

    // Get user's current location using Geolocation API
    document.getElementById('getLocationBtn').addEventListener('click', function() {
        const button = this;
        const originalText = button.innerHTML;

        // Check if geolocation is available
        if (!navigator.geolocation) {
            alert('Tu navegador no soporta geolocalización.');
            return;
        }

        // Check if site is secure (HTTPS or localhost)
        const isSecure = window.location.protocol === 'https:' ||
                        window.location.hostname === 'localhost' ||
                        window.location.hostname === '127.0.0.1';

        if (!isSecure) {
            if (confirm('⚠️ La geolocalización requiere una conexión segura (HTTPS).\n\n' +
                       'Como alternativa, puedes:\n' +
                       '1. Hacer clic directamente en el mapa donde está tu negocio\n' +
                       '2. Buscar tu negocio en Google Maps y copiar las coordenadas\n\n' +
                       '¿Quieres intentar obtener tu ubicación de todas formas?')) {
                // Intentar de todas formas
                attemptGeolocation();
            }
            return;
        }

        attemptGeolocation();

        function attemptGeolocation() {
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Obteniendo ubicación...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const location = { lat: lat, lng: lng };

                    // Center map on user's location
                    map.setCenter(location);
                    map.setZoom(17);

                    // Place marker at user's location
                    placeMarker(location);
                    updateCoordinates(lat, lng);

                    // Restore button
                    button.disabled = false;
                    button.innerHTML = originalText;

                    // Show success message
                    const successMsg = document.createElement('div');
                    successMsg.className = 'alert alert-success alert-dismissible fade show mt-2';
                    successMsg.innerHTML = '✓ Ubicación obtenida correctamente. La dirección se ha actualizado automáticamente.';
                    document.getElementById('map').parentElement.appendChild(successMsg);
                    setTimeout(() => successMsg.remove(), 5000);
                },
                function(error) {
                    // Handle error
                    button.disabled = false;
                    button.innerHTML = originalText;

                    let errorMessage = '❌ No se pudo obtener tu ubicación.\n\n';
                    let helpText = '';

                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += 'Has bloqueado el acceso a tu ubicación.\n\n';
                            helpText = 'Para permitir el acceso:\n' +
                                     '• Chrome: Haz clic en el icono 🔒 o ⓘ en la barra de direcciones\n' +
                                     '• Firefox: Haz clic en el candado y administra permisos\n' +
                                     '• Edge: Configuración del sitio → Ubicación → Permitir\n\n' +
                                     'Alternativamente, puedes hacer clic directamente en el mapa.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += 'No se pudo determinar tu ubicación.\n\n';
                            helpText = 'Intenta:\n' +
                                     '• Activar el GPS en tu dispositivo\n' +
                                     '• Verificar tu conexión a internet\n' +
                                     '• O haz clic directamente en el mapa';
                            break;
                        case error.TIMEOUT:
                            errorMessage += 'Se agotó el tiempo de espera.\n\n';
                            helpText = 'Intenta de nuevo o haz clic directamente en el mapa.';
                            break;
                        default:
                            errorMessage += 'Error desconocido.\n\n';
                            helpText = 'Haz clic directamente en el mapa para seleccionar tu ubicación.';
                    }

                    alert(errorMessage + helpText);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        }
    });

    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
    });
</script>
@endpush

@endsection
