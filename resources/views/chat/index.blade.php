@extends('layouts.business-app')

@section('title', 'Chat de Órdenes')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Chat de Órdenes</h2>
            <p class="mb-0">Comunícate con tus clientes en tiempo real</p>
        </div>
    </div>

    <div class="row" style="height: calc(100vh - 250px);">
        <!-- Left Panel: Orders List -->
        <div class="col-12 col-lg-4 mb-4 mb-lg-0">
            <div class="card border-0 shadow h-100 d-flex flex-column">
                <!-- Search Bar -->
                <div class="card-header border-bottom">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <input type="text" id="search-orders" class="form-control" placeholder="Buscar orden...">
                    </div>
                </div>

                <!-- Orders List -->
                <div class="flex-fill overflow-auto" style="max-height: calc(100vh - 400px);">
                    @forelse($activeOrders as $order)
                    <div class="p-3 border-bottom order-item"
                         style="cursor: pointer;"
                         data-order-id="{{ $order->order_id }}"
                         onclick="selectOrder('{{ $order->order_id }}', '{{ $order->folio_number }}', '{{ $order->status }}')">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-gray-900">{{ $order->folio_number }}</span>
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'bg-warning', 'label' => 'Pendiente'],
                                        'ready' => ['class' => 'bg-success', 'label' => 'Listo'],
                                        'delivered' => ['class' => 'bg-info', 'label' => 'Entregado'],
                                    ];
                                    $config = $statusConfig[$order->status] ?? ['class' => 'bg-secondary', 'label' => 'Desconocido'];
                                @endphp
                                <span class="badge {{ $config['class'] }} badge-sm">{{ $config['label'] }}</span>
                            </div>
                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                        </div>
                        <p class="text-sm text-gray-700 mb-1">{{ Str::limit($order->description ?? 'Sin descripción', 40) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-primary fw-bold">Token: {{ $order->pickup_token }}</small>
                            <small class="text-muted">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                                </svg>
                                Chat
                            </small>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center">
                        <svg class="icon icon-xl text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-muted small mb-2">No hay órdenes activas</p>
                        <a href="{{ route('business.orders.create') }}" class="btn btn-sm btn-primary">Crear nueva orden</a>
                    </div>
                    @endforelse
                </div>

                <!-- Info Note -->
                <div class="card-footer bg-soft border-top">
                    <div class="d-flex align-items-start gap-2">
                        <svg class="icon icon-xs text-info mt-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <small class="text-muted">Selecciona una orden para ver el chat. Solo órdenes activas de hoy.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Chat Window -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow h-100 d-flex flex-column" id="chat-panel">
                <!-- Empty State -->
                <div id="empty-state" class="flex-fill d-flex align-items-center justify-content-center">
                    <div class="text-center p-5">
                        <svg class="icon icon-xxl text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h4 class="h5 mb-2">Selecciona una orden</h4>
                        <p class="text-muted">Haz clic en una orden de la lista para ver la conversación</p>
                    </div>
                </div>

                <!-- Chat Interface -->
                <div id="chat-interface" class="d-none flex-fill d-flex flex-column">
                    <!-- Chat Header -->
                    <div class="card-header bg-light border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1" id="chat-order-number">#ORD-001</h5>
                                <small class="text-muted" id="chat-customer-info">Información del cliente</small>
                            </div>
                            <span id="chat-status-badge" class="badge bg-warning">Pendiente</span>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div id="chat-messages" class="flex-fill overflow-auto p-4 bg-soft" style="max-height: calc(100vh - 450px);">
                        <div class="text-center text-muted py-5">
                            <svg class="icon icon-lg text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <p>Cargando conversación...</p>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="card-footer border-top bg-white">
                        <form id="chat-form" onsubmit="sendMessage(event)">
                            <div class="input-group">
                                <input type="text"
                                       id="chat-input"
                                       class="form-control"
                                       placeholder="Escribe un mensaje al cliente..."
                                       autocomplete="off">
                                <button type="submit" class="btn btn-primary">
                                    <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Enviar
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2">Presiona Enter para enviar</small>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .order-item:hover {
        background-color: #f8f9fa;
    }
    .order-item.selected {
        background-color: #e7f3ff;
        border-left: 4px solid #0d6efd !important;
    }
    #chat-messages::-webkit-scrollbar {
        width: 8px;
    }
    #chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    #chat-messages::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<script>
    let currentOrderId = null;

    function selectOrder(orderId, folioNumber, status) {
        currentOrderId = orderId;

        // Update UI
        document.getElementById('empty-state').classList.add('d-none');
        document.getElementById('chat-interface').classList.remove('d-none');

        // Update header
        document.getElementById('chat-order-number').textContent = folioNumber;
        document.getElementById('chat-customer-info').textContent = 'Token: ' + orderId;

        // Update status badge
        const statusBadge = document.getElementById('chat-status-badge');
        statusBadge.textContent = getStatusText(status);
        statusBadge.className = 'badge ' + getStatusClass(status);

        // Highlight selected order
        document.querySelectorAll('.order-item').forEach(item => {
            item.classList.remove('selected');
        });
        document.querySelector(`[data-order-id="${orderId}"]`).classList.add('selected');

        // Load messages
        loadMessages(orderId);
    }

    function getStatusText(status) {
        const statusMap = {
            'pending': 'Pendiente',
            'ready': 'Listo',
            'delivered': 'Entregado',
            'cancelled': 'Cancelado'
        };
        return statusMap[status] || status;
    }

    function getStatusClass(status) {
        const classMap = {
            'pending': 'bg-warning',
            'ready': 'bg-success',
            'delivered': 'bg-info',
            'cancelled': 'bg-danger'
        };
        return classMap[status] || 'bg-secondary';
    }

    function loadMessages(orderId) {
        const messagesContainer = document.getElementById('chat-messages');
        messagesContainer.innerHTML = '<div class="text-center text-muted py-5"><p>Cargando mensajes...</p></div>';

        // Demo messages
        setTimeout(() => {
            messagesContainer.innerHTML = '';

            const demoMessages = [
                { sender: 'customer', message: '¿Cuánto tiempo falta para mi orden?', time: '10:35 AM' },
                { sender: 'business', message: 'Tu orden estará lista en aproximadamente 10 minutos. Te avisaremos cuando esté lista.', time: '10:36 AM' },
                { sender: 'customer', message: 'Perfecto, gracias!', time: '10:37 AM' }
            ];

            demoMessages.forEach(msg => {
                addMessageToDOM(msg.message, msg.sender, msg.time);
            });
        }, 500);
    }

    function sendMessage(event) {
        event.preventDefault();

        const input = document.getElementById('chat-input');
        const message = input.value.trim();

        if (!message || !currentOrderId) return;

        const time = new Date().toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });

        // Add message to DOM
        addMessageToDOM(message, 'business', time);

        // Clear input
        input.value = '';

        // TODO: Send to API
    }

    function addMessageToDOM(message, sender, time) {
        const messagesContainer = document.getElementById('chat-messages');

        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-3';

        if (sender === 'business') {
            messageDiv.innerHTML = `
                <div class="d-flex justify-content-end">
                    <div class="bg-primary text-white rounded p-3 shadow-sm" style="max-width: 70%;">
                        <p class="mb-1 small">${message}</p>
                        <small class="opacity-75">${time}</small>
                    </div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="d-flex align-items-start gap-2">
                    <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; flex-shrink: 0;">
                        <span class="fw-bold">CL</span>
                    </div>
                    <div class="bg-white border rounded p-3 shadow-sm" style="max-width: 70%;">
                        <p class="mb-1 small text-gray-800">${message}</p>
                        <small class="text-muted">${time}</small>
                    </div>
                </div>
            `;
        }

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Enter key to send
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('chat-input');
        if (input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage(e);
                }
            });
        }

        // Search functionality
        const searchInput = document.getElementById('search-orders');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.order-item').forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? 'block' : 'none';
                });
            });
        }
    });
</script>
@endsection
