@extends('layouts.superadmin-app')

@section('title', 'Ticket #' . $ticket->support_ticket_id)

@section('page')
<div class="py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.dashboard') }}">
                    <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.tickets.index') }}">Tickets de Soporte</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Ticket #{{ $ticket->support_ticket_id }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <h1 class="h4">Ticket #{{ $ticket->support_ticket_id }}</h1>
            <p class="mb-0">Detalles y gestion del ticket de soporte</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('superadmin.tickets.index') }}" class="btn btn-sm btn-outline-gray-600">
                <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12 col-xl-8">
        <!-- Ticket Details Card -->
        <div class="card border-0 shadow mb-4">
            <div class="card-header border-bottom">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="h5 mb-0">{{ $ticket->subject }}</h3>
                    </div>
                    <div class="col-auto">
                        @php
                            $statusConfig = [
                                'open' => ['class' => 'bg-primary', 'label' => 'Abierto'],
                                'in_progress' => ['class' => 'bg-warning', 'label' => 'En Progreso'],
                                'resolved' => ['class' => 'bg-success', 'label' => 'Resuelto'],
                                'closed' => ['class' => 'bg-secondary', 'label' => 'Cerrado'],
                            ];
                            $config = $statusConfig[$ticket->status] ?? ['class' => 'bg-secondary', 'label' => 'Desconocido'];
                        @endphp
                        <span class="badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Info Row -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="d-flex flex-column">
                            <small class="text-gray-500 mb-1">Prioridad</small>
                            @php
                                $priorityConfig = [
                                    'low' => ['class' => 'bg-info', 'label' => 'Baja'],
                                    'medium' => ['class' => 'bg-warning', 'label' => 'Media'],
                                    'high' => ['class' => 'bg-danger', 'label' => 'Alta'],
                                ];
                                $pConfig = $priorityConfig[$ticket->priority] ?? ['class' => 'bg-secondary', 'label' => 'Desconocido'];
                            @endphp
                            <span class="badge {{ $pConfig['class'] }}">{{ $pConfig['label'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="d-flex flex-column">
                            <small class="text-gray-500 mb-1">Fecha de Creacion</small>
                            <span class="fw-bold">{{ $ticket->created_at->format('d/m/Y') }}</span>
                            <small class="text-muted">{{ $ticket->created_at->format('H:i') }}</small>
                        </div>
                    </div>
                    @if($ticket->responded_at)
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="d-flex flex-column">
                            <small class="text-gray-500 mb-1">Respondido</small>
                            <span class="fw-bold">{{ $ticket->responded_at->format('d/m/Y') }}</span>
                            <small class="text-muted">{{ $ticket->responded_at->format('H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    @if($ticket->closed_at)
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="d-flex flex-column">
                            <small class="text-gray-500 mb-1">Cerrado</small>
                            <span class="fw-bold">{{ $ticket->closed_at->format('d/m/Y') }}</span>
                            <small class="text-muted">{{ $ticket->closed_at->format('H:i') }}</small>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Descripcion del Problema</h6>
                    <div class="bg-light rounded p-3">
                        <p class="mb-0 text-gray-700" style="white-space: pre-wrap;">{{ $ticket->description }}</p>
                    </div>
                </div>

                <!-- Client Attachment -->
                @if($ticket->attachment_url)
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Archivo Adjunto del Cliente</h6>
                    <a href="{{ $ticket->attachment_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                        </svg>
                        Ver archivo adjunto
                    </a>
                </div>
                @endif

                <!-- Admin Response -->
                @if($ticket->response)
                <div class="border-top pt-4">
                    <h6 class="fw-bold mb-3">Tu Respuesta</h6>
                    <div class="alert alert-success" role="alert">
                        <div class="d-flex align-items-start">
                            <svg class="icon icon-sm me-2 mt-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="flex-grow-1">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $ticket->response }}</p>
                                @if($ticket->responded_at)
                                <small class="text-muted d-block mt-2">
                                    <svg class="icon icon-xxs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $ticket->responded_at->format('d/m/Y H:i') }}
                                </small>
                                @endif

                                <!-- Response Attachment -->
                                @if($ticket->response_attachment_url)
                                <div class="mt-3">
                                    <a href="{{ $ticket->response_attachment_url }}" target="_blank" class="btn btn-outline-success btn-sm">
                                        <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                        </svg>
                                        Ver archivo de respuesta
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

    <div class="col-12 col-xl-4">
        <!-- Business Info Card -->
        <div class="card border-0 shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informacion del Negocio</h5>
            </div>
            <div class="card-body">
                @if($ticket->business)
                    <div class="text-center mb-3">
                        @if($ticket->business->photo)
                            <img src="{{ asset('storage/' . $ticket->business->photo) }}" class="avatar-lg rounded-circle mb-3" alt="{{ $ticket->business->business_name }}">
                        @else
                            <div class="avatar-lg rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center">
                                <span class="text-white fw-bold h3 mb-0">{{ substr($ticket->business->business_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <h5 class="mb-1">{{ $ticket->business->business_name }}</h5>
                        <small class="text-muted">ID: {{ $ticket->business->business_id }}</small>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <small class="text-gray-500">Email</small>
                        <div class="fw-bold">{{ $ticket->business->email }}</div>
                    </div>
                    @if($ticket->business->phone)
                    <div class="mb-2">
                        <small class="text-gray-500">Telefono</small>
                        <div class="fw-bold">{{ $ticket->business->phone }}</div>
                    </div>
                    @endif
                @else
                    <p class="text-muted mb-0">No hay informacion del negocio disponible</p>
                @endif
            </div>
        </div>

        <!-- Actions Card -->
        <div class="card border-0 shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Acciones</h5>
            </div>
            <div class="card-body">
                @if(!$ticket->response)
                    <!-- Respond Button -->
                    <a href="{{ route('superadmin.tickets.respond', $ticket->support_ticket_id) }}" class="btn btn-primary w-100 mb-2">
                        <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        Responder Ticket
                    </a>
                @endif

                <!-- Change Status -->
                @if($ticket->status !== 'in_progress')
                <form method="POST" action="{{ route('superadmin.tickets.updateStatus', $ticket->support_ticket_id) }}" class="mb-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="btn btn-warning w-100">
                        <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Marcar En Progreso
                    </button>
                </form>
                @endif

                @if($ticket->status !== 'closed' && $ticket->response)
                <form method="POST" action="{{ route('superadmin.tickets.updateStatus', $ticket->support_ticket_id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="closed">
                    <button type="submit" class="btn btn-secondary w-100">
                        <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Cerrar Ticket
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
