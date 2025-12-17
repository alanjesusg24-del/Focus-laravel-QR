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
                    <x-icon name="nav.home" />
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
    </div>
</div>

{{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <x-icon name="success" class="me-2" />
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        
        {{-- COLUMNA DERECHA: TARJETA DE IDENTIDAD --}}
        <div class="col-12 col-xl-4 order-xl-2">
            <div class="card shadow border-0 text-center mb-3">
                <div class="card-body py-3">
                    
                    {{-- Avatar / Logo Logic --}}
                    <div class="mx-auto mb-4 position-relative avatar-container">
                        @if(isset($business->logo_url) && $business->logo_url)
                            @php
                                $logoUrl = str_starts_with($business->logo_url, 'storage/') 
                                    ? asset($business->logo_url) 
                                    : asset('storage/' . $business->logo_url);
                            @endphp
                            <img src="{{ $logoUrl }}"
                                 class="avatar-xl rounded-circle border border-gray-300 shadow w-100 h-100 object-fit-cover"
                                 alt="Logo del negocio"
                                 id="identityLogo">
                        @else
                            {{-- Lógica de Iniciales --}}
                            @php
                                $businessName = $business->business_name ?? 'NN';
                                $words = explode(' ', trim($businessName));
                                $initials = '';
                                if (count($words) >= 2) {
                                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                } else {
                                    $initials = strtoupper(substr($businessName, 0, 2));
                                }
                            @endphp
                            <div class="avatar-xl rounded-circle border border-gray-300 shadow w-100 h-100 d-flex align-items-center justify-content-center bg-primary text-white fs-1 fw-bold"
                                 id="identityLogo">
                                {{ $initials }}
                            </div>
                        @endif
                    </div>

                    <h4 class="h3 fw-bold text-primary">{{ $business->business_name }}</h4>
                    <p class="text-gray-500 mb-3">{{ $business->email }}</p>
                </div>
            </div>

            {{-- Card para subir foto de perfil --}}
            <div class="card card-body border-0 shadow mb-4">
                <h5 class="text-primary fw-bold mb-4">Seleccionar foto de perfil</h5>

                {{-- Formulario para el logo --}}
                <form action="{{ route('business.profile.update-logo') }}" method="POST" enctype="multipart/form-data" id="logoForm">
                    @csrf
                    @method('PATCH')
                    
                    <div class="d-flex align-items-center gap-4">
                        {{-- Image Preview (large on the left) --}}
                        <div class="flex-shrink-0">
                            @if(isset($business->logo_url) && $business->logo_url)
                                @php
                                    $logoUrl = str_starts_with($business->logo_url, 'storage/') 
                                        ? asset($business->logo_url) 
                                        : asset('storage/' . $business->logo_url);
                                @endphp
                                <img src="{{ $logoUrl }}"
                                     class="rounded border logo-preview-size object-fit-cover"
                                     alt="Logo actual"
                                     id="logoPreview">
                            @else
                                {{-- Show initials if no logo --}}
                                @php
                                    $businessName = $business->business_name ?? 'NN';
                                    $words = explode(' ', trim($businessName));
                                    $initials = '';
                                    if (count($words) >= 2) {
                                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                    } else {
                                        $initials = strtoupper(substr($businessName, 0, 2));
                                    }
                                @endphp
                                <div class="rounded border d-flex align-items-center justify-content-center bg-primary text-white fs-1 fw-bold logo-preview-size"
                                     id="logoPreview">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>

                        <div class="flex-grow-1">
                            <label for="logo-upload" class="d-flex align-items-start gap-2 mb-0 cursor-pointer">
                                <x-icon name="action.upload" class="text-gray-500 mt-1 small" />
                                <div>
                                    <div class="fw-bold text-dark">Elegir Imagen</div>
                                </div>
                            </label>
                            <input type="file" id="logo-upload" name="logo" accept="image/*" class="d-none">
                        </div>
                    </div>

                    {{-- Validation Error Message --}}
                    @error('logo') 
                        <div class="text-danger small mt-3">{{ $message }}</div> 
                    @enderror

                    {{-- Save Logo Button (Only appears if an image is selected) --}}
                    <div class="mt-3 d-none" id="saveLogoSection">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <x-icon name="action.save" class="me-1" />
                            Guardar
                        </button>
                        <button type="button" class="btn btn-gray-300 ms-2" id="cancelLogo">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        {{-- COLUMNA IZQUIERDA: INFORMACIÓN DETALLADA --}}
        <div class="col-12 col-xl-8 order-xl-1">
            
            {{-- 1. Información General --}}
            <div class="card card-body border-0 shadow mb-4">
                <h2 class="h5 mb-4 text-primary fw-bold">Información General del Negocio</h2>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="business_name" class="form-label text-primary fw-bold">Razón Social <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="business_name" 
                               value="{{ $business->business_name }}" 
                               disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="rfc" class="form-label text-primary fw-bold">RFC <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="rfc" 
                               value="{{ $business->rfc }}" 
                               disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label text-primary fw-bold">Teléfono de Contacto <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="phone" 
                               value="{{ $business->phone }}" 
                               disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label text-primary fw-bold">Email de Contacto <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               value="{{ $business->email }}" 
                               disabled>
                    </div>
                    <div class="col-12">
                        <label for="address" class="form-label text-primary fw-bold">
                            Dirección <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="address" 
                                  rows="2" 
                                  disabled>{{ $business->address ?? 'Dirección no especificada' }}</textarea>
                    </div>
                </div>
                
                {{-- Botón de Editar Perfil --}}
                <div class="d-flex justify-content-start mt-3">
                    <a href="{{ route('business.profile.edit') }}" class="btn btn-primary d-inline-flex align-items-center">
                        <x-icon name="action.edit" class="me-2" />
                        Editar
                    </a>
                </div>
            </div>

            {{-- 2. Configuración del Sistema --}}
            <div class="card card-body border-0 shadow mb-4">
                <h2 class="h5 mb-4 text-primary fw-bold">Configuración</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-primary fw-bold">Módulo de Chat</label>
                        <div class="mt-1">
                            @if($business->has_chat_module)
                                <span class="d-inline-flex align-items-center text-success fw-bold">
                                    <x-icon name="state.success" class="me-1"/> Activado
                                </span>
                            @else
                                <span class="d-inline-flex align-items-center text-danger fw-bold">
                                    <x-icon name="state.error" class="text-danger me-1"/> No incluido
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-primary fw-bold">Retención de Datos</label>
                        <div class="mt-1 fw-bold text-dark">
                            @php
                                $retentionMonths = $business->data_retention_months ?? 1;
                            @endphp
                            {{ $retentionMonths }} {{ $retentionMonths == 1 ? 'mes' : 'meses' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Mapa de Ubicación --}}
            @if($business->latitude && $business->longitude)
                <div class="card card-body border-0 shadow mb-4">
                    <h2 class="h5 mb-3 text-primary fw-bold">Ubicación Registrada</h2>
                    <div id="map" class="w-100 rounded map-height"></div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- JavaScript para previsualización de logo --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoUpload = document.getElementById('logo-upload');
    const logoPreview = document.getElementById('logoPreview');
    const saveLogoSection = document.getElementById('saveLogoSection');
    const cancelLogoBtn = document.getElementById('cancelLogo');
    
    // Guardar el contenido original del preview
    const originalPreviewContent = logoPreview.outerHTML;
    
    // Obtener la imagen de la tarjeta de identidad y guardar su estado original
    let originalIdentityLogo = null;
    const identityElement = document.getElementById('identityLogo');
    if (identityElement) {
        originalIdentityLogo = identityElement.cloneNode(true);
        console.log('Imagen original guardada:', originalIdentityLogo);
    } else {
        console.log('No se encontró elemento identityLogo para guardar');
    }

    logoUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Debug info
            console.log('File selected:', {
                name: file.name,
                size: file.size,
                type: file.type,
                lastModified: file.lastModified
            });

            // Validar tamaño (2MB máximo)
            if (file.size > 2 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 2MB permitido.\nTamaño actual: ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB');
                e.target.value = '';
                return;
            }

            // Validar tipo de archivo (más específico)
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Tipo de archivo no permitido.\nTipo detectado: ' + file.type + '\nTipos permitidos: JPEG, PNG, JPG, GIF, WebP');
                e.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                // Actualizar la imagen de preview en la card de foto de perfil
                const currentLogoPreview = document.getElementById('logoPreview');
                if (currentLogoPreview) {
                    // Crear nueva imagen para el preview
                    const newPreviewImg = document.createElement('img');
                    newPreviewImg.src = e.target.result;
                    newPreviewImg.className = 'rounded border logo-preview-size object-fit-cover';
                    newPreviewImg.alt = 'Preview';
                    newPreviewImg.id = 'logoPreview';
                    
                    // Reemplazar el elemento actual
                    currentLogoPreview.parentNode.replaceChild(newPreviewImg, currentLogoPreview);
                }
                
                // También actualizar la imagen en la tarjeta de identidad si existe
                const currentIdentityLogo = document.getElementById('identityLogo');
                if (currentIdentityLogo) {
                    // Crear nueva imagen para la tarjeta de identidad
                    const newIdentityImg = document.createElement('img');
                    newIdentityImg.src = e.target.result;
                    newIdentityImg.className = 'avatar-xl rounded-circle border border-gray-300 shadow w-100 h-100 object-fit-cover';
                    newIdentityImg.alt = 'Preview logo del negocio';
                    newIdentityImg.id = 'identityLogo';
                    
                    // Reemplazar el elemento actual
                    currentIdentityLogo.parentNode.replaceChild(newIdentityImg, currentIdentityLogo);
                }
                
                // Mostrar botones de guardar/cancelar
                saveLogoSection.classList.remove('d-none');
            };
            
            reader.onerror = function() {
                alert('Error al leer el archivo. Por favor intenta con otro archivo.');
                e.target.value = '';
            };
            
            reader.readAsDataURL(file);
        }
    });

    // Cancelar selección
    cancelLogoBtn.addEventListener('click', function() {
        logoUpload.value = '';
        
        // Restaurar la imagen original en el preview
        const currentLogoPreview = document.getElementById('logoPreview');
        if (currentLogoPreview && originalPreviewContent) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = originalPreviewContent;
            const restoredElement = tempDiv.firstElementChild;
            currentLogoPreview.parentNode.replaceChild(restoredElement, currentLogoPreview);
        }
        
        // Restaurar la imagen original en la tarjeta de identidad
        const currentIdentityLogo = document.getElementById('identityLogo');
        if (currentIdentityLogo && originalIdentityLogo) {
            console.log('Restaurando imagen original de identidad');
            // Restaurar el elemento original completo
            const clonedOriginal = originalIdentityLogo.cloneNode(true);
            currentIdentityLogo.parentNode.replaceChild(clonedOriginal, currentIdentityLogo);
        } else {
            // Si no tenemos el original guardado, recargar la página como fallback
            console.log('No se pudo restaurar la imagen, recargando...');
            location.reload();
        }
        
        saveLogoSection.classList.add('d-none');
    });

    // Validar antes de enviar el formulario
    const logoForm = document.getElementById('logoForm');
    logoForm.addEventListener('submit', function(e) {
        const file = logoUpload.files[0];
        
        if (!file) {
            e.preventDefault();
            alert('Por favor selecciona una imagen antes de guardar.');
            return false;
        }
        
        console.log('Submitting form with file:', {
            name: file.name,
            size: file.size,
            type: file.type
        });
        
        // Deshabilitar botón para evitar doble envío
        const submitBtn = logoForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
        }
    });
});
</script>

{{-- Script de Google Maps (UNIFICADO) --}}
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
            styles: [{ featureType: 'poi', stylers: [{ visibility: 'off' }] }]
        });
        new google.maps.Marker({
            position: businessLocation,
            map: map,
            title: '{{ $business->business_name }}',
            animation: google.maps.Animation.DROP
        });
        new google.maps.InfoWindow({
            content: '<div class="p-1"><strong>{{ $business->business_name }}</strong></div>'
        }).open(map, marker);
    }
    if (typeof google !== 'undefined') { initMap(); } else { window.addEventListener('load', initMap); }
</script>
@endif

@endpush