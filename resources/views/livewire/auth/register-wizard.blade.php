{{--
  Company: CETAM
  Project: Focus QR System
  File: register-wizard.blade.php
  Created on: 03/12/2025
  Created by: Vane
  Approved by:

  Changelog:
  - ID: 1 | Date: 03/12/2025
    Modified by: Vane
    Description: Wizard de registro de 3 pasos. Ajuste de botones a color primario y limpieza de inputs.
    Updated: Títulos centrados.
--}}

<div class="vh-lg-100 mt-5 mt-lg-0 bg-soft d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 d-flex align-items-center justify-content-center flex-column">

                <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500 mb-4">

                    <div class="text-center text-md-center mb-4 mt-md-0">
                        <h1 class="mb-0 h3">Crear Cuenta</h1>
                    </div>

                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- PASO 1: DATOS DEL NEGOCIO --}}
                    @if($currentStep === 1)
                        <div class="animate__animated animate__fadeIn">
                            {{-- Agregado text-center --}}
                            <h4 class="mb-4 text-center">Datos del Negocio</h4>

                            <div class="mb-3">
                                <label class="form-label">Nombre del Negocio <span class="text-danger">*</span></label>
                                <input type="text" wire:model="business_name"
                                       class="form-control @error('business_name') is-invalid @enderror"
                                       placeholder="Ej. Cafetería Central">
                                @error('business_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">RFC <span class="text-danger">*</span></label>
                                <input type="text" wire:model="rfc"
                                       class="form-control @error('rfc') is-invalid @enderror"
                                       placeholder="ABC123456XYZ">
                                @error('rfc') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <input type="tel" wire:model="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="5512345678">
                                @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    {{-- PASO 2: DATOS DE ACCESO --}}
                    @if($currentStep === 2)
                        <div class="animate__animated animate__fadeIn">
                            {{-- Agregado text-center --}}
                            <h4 class="mb-4 text-center">Datos de Acceso</h4>

                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" wire:model="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="correo@ejemplo.com">
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" wire:model="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres">
                                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" wire:model="password_confirmation"
                                       class="form-control"
                                       placeholder="Repite tu contraseña">
                            </div>
                        </div>
                    @endif

                    {{-- PASO 3: FINALIZAR --}}
                    @if($currentStep === 3)
                        <div class="animate__animated animate__fadeIn">
                            {{-- Agregado text-center --}}
                            <h4 class="mb-4 text-center">Finalizar Registro</h4>

                            <div class="form-check mb-4">
                                <input wire:model="terms" class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="termsCheck">
                                <label class="form-check-label" for="termsCheck">
                                    Acepto los <a href="#" class="text-primary fw-bold">Términos y Condiciones</a>
                                </label>
                                @error('terms') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    @endif

                    {{-- BOTONES DE NAVEGACIÓN --}}
                    <div class="mt-5 d-flex align-items-center {{ $currentStep > 1 ? 'justify-content-between' : 'justify-content-end' }}">

                        @if($currentStep > 1)
                            <button wire:click="previousStep" class="btn btn-secondary text-white px-4">
                                <x-icon name="nav.back" class="me-2"/> Atrás
                            </button>
                        @endif

                        @if($currentStep < 3)
                            {{-- Botón Siguiente: Primario --}}
                            <button wire:click="nextStep" class="btn btn-primary px-4">
                                Siguiente <x-icon name="nav.forward" class="ms-2"/>
                            </button>
                        @else
                            {{-- Botón Registrar: Primario --}}
                            <button wire:click="submit" class="btn btn-primary px-4" wire:loading.attr="disabled">
                                <span wire:loading.remove>Registrar</span>
                                <span wire:loading>Procesando...</span>
                            </button>
                        @endif
                    </div>

                    <div class="d-flex justify-content-center align-items-center mt-4">
                        <span class="fw-normal small">
                            ¿Ya tienes cuenta?
                            <a href="{{ route('business.login') }}" class="fw-bold text-info ms-1">Inicia Sesión</a>
                        </span>
                    </div>

                </div>

                @include('partials.footer')

            </div>
        </div>
    </div>
</div>