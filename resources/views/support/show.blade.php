@extends('layouts.business-app')

@section('title', 'Ticket #' . $supportTicket->support_ticket_id . ' - Order QR System')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.support.index') }}">Tickets de Soporte</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Ticket #{{ $supportTicket->support_ticket_id }}</li>
                </ol>
            </nav>
            <h2 class="h4">Ticket #{{ $supportTicket->support_ticket_id }}</h2>
            <p class="mb-0">Detalles de tu solicitud de soporte</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            @if($supportTicket->status === 'open')
                <a href="{{ route('business.support.edit', $supportTicket->support_ticket_id) }}" class="btn btn-sm btn-outline-primary me-2">
                    <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                    </svg>
                    Editar
                </a>
                <form method="POST" action="{{ route('business.support.close', $supportTicket->support_ticket_id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Cerrar Ticket
                    </button>
                </form>
            @elseif($supportTicket->status === 'closed')
                <form method="POST" action="{{ route('business.support.reopen', $supportTicket->support_ticket_id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                        </svg>
                        Reabrir Ticket
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Alerts -->
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

    <!-- Ticket Details Card -->
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
            <!-- Info Row -->
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

            <!-- Description -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Descripcion del Problema</h6>
                <div class="bg-light rounded p-3">
                    <p class="mb-0 text-gray-700" style="white-space: pre-wrap;">{{ $supportTicket->description }}</p>
                </div>
            </div>

            <!-- Attachment -->
            @if($supportTicket->attachment_url)
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Archivo Adjunto</h6>
                <a href="{{ $supportTicket->attachment_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                    <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                    </svg>
                    Ver archivo adjunto
                </a>
            </div>
            @endif

            <!-- Admin Response -->
            @if($supportTicket->response)
            <div class="border-top pt-4">
                <h6 class="fw-bold mb-3">Respuesta del Administrador</h6>
                <div class="alert alert-success" role="alert">
                    <div class="d-flex align-items-start">
                        <svg class="icon icon-sm me-2 mt-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-grow-1">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $supportTicket->response }}</p>
                            @if($supportTicket->responded_at)
                            <small class="text-muted d-block mt-2">
                                <svg class="icon icon-xxs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                Respondido el {{ $supportTicket->responded_at->format('d/m/Y H:i') }}
                            </small>
                            @endif

                            <!-- Response Attachment -->
                            @if($supportTicket->response_attachment_url)
                            <div class="mt-3">
                                <a href="{{ $supportTicket->response_attachment_url }}" target="_blank" class="btn btn-outline-success btn-sm">
                                    <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                    </svg>
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
