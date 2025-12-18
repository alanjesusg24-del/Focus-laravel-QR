{{--
  Company: CETAM
  Project: FQR
  File: register-wizard.blade.php
  Created on: 03/12/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno

  Changelog:
  - ID: 1 | Date: 07/12/2025
    Modified by: Dafne Vanessa Castillo Moreno
    Description: 3-step registration wizard. Setting buttons to primary color and cleaning inputs.
--}}

<div class="vh-lg-100 mt-5 mt-lg-0 bg-soft d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 d-flex align-items-center justify-content-center flex-column">

                <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500 mb-4">

                    <div class="text-center text-md-center mb-4 mt-md-0">
                        <h1 class="mb-0 h3">Crear Cuenta</h1>
                    </div>


                    {{-- 1: Business data --}}
                    @if($currentStep === 1)
                        <div class="animate__animated animate__fadeIn">
                            <h4 class="mb-4 text-center">Datos del Negocio</h4>

                            <div class="mb-3">
                                <label class="form-label">Nombre del Negocio <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="business_name"
                                       class="form-control @error('business_name') is-invalid @enderror"
                                       placeholder="Nombre del negocio" maxlength="15">
                                @error('business_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">RFC <span class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="rfc"
                                       class="form-control @error('rfc') is-invalid @enderror"
                                       placeholder="XAXX010101000" maxlength="13"
                                       style="text-transform: uppercase;">
                                @error('rfc') 
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <input type="tel" wire:model.blur="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="10 Digitos" maxlength="10"
                                       pattern="[0-9]*"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('phone') 
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- 2: Access data --}}
                    @if($currentStep === 2)
                        <div class="animate__animated animate__fadeIn">
                            <h4 class="mb-4 text-center">Datos de Acceso</h4>

                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" wire:model.blur="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="ejemplo@institucion.com">
                                @error('email') 
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" wire:model.blur="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres" maxlength="12">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small class="text-muted">Mínimo 8 caracteres, máximo 12.</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" wire:model.blur="password_confirmation"
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       placeholder="Confirmar contraseña" maxlength="12">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- 3: Terms and Conditions --}}
                    @if($currentStep === 3)
                        <div class="animate__animated animate__fadeIn">
                            <h4 class="mb-4 text-center">Finalizar Registro</h4>

                            <div class="form-check mb-4">
                                <input wire:model="terms" class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="termsCheck">
                                <label class="form-check-label" for="termsCheck">
                                    Acepto los 
                                    <a href="#" class="text-info fw-bold text-decoration-none" 
                                       data-bs-toggle="modal" data-bs-target="#termsModal">
                                        Términos de Uso
                                    </a> 
                                    y 
                                    <a href="#" class="text-info fw-bold text-decoration-none" 
                                       data-bs-toggle="modal" data-bs-target="#privacyModal">
                                        Aviso de Privacidad
                                    </a>
                                </label>
                                @error('terms') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Navigation buttons --}}
                    <div class="mt-5 d-flex align-items-center {{ $currentStep > 1 ? 'justify-content-between' : 'justify-content-end' }}">

                        @if($currentStep > 1)
                            <button wire:click="previousStep" class="btn btn-secondary text-white px-4">
                                <x-icon name="nav.back" class="me-2"/> Atrás
                            </button>
                        @endif

                        @if($currentStep < 3)
                            {{-- Next Button --}}
                            <button wire:click="nextStep" class="btn btn-primary px-4">
                                Siguiente <x-icon name="nav.forward" class="ms-2"/>
                            </button>
                        @else
                            <button wire:click="submit"
                                    class="btn btn-primary px-4"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove">
                                    Registrar
                                </span>
                            </button>
                        @endif
                    </div>

                    <div class="d-flex justify-content-center align-items-center mt-4">
                        <span class="fw-normal">
                            ¿Ya tienes cuenta?
                            <a href="{{ route('business.login') }}" class="text-info ">Inicia Sesión</a>
                        </span>
                    </div>

                </div>

                @include('partials.footer')

            </div>
        </div>
    </div>
</div>