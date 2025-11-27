<div class="vh-lg-100 mt-5 mt-lg-0 bg-soft d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 d-flex align-items-center justify-content-center flex-column">

                {{-- TARJETA BLANCA --}}
                <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500 mb-4">

                    {{-- Título Principal --}}
                    <div class="text-center text-md-center mb-4 mt-md-0">
                        <h1 class="mb-0 h3">Crear Cuenta</h1>
                    </div>

                    {{-- Mostrar errores de sesión --}}
                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <x-icon name="error" class="me-2" />
                            <strong>Error:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <x-icon name="check" class="me-2" />
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- PASO 1: Datos del Negocio (SIN CAMBIOS) --}}
                    @if($currentStep === 1)
                        <h4 class="mb-4">Datos del Negocio</h4>
                        <div class="mb-3">
                            <label class="form-label">Nombre del Negocio <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><x-icon name="store" class="text-gray-600"/></span>
                                <input type="text" wire:model="business_name" class="form-control @error('business_name') is-invalid @enderror" placeholder="Ej. Cafetería Central">
                            </div>
                            @error('business_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">RFC <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><x-icon name="idCard" class="text-gray-600"/></span>
                                <input type="text" wire:model="rfc" class="form-control @error('rfc') is-invalid @enderror" placeholder="ABC123456XYZ">
                            </div>
                            @error('rfc') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><x-icon name="phone" class="text-gray-600"/></span>
                                <input type="tel" wire:model="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="5512345678">
                            </div>
                            @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- PASO 2: Datos de Acceso (SIN CAMBIOS) --}}
                    @if($currentStep === 2)
                        <h4 class="mb-4">Datos de Acceso</h4>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><x-icon name="email" class="text-gray-600"/></span>
                                <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="correo@ejemplo.com">
                            </div>
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><x-icon name="lock" class="text-gray-600"/></span>
                                <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres">
                            </div>
                            @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><x-icon name="lock" class="text-gray-600"/></span>
                                <input type="password" wire:model="password_confirmation" class="form-control" placeholder="Repite tu contraseña">
                            </div>
                        </div>
                    @endif

                    {{-- PASO 3: Elige tu Plan (Horizontal) --}}
                    @if($currentStep === 3)
                        <h4 class="mb-4 text-center">Elige tu Plan</h4>
                        <div class="d-flex flex-column gap-3">
                            @foreach($plans as $plan)
                            <div class="card p-3 border rounded transition-all plan-card-hover {{ $plan_id == $plan->plan_id ? 'border-primary bg-primary-soft shadow-sm' : 'border-light bg-white' }}"
                                 wire:click="selectPlan({{ $plan->plan_id }})"
                                 style="cursor: pointer; position: relative;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="radio" {{ $plan_id == $plan->plan_id ? 'checked' : '' }} style="pointer-events: none; transform: scale(1.1);">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">{{ $plan->name }}</h6>
                                            <p class="mb-0 small text-muted text-wrap lh-sm" style="max-width: 200px;">{{ $plan->description }}</p>
                                        </div>
                                    </div>
                                    <div class="text-end ms-auto mt-2 mt-sm-0">
                                        <h4 class="mb-0 fw-bolder text-dark" style="line-height: 1;">${{ number_format($plan->price, 0) }} <span class="fs-6 text-muted fw-normal">/MXN</span></h4>
                                        <span class="badge bg-white text-dark border mt-1 shadow-sm">{{ $plan->duration_days }} días</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="form-check mt-4 p-3 bg-light rounded border border-light">
                            <input class="form-check-input" type="checkbox" wire:model="terms" id="terms" style="margin-top: 0.3rem;">
                            <label class="form-check-label ms-2 small" for="terms" style="cursor: pointer;">He leído y acepto los <a href="#" class="text-primary text-decoration-underline">términos y condiciones</a></label>
                            @error('terms') <div class="text-danger small d-block mt-2">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    {{-- ================================================================== --}}
                    {{-- BOTONES DE NAVEGACIÓN (VERDE CORREGIDO) --}}
                    {{-- ================================================================== --}}
                    <div class="mt-5 d-flex align-items-center {{ $currentStep > 1 ? 'justify-content-between' : 'justify-content-end' }}">
                        
                        {{-- Botón Atrás (Rojo) --}}
                        @if($currentStep > 1)
                            <button wire:click="previousStep" class="btn btn-danger px-4">
                                <x-icon name="back" class="me-2"/> Atrás
                            </button>
                        @endif

                        {{-- Botón Siguiente (Gris) --}}
                        @if($currentStep < 3)
                            <button wire:click="nextStep" class="btn btn-gray-800 px-4">
                                Siguiente <x-icon name="forward" class="ms-2"/>
                            </button>
                        @else
                            {{-- Último paso: Botón Registrar (AHORA VERDE) --}}
                            <button wire:click="submit" class="btn btn-success px-4" wire:loading.attr="disabled">
                                <span wire:loading.remove>Registrar</span>
                                <span wire:loading>Procesando...</span>
                            </button>
                        @endif
                    </div>
                    {{-- ================================================================== --}}

                    {{-- Link a Login --}}
                    <div class="d-flex justify-content-center align-items-center mt-4">
                        <span class="fw-normal small">
                            ¿Ya tienes cuenta?
                            <a href="{{ route('business.login') }}" class="fw-bold text-dark ms-1">Inicia Sesión</a>
                        </span>
                    </div>

                </div> {{-- Fin de Tarjeta --}}

                {{-- FOOTER --}}
                @include('partials.footer')

            </div>
        </div>
    </div>
</div>