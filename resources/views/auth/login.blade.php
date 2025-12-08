{{--
    Company: CETAM
    Project: FQR
    File: auth/login.blade.php
    Description: Login page matching user's original design (Clean & Centered).
    Standard: Section 7.1 & 8.2
--}}
@extends('layouts.base')

@section('title', 'Inicio de Sesión')

@section('content')
    <main>
        {{-- Fondo gris claro --}}
        <section class="vh-lg-100 mt-5 mt-lg-0 bg-soft d-flex align-items-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-column">
                        
                        <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500 mb-4">
                            
                            <div class="text-center text-md-center mb-4 mt-md-0">
                                <h1 class="mb-0 h3">Inicio de Sesión</h1>
                            </div>

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <x-icon name="check" class="me-2" />
                                    <strong>¡Éxito!</strong> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- ELIMINADO: El bloque de $errors->any() general para evitar la alerta superior --}}

                            <form method="POST" action="{{ route('business.login') }}" class="mt-4">
                                @csrf

                                <div class="form-group mb-4">
                                    <label for="email">Correo electrónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <x-icon name="email" class="text-gray-600" />
                                        </span>
                                        {{-- 
                                            LOGICA APLICADA:
                                            1. value="{{ old('email') }}" mantiene el correo escrito si falla.
                                            2. @error('email') is-invalid @enderror agrega la clase de error (borde rojo e icono).
                                        --}}
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               placeholder="ejemplo@institucion.com" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               required 
                                               autofocus>
                                    </div>
                                    
                                    {{-- MENSAJE DE ERROR ESPECÍFICO DEL EMAIL --}}
                                    @error('email')
                                        <div class="text-danger mt-2 small fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="password">Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon2">
                                            <x-icon name="lock" class="text-gray-600" />
                                        </span>
                                        <input type="password" 
                                               placeholder="••••••••" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required>
                                    </div>
                                    {{-- MENSAJE DE ERROR ESPECÍFICO DE PASSWORD (Opcional) --}}
                                    @error('password')
                                        <div class="text-danger mt-2 small fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    {{-- Espacio para "Recordarme" o "Olvidé contraseña" si se requiere a futuro --}}
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-gray-800">Iniciar Sesión</button>
                                </div>
                            </form>
                            
                            <div class="d-flex justify-content-center align-items-center mt-4">
                                <span class="fw-normal">
                                    ¿No estás registrado?
                                    <a href="{{ route('business.register') }}" class="fw-bold text-info ms-1">Crear Cuenta</a>
                                </span>
                            </div>

                        </div> 

                        @include('partials.footer')

                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection