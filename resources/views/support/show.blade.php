{{--
  Company: CETAM
  Project: FQR
  File: show.blade.php
  Created on: 05/12/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno

  Changelog:
  - ID: 1 | Date: 08/12/2025
    Modified by: Alan Jesus Garcia Nava
    Description: Changed the layout to business-app and updated the breadcrumb navigation.
--}}
@extends('layouts.business-app')

@section('title', 'Ticket #' . $supportTicket->support_ticket_id . ' - Order QR System')

@section('page')
<div class="py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                            <x-icon name="home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.support.index') }}" class="text-primary">Tickets de Soporte</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Ticket #{{ $supportTicket->support_ticket_id }}</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Ticket #{{ $supportTicket->support_ticket_id }}</h2>
            <p class="mb-0 text-muted">Detalles de tu solicitud de soporte</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            @if($supportTicket->status === 'open')
            <a href="{{ route('business.support.edit', $supportTicket->support_ticket_id) }}" class="btn btn-sm btn-primary me-2 d-inline-flex align-items-center">
                <x-icon name="edit" class="me-2"/> Editar
            </a>
            <form method="POST" action="{{ route('business.support.close', $supportTicket->support_ticket_id) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">
                    Cerrar Ticket
                </button>
            </form>
            @elseif($supportTicket->status === 'closed')
            <form method="POST" action="{{ route('business.support.reopen', $supportTicket->support_ticket_id) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                     Reabrir Ticket
                </button>
            </form>
            @endif
        </div>
    </div>
   

    {{-- Alerts --}}
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

    {{-- Ticket Details Card --}}
    <div class="card border-0 shadow mb-4">
        <div class="card-header border-bottom">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="h5 mb-0">{{ $supportTicket->subject }}</h3>
                </div>
                <div class="col-auto">
                    @php
                        $statusConfig = [
                            'open' => ['class' => 'bg-info', 'label' => 'Abierto'],
                            'in_progress' => ['class' => 'bg-warning', 'label' => 'En Progreso'],
                            'closed' => ['class' => 'bg-secondary', 'label' => 'Cerrado'],
                        ];
                        $config = $statusConfig[$supportTicket->status] ?? ['class' => 'bg-secondary', 'label' => 'Desconocido'];
                    @endphp
                    <span class="badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- Info Row --}}
            <div class="row mb-4">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="d-flex flex-column">
                        <small class="text-gray-500 mb-1">Prioridad</small>
                        @php
                            $priorityConfig = [
                                'low' => ['class' => 'bg-success', 'label' => 'Baja'],
                                'medium' => ['class' => 'bg-warning', 'label' => 'Media'],
                                'high' => ['class' => 'bg-danger', 'label' => 'Alta'],
                            ];
                            $pConfig = $priorityConfig[$supportTicket->priority] ?? ['class' => 'bg-secondary', 'label' => 'Desconocido'];
                        @endphp
                        <span class="badge {{ $pConfig['class'] }}">{{ $pConfig['label'] }}</span>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="d-flex flex-column">
                        <small class="text-gray-500 mb-1">Fecha de Creacion</small>
                        <span class="fw-bold">{{ $supportTicket->created_at->format('d/m/Y') }}</span>
                        <small class="text-muted">{{ $supportTicket->created_at->format('H:i') }}</small>
                    </div>
                </div>
                @if($supportTicket->responded_at)
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="d-flex flex-column">
                        <small class="text-gray-500 mb-1">Respondido</small>
                        <span class="fw-bold">{{ $supportTicket->responded_at->format('d/m/Y') }}</span>
                        <small class="text-muted">{{ $supportTicket->responded_at->format('H:i') }}</small>
                    </div>
                </div>
                @endif
                @if($supportTicket->closed_at)
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="d-flex flex-column">
                        <small class="text-gray-500 mb-1">Cerrado</small>
                        <span class="fw-bold">{{ $supportTicket->closed_at->format('d/m/Y') }}</span>
                        <small class="text-muted">{{ $supportTicket->closed_at->format('H:i') }}</small>
                    </div>
                </div>
                @endif
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Descripcion del Problema</h6>
                <div class="bg-light rounded p-3">
                    <p class="mb-0 text-gray-700" style="white-space: pre-wrap;">{{ $supportTicket->description }}</p>
                </div>
            </div>

            {{-- Attachment --}}
            @if($supportTicket->attachment_url)
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Archivo Adjunto</h6>
                <a href="{{ $supportTicket->attachment_url }}" target="_blank" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center">
                    <x-icon name="attachment" class="me-2" />
                    Ver archivo adjunto</a>
                </div>
                @endif

            {{-- Admin Response --}}
            @if($supportTicket->response)
            <div class="border-top pt-4">
                <h6 class="fw-bold mb-3">Respuesta del Administrador</h6>
                <div class="alert alert-success" role="alert">
                    <div class="d-flex align-items-start">
                        <x-icon name="success" class="me-2 mt-1 flex-shrink-0" />
                        <div class="flex-grow-1">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $supportTicket->response }}</p>
                            @if($supportTicket->responded_at)
                            <small class="text-success-emphasis d-block mt-2">
                                <x-icon name="clock" class="me-1" />
                                Respondido el {{ $supportTicket->responded_at->format('d/m/Y H:i') }}
                            </small>
                            @endif
                            @if($supportTicket->response_attachment_url)
                            <div class="mt-3">
                                
                                <a href="{{ $supportTicket->response_attachment_url }}" target="_blank" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center">
                                    <x-icon name="attachment" class="me-2" />
                                    Ver archivo adjunto de la respuesta
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
