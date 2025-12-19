{{--
  Company: CETAM
  Project: FQR
  File: edit.blade.php
  Created on: 08/10/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno
--}}
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
                            <x-icon name="nav.home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('business.profile.index') }}">Mi Perfil</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
            <h2 class="h4">Editar Perfil</h2>
            <p class="mb-0">Actualiza la información de tu negocio</p>
        </div>
        
    </div>

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
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $business->email) }}"
                                           required>
                                    @error('email')
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
                                    <label class="form-label mb-2">Ubicación del Negocio</label>

                                    <!-- Search Box -->
                                    <div class="mb-2">
                                        <input type="text"
                                               id="searchInput"
                                               class="form-control"
                                               placeholder="Busca tu negocio o dirección (ej. Cafetería Central, Av. Juárez 123...)">
                                        <small class="text-muted">Escribe tu dirección y selecciona de las sugerencias, o haz clic directamente en el mapa</small>
                                    </div>

                                    <!-- Selected Address Display -->
                                    <div id="selectedAddress" class="alert alert-info d-none mb-2">
                                        <div class="d-flex align-items-start">
                                            <x-icon name="mapPin" class="me-2 mt-1" />
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
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="col-12">
                                <h6 class="fw-bold mb-2 mt-3">Cambiar Contraseña (Opcional)</h6>
                                <p class="text-muted small mb-3">Deja los campos en blanco si no deseas cambiar tu contraseña</p>
                            </div>

                            <!-- Password Fields -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Contraseña Actual</label>
                                    <input type="password"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           id="current_password"
                                           name="current_password"
                                           placeholder="Ingresa tu contraseña actual">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           minlength="8"
                                           maxlength="12"
                                           placeholder="Ingresa tu nueva contraseña">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">La contraseña debe tener entre 8 y 12 caracteres</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           minlength="8"
                                           maxlength="12"
                                           placeholder="Confirma tu nueva contraseña">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-start gap-3 pt-3 border-top">
                            <button type="submit" class="btn btn-primary">
                                <x-icon name="action.save" class="me-2" />
                                Guardar
                            </button>

                            <a href="{{ route('business.profile.index') }}" class="btn btn-gray-300">
                                Cancelar
                            </a>
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

    // Helper function to show error messages
    function showErrorMessage(title, message, helpHtml) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
        errorDiv.innerHTML = `
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <h6 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i>${title}</h6>
            <p class="mb-2">${message}</p>
            <hr>
            <div class="mb-0 small">${helpHtml}</div>
        `;

        // Remove any existing error messages
        const existingErrors = document.querySelectorAll('.alert-danger');
        existingErrors.forEach(el => el.remove());

        // Insert after the map
        document.getElementById('map').parentElement.appendChild(errorDiv);

        // Auto-remove after 15 seconds
        setTimeout(() => {
            if (errorDiv.parentElement) {
                errorDiv.remove();
            }
        }, 15000);
    }

    // Google Maps Initialization
    let map;
    let marker;
    let autocomplete;
    let geocoder;

    function initMap() {
        // Get existing coordinates or use default (Mexico City)
        const latInput = document.getElementById('latitude').value;
        const lngInput = document.getElementById('longitude').value;

        console.log('=== CARGA INICIAL DEL MAPA ===');
        console.log('Valor del input latitude:', latInput);
        console.log('Valor del input longitude:', lngInput);

        // Parse coordinates
        const latValue = parseFloat(latInput);
        const lngValue = parseFloat(lngInput);

        // Check if coordinates are valid (not 0, not NaN, not empty)
        const hasValidCoordinates = latInput && lngInput &&
                                   !isNaN(latValue) && !isNaN(lngValue) &&
                                   latValue !== 0 && lngValue !== 0;

        // Use saved coordinates or default to Mexico City
        const existingLat = hasValidCoordinates ? latValue : 19.4326;
        const existingLng = hasValidCoordinates ? lngValue : -99.1332;
        const hasExistingLocation = hasValidCoordinates;

        console.log('Coordenadas a usar:', { lat: existingLat, lng: existingLng });
        console.log('¿Tiene ubicación guardada?', hasExistingLocation);

        // Initialize geocoder
        geocoder = new google.maps.Geocoder();

        // Initialize map
        const mapCenter = { lat: existingLat, lng: existingLng };
        map = new google.maps.Map(document.getElementById('map'), {
            center: mapCenter,
            zoom: hasExistingLocation ? 15 : 12,
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

        // Initialize Places Autocomplete
        const searchInput = document.getElementById('searchInput');
        autocomplete = new google.maps.places.Autocomplete(searchInput, {
            componentRestrictions: { country: 'mx' }, 
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

        console.log('Coordenadas actualizadas:', {
            latitude: lat.toFixed(7),
            longitude: lng.toFixed(7)
        });

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

    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initMap();

        // Debug: Verify form data before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            const addr = document.getElementById('address').value;

            console.log('=== ENVIANDO FORMULARIO ===');
            console.log('Latitude:', lat);
            console.log('Longitude:', lng);
            console.log('Address:', addr);

            const latNum = parseFloat(lat);
            const lngNum = parseFloat(lng);

            if (!lat || !lng || latNum === 0 || lngNum === 0 || isNaN(latNum) || isNaN(lngNum)) {
                e.preventDefault();
                alert('⚠️ ERROR: Las coordenadas no están configuradas correctamente.\n\n' +
                      'Latitude: ' + lat + ' (' + latNum + ')\n' +
                      'Longitude: ' + lng + ' (' + lngNum + ')\n\n' +
                      'Por favor, selecciona una ubicación en el mapa antes de guardar.');
                return false;
            }
        });
    });
</script>
@endpush

@endsection
