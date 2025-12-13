{{--
  Company: CETAM
  Project: FQR
  File: login.blade.php
  Created on: 08/10/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.base')

@section('title', 'Inicio de Sesión')

@section('content')
    <main>
        <section class="vh-lg-100 mt-5 mt-lg-0 bg-soft d-flex align-items-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 d-flex align-items-center justify-content-center flex-column">
                        
                        <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500 mb-4">
                            
                            <div class="text-center text-md-center mb-4 mt-md-0">
                                <h1 class="mb-3 h3 fw-bold text-primary">Inicia sesión en Focus</h1>
                            </div>

                            <form method="POST" action="{{ route('business.login') }}" class="mt-4">
                                @csrf

                                <div class="form-group mb-4">
                                    <label for="email">Correo electrónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <x-icon name="msg.email" class="text-gray-600" />
                                        </span>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               placeholder="ejemplo@institucion.com"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               autofocus>
                                    </div>
                                    @error('email')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="password">Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon2">
                                            <x-icon name="access.lock" class="text-gray-600" />
                                        </span>
                                        <input type="password"
                                               placeholder="Contraseña"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password">
                                    </div>
                                    @error('password')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-gray-800">Iniciar Sesión</button>
                                </div>
                            </form>
                            
                            <div class="d-flex justify-content-center align-items-center mt-4">
                                <span class="fw-normal text-primary">
                                    ¿No estás registrado?
                                    <a href="{{ route('business.register') }}" class="text-info">Crear Cuenta</a>
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