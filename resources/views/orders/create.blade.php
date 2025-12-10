{{--
    Company: CETAM
    Project: FOCUS-LARAVEL-QR
    File: create.blade.php
    Created on: 28/11/2025
    Created by: Vanessa
    Approved by: Alan

    Changelog:
    - ID: 1 | Date: 28/11/2025
        Modified by: Vanessa
        Description: Implementation of payment plans view following FoodFlow design and CETAM standards.
--}}
@extends('layouts.business-app')

@section('title', 'Crear Nueva Orden')

@section('page')
<div class="py-4">
    <div class="row">
        <div class="col-12">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
                <div class="d-block">
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                            <li class="breadcrumb-item">
                                <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                                    <x-icon name="nav.home" />
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('business.orders.index') }}" class="text-primary">Órdenes</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Crear</li>
                        </ol>
                    </nav>
                    <h2 class="h4 mt-1">Crear Nueva Orden</h2>
                    <p class="mb-0 text-muted">Completa los datos para generar una orden con código QR</p>
                </div>
                
            </div>

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            @endif

            <div class="card border-0 shadow">
                
                <div class="card-body">
                    <form action="{{ route('business.orders.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="business_folio" class="form-label">
                                Folio del Negocio <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="business_folio"
                                id="business_folio"
                                class="form-control @error('business_folio') is-invalid @enderror"
                                value="{{ old('business_folio') }}"
                                autocomplete="off">
                            @error('business_folio')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">
                                Descripción de la Orden (opcional)
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="6"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-start gap-3 pt-3 border-top">

                            <button type="submit" class="btn btn-primary">
                                <x-icon name="action.save" class="me-2" />
                                Guardar
                            </button>

                            <a href="{{ route('business.orders.index') }}" class="btn btn-gray-500">
                                Cancelar
                            </a>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection