@extends('layouts.business-app')

@section('title', 'Tickets de Soporte - Order QR System')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Tickets de Soporte</h2>
            <p class="mb-0">Administre sus solicitudes de ayuda y soporte técnico</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.support.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuevo Ticket
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('business.support.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Filtrar por estado</label>
                    <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abierto</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card border-0 shadow">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">Sus Tickets</h2>
                </div>
            </div>
        </div>
        @if($tickets->count() > 0)
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="border-bottom" scope="col">ID</th>
                        <th class="border-bottom" scope="col">Asunto</th>
                        <th class="border-bottom" scope="col">Estado</th>
                        <th class="border-bottom" scope="col">Respuesta</th>
                        <th class="border-bottom" scope="col">Creado</th>
                        <th class="border-bottom" scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td class="fw-bolder text-gray-500">#{{ $ticket->support_ticket_id }}</td>
                        <td>
                            <a href="{{ route('business.support.show', $ticket->support_ticket_id) }}" class="text-primary fw-bold">
                                {{ Str::limit($ticket->subject, 50) }}
                            </a>
                            @if($ticket->response)
                                <span class="badge bg-success ms-2">
                                    <svg class="icon icon-xxs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                </span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusConfig = [
                                    'open' => ['class' => 'bg-info', 'label' => 'Abierto'],
                                    'in_progress' => ['class' => 'bg-warning', 'label' => 'En Progreso'],
                                    'closed' => ['class' => 'bg-secondary', 'label' => 'Cerrado'],
                                ];
                                $config = $statusConfig[$ticket->status] ?? ['class' => 'bg-secondary', 'label' => 'Desconocido'];
                            @endphp
                            <span class="badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                        </td>
                        <td>
                            @if($ticket->response)
                                <div class="d-flex align-items-center">
                                    <svg class="icon icon-xs text-success me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-success fw-bold small">Respondido</span>
                                </div>
                                @if($ticket->responded_at)
                                <small class="text-muted d-block">{{ $ticket->responded_at->format('d/m/Y') }}</small>
                                @endif
                            @else
                                <div class="d-flex align-items-center">
                                    <svg class="icon icon-xs text-warning me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-muted small">Pendiente</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('business.support.show', $ticket->support_ticket_id) }}" class="btn btn-sm btn-primary">
                                <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            {{ $tickets->links() }}
        </div>
        @endif
        @else
        <div class="card-body text-center py-5">
            <svg class="icon icon-xxl text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <p class="text-gray-600 mb-3">No se encontraron tickets de soporte</p>
            <a href="{{ route('business.support.create') }}" class="btn btn-primary btn-sm">Crear Primer Ticket</a>
        </div>
        @endif
    </div>
</div>
@endsection
