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

                    {{-- PASO 1: Datos del Negocio --}}
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

                    {{-- PASO 2: Datos de Acceso --}}
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

                    {{-- PASO 3: Elige tu Plan --}}
                    @if($currentStep === 3)
                        <h4 class="mb-4 text-center">Elige tu Plan</h4>
                        <div class="row g-3">
                            @foreach($plans as $plan)
                            <div class="col-12 col-md-4">
                                <div class="card h-100 {{ $plan_id == $plan->plan_id ? 'border-primary shadow' : 'border-light' }}"
                                     wire:click="selectPlan({{ $plan->plan_id }})"
                                     style="cursor: pointer; transition: all 0.3s; border-width: 2px;"
                                     onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 0.5rem 1rem rgba(0,0,0,0.15)'"
                                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                                    <div class="card-body text-center p-4">
                                        <h5 class="fw-bold mb-3" style="color: #262B40;">{{ $plan->name }}</h5>

                                        <div class="my-4">
                                            <h2 class="fw-bold mb-0" style="color: #262B40;">
                                                ${{ number_format($plan->price, 0) }}
                                            </h2>
                                            <span class="small text-muted">MXN</span>
                                        </div>

                                        <p class="small text-muted mb-3" style="min-height: 40px;">{{ $plan->description }}</p>

                                        <div class="border-top pt-3 mt-3">
                                            <p class="small text-muted mb-2">
                                                <x-icon name="check" class="text-success me-1"/>
                                                <span class="fw-semibold">{{ $plan->duration_days }} días</span>
                                            </p>
                                        </div>

                                        @if($plan_id == $plan->plan_id)
                                            <div class="mt-3">
                                                <span class="badge bg-primary px-3 py-2">
                                                    <x-icon name="check" class="me-1"/> Seleccionado
                                                </span>
                                            </div>
                                        @else
                                            <div class="mt-3">
                                                <span class="badge bg-light text-dark px-3 py-2">
                                                    Click para seleccionar
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="form-check mt-4 p-3 bg-light rounded">
                            <input class="form-check-input" type="checkbox" wire:model="terms" id="terms" style="margin-top: 0.35rem;">
                            <label class="form-check-label ms-2" for="terms" style="cursor: pointer;">
                                He leído y acepto los <a href="#" class="text-primary text-decoration-underline">términos y condiciones</a>
                            </label>
                            @error('terms') <div class="text-danger small d-block mt-2">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    {{-- Botones de Navegación --}}
                    <div class="mt-4">
                        @if($currentStep < 3)
                            <div class="d-grid">
                                <button wire:click="nextStep" class="btn btn-gray-800">
                                    Siguiente <x-icon name="forward" class="ms-2"/>
                                </button>
                            </div>
                        @else
                            <div class="d-grid">
                                <button wire:click="submit" class="btn btn-gray-800" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Finalizar Registro</span>
                                    <span wire:loading>Procesando...</span>
                                </button>
                            </div>
                        @endif

                        @if($currentStep > 1)
                            <div class="d-grid mt-2">
                                <button wire:click="previousStep" class="btn btn-link text-gray-600 text-decoration-none">
                                    <x-icon name="back" class="me-2"/> Anterior
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Link a Login --}}
                    <div class="d-flex justify-content-center align-items-center mt-4">
                        <span class="fw-normal">
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