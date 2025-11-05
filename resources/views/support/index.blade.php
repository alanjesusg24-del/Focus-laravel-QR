@extends('layouts.order-qr')

@section('title', 'Tickets de Soporte')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tickets de Soporte</h1>
            <p class="text-gray-600 mt-1">Gestiona tus solicitudes de ayuda y soporte técnico</p>
        </div>
        <a href="{{ route('support.create') }}">
            <x-button variant="primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Ticket
            </x-button>
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <x-alert type="success" class="mb-4">
            {{ session('success') }}
        </x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error" class="mb-4">
            {{ session('error') }}
        </x-alert>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="{{ route('support.index') }}" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por estado</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-institutional-blue focus:border-transparent" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abiertos</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Cerrados</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Tickets List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($tickets->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asunto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $ticket->support_ticket_id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <a href="{{ route('support.show', $ticket->support_ticket_id) }}" class="text-institutional-blue hover:underline font-medium">
                                    {{ Str::limit($ticket->subject, 50) }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $priorityColors = [
                                        'low' => 'bg-green-100 text-green-800',
                                        'medium' => 'bg-yellow-100 text-yellow-800',
                                        'high' => 'bg-red-100 text-red-800'
                                    ];
                                    $priorityLabels = [
                                        'low' => 'Baja',
                                        'medium' => 'Media',
                                        'high' => 'Alta'
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityColors[$ticket->priority] }}">
                                    {{ $priorityLabels[$ticket->priority] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'open' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                                        'closed' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusLabels = [
                                        'open' => 'Abierto',
                                        'in_progress' => 'En Progreso',
                                        'closed' => 'Cerrado'
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$ticket->status] }}">
                                    {{ $statusLabels[$ticket->status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('support.show', $ticket->support_ticket_id) }}" class="text-institutional-blue hover:text-institutional-blue/80 mr-3">
                                    Ver
                                </a>
                                @if($ticket->status === 'open')
                                    <a href="{{ route('support.edit', $ticket->support_ticket_id) }}" class="text-green-600 hover:text-green-900">
                                        Editar
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50">
                {{ $tickets->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay tickets de soporte</h3>
                <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo ticket de soporte.</p>
                <div class="mt-6">
                    <a href="{{ route('support.create') }}">
                        <x-button variant="primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Crear Ticket
                        </x-button>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
