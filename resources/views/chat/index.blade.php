@extends('layouts.order-qr')

@section('title', 'Chat de Órdenes')
@section('page-title', 'Chat de Órdenes')

@section('content')
<div class="flex gap-6 h-[calc(100vh-180px)]">
    {{-- Left Panel: Orders List --}}
    <div class="w-96 bg-white rounded-lg shadow flex flex-col">
        {{-- Search Bar --}}
        <div class="p-4 border-b border-gray-200">
            <input type="text"
                   id="search-orders"
                   placeholder="Buscar orden..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Orders List --}}
        <div class="flex-1 overflow-y-auto divide-y divide-gray-200">
            @forelse($activeOrders as $order)
                <div class="p-4 hover:bg-gray-50 cursor-pointer transition order-item"
                     data-order-id="{{ $order->order_id }}"
                     onclick="selectOrder('{{ $order->order_id }}', '{{ $order->customer_name }}', '{{ $order->status }}')">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-gray-900">#{{ $order->order_number }}</span>
                            @if($order->status === 'pending')
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Pendiente</span>
                            @elseif($order->status === 'ready')
                                <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Lista</span>
                            @elseif($order->status === 'in_progress')
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">En progreso</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mb-1">{{ $order->customer_name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Str::limit($order->items, 50) }}</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-bold text-blue-600">${{ number_format($order->total_amount, 2) }} MXN</span>
                        <span class="flex items-center space-x-1 text-xs text-gray-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Chat</span>
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm mb-2">No hay órdenes activas</p>
                    <a href="{{ route('orders.create') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                        Crear nueva orden
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Info Note --}}
        <div class="p-4 bg-blue-50 border-t border-blue-100">
            <div class="flex items-start space-x-2 text-xs text-blue-800">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p>Selecciona una orden para ver el chat. Solo órdenes activas de hoy.</p>
            </div>
        </div>
    </div>

    {{-- Right Panel: Chat Window --}}
    <div class="flex-1 bg-white rounded-lg shadow flex flex-col" id="chat-panel">
        {{-- Empty State (when no order selected) --}}
        <div id="empty-state" class="flex-1 flex items-center justify-center">
            <div class="text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Selecciona una orden</h3>
                <p class="text-gray-500">Haz clic en una orden de la lista para ver la conversación</p>
            </div>
        </div>

        {{-- Chat Interface (hidden initially) --}}
        <div id="chat-interface" class="hidden flex-1 flex flex-col">
            {{-- Chat Header --}}
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg" id="chat-order-number">#ORD-001</h3>
                        <p class="text-sm text-gray-600" id="chat-customer-name">Cliente</p>
                    </div>
                    <span id="chat-status-badge" class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Pendiente</span>
                </div>
            </div>

            {{-- Messages Area --}}
            <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
                {{-- Messages will be loaded here dynamically --}}
                <div class="text-center text-gray-500 text-sm">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    <p>Cargando conversación...</p>
                </div>
            </div>

            {{-- Message Input --}}
            <div class="border-t border-gray-200 p-4 bg-white">
                <form id="chat-form" onsubmit="sendMessage(event)" class="flex space-x-3">
                    <input type="text"
                           id="chat-input"
                           placeholder="Escribe un mensaje al cliente..."
                           class="flex-1 border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           autocomplete="off">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-6 py-3 transition flex-shrink-0 font-semibold">
                        Enviar
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">Presiona Enter para enviar</p>
            </div>
        </div>
    </div>
</div>

<style>
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

    .order-item.selected {
        background-color: #EFF6FF;
        border-left: 4px solid #2563EB;
    }
</style>

<script>
    let currentOrderId = null;

    function selectOrder(orderId, customerName, status) {
        currentOrderId = orderId;

        // Update UI
        document.getElementById('empty-state').classList.add('hidden');
        document.getElementById('chat-interface').classList.remove('hidden');

        // Update header
        document.getElementById('chat-order-number').textContent = '#' + orderId;
        document.getElementById('chat-customer-name').textContent = customerName;

        // Update status badge
        const statusBadge = document.getElementById('chat-status-badge');
        statusBadge.textContent = getStatusText(status);
        statusBadge.className = 'px-3 py-1 text-xs font-semibold rounded-full ' + getStatusClass(status);

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
            'ready': 'Lista',
            'in_progress': 'En progreso',
            'delivered': 'Entregada'
        };
        return statusMap[status] || status;
    }

    function getStatusClass(status) {
        const classMap = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'ready': 'bg-green-100 text-green-800',
            'in_progress': 'bg-blue-100 text-blue-800',
            'delivered': 'bg-gray-100 text-gray-800'
        };
        return classMap[status] || 'bg-gray-100 text-gray-800';
    }

    function loadMessages(orderId) {
        const messagesContainer = document.getElementById('chat-messages');
        messagesContainer.innerHTML = '<div class="text-center text-gray-500"><p>Cargando mensajes...</p></div>';

        // TODO: Replace with actual API call
        // For now, load demo messages
        setTimeout(() => {
            messagesContainer.innerHTML = '';

            const demoMessages = [
                { sender: 'customer', message: '¿Cuánto tiempo falta para mi orden?', time: '10:35 AM', initials: 'CL' },
                { sender: 'business', message: 'Tu orden estará lista en aproximadamente 10 minutos. Te avisaremos cuando esté lista.', time: '10:36 AM' },
                { sender: 'customer', message: 'Perfecto, gracias!', time: '10:37 AM', initials: 'CL' }
            ];

            demoMessages.forEach(msg => {
                addMessageToDOM(msg.message, msg.sender, msg.time, msg.initials);
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
        // sendMessageToAPI(currentOrderId, message);
    }

    function addMessageToDOM(message, sender, time, initials = 'NE') {
        const messagesContainer = document.getElementById('chat-messages');

        const messageDiv = document.createElement('div');

        if (sender === 'business') {
            messageDiv.className = 'flex justify-end';
            messageDiv.innerHTML = `
                <div class="bg-blue-600 text-white rounded-lg p-4 shadow-sm max-w-[70%]">
                    <p class="text-sm leading-relaxed">${message}</p>
                    <span class="text-xs text-blue-100 mt-2 block">${time}</span>
                </div>
            `;
        } else {
            messageDiv.className = 'flex items-start space-x-3';
            messageDiv.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-sm font-bold flex-shrink-0">
                    ${initials}
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm max-w-[70%] border border-gray-200">
                    <p class="text-sm text-gray-800 leading-relaxed">${message}</p>
                    <span class="text-xs text-gray-500 mt-2 block">${time}</span>
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

    // TODO: Integration points
    // - loadMessages(orderId) - Load real messages from API
    // - sendMessageToAPI(orderId, message) - Send message via API
    // - WebSocket integration for real-time updates
    // - Notification sound when new message arrives
</script>
@endsection
