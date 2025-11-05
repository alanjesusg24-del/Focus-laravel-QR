@extends('layouts.order-qr')

@section('title', 'Ticket #' . $supportTicket->support_ticket_id)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('business.support.index') }}" class="text-institutional-blue hover:underline flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Tickets
        </a>
    </div>

    @if(session('success'))
        <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error" class="mb-4">{{ $session('error') }}</x-alert>
    @endif

    <div class="bg-white rounded-lg shadow-lg">
        <!-- Header -->
        <div class="border-b border-gray-200 p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $supportTicket->subject }}</h1>
                    <p class="text-sm text-gray-500 mt-1">Ticket #{{ $supportTicket->support_ticket_id }}</p>
                </div>
                <div class="flex gap-2">
                    @if($supportTicket->status === 'open')
                        <a href="{{ route('support.edit', $supportTicket->support_ticket_id) }}">
                            <x-button variant="outline" size="sm">Editar</x-button>
                        </a>
                        <form method="POST" action="{{ route('support.close', $supportTicket->support_ticket_id) }}" class="inline">
                            @csrf
                            <x-button variant="success" size="sm" type="submit">Cerrar Ticket</x-button>
                        </form>
                    @elseif($supportTicket->status === 'closed')
                        <form method="POST" action="{{ route('support.reopen', $supportTicket->support_ticket_id) }}" class="inline">
                            @csrf
                            <x-button variant="primary" size="sm" type="submit">Reabrir</x-button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Estado</p>
                    @php
                        $statusColors = [
                            'open' => 'bg-blue-100 text-blue-800',
                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                            'closed' => 'bg-gray-100 text-gray-800'
                        ];
                        $statusLabels = ['open' => 'Abierto', 'in_progress' => 'En Progreso', 'closed' => 'Cerrado'];
                    @endphp
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$supportTicket->status] }}">
                        {{ $statusLabels[$supportTicket->status] }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Prioridad</p>
                    @php
                        $priorityColors = ['low' => 'bg-green-100 text-green-800', 'medium' => 'bg-yellow-100 text-yellow-800', 'high' => 'bg-red-100 text-red-800'];
                        $priorityLabels = ['low' => 'Baja', 'medium' => 'Media', 'high' => 'Alta'];
                    @endphp
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$supportTicket->priority] }}">
                        {{ $priorityLabels[$supportTicket->priority] }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Creado</p>
                    <p class="text-sm font-medium text-gray-900">{{ $supportTicket->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($supportTicket->resolved_at)
                <div>
                    <p class="text-sm text-gray-500">Resuelto</p>
                    <p class="text-sm font-medium text-gray-900">{{ $supportTicket->resolved_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>

            <!-- Description -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Descripción</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $supportTicket->description }}</p>
                </div>
            </div>

            <!-- Attachment -->
            @if($supportTicket->attachment_url)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Archivo Adjunto</h3>
                <a href="{{ $supportTicket->attachment_url }}" target="_blank" class="inline-flex items-center text-institutional-blue hover:underline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ver archivo adjunto
                </a>
            </div>
            @endif

            <!-- Admin Response -->
            @if($supportTicket->admin_response)
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Respuesta del Administrador</h3>
                <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-institutional-blue">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $supportTicket->admin_response }}</p>
                    @if($supportTicket->responded_at)
                    <p class="text-xs text-gray-500 mt-2">{{ $supportTicket->responded_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
