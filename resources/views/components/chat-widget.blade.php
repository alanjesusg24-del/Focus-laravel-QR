{{-- Chat Sidebar Component --}}
{{-- Only shown if business has chat module enabled --}}
@props(['business'])

@if($business->has_chat_module)
<div id="chat-sidebar" class="fixed inset-y-0 left-0 z-40 w-80 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out -translate-x-full shadow-2xl" style="margin-left: 256px;">
    {{-- Sidebar Content --}}
    <div class="flex flex-col h-full">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <div>
                    <h3 class="font-bold text-lg">Chat de Órdenes</h3>
                    <p class="text-xs text-blue-100">Selecciona una orden para chatear</p>
                </div>
            </div>
            <button onclick="toggleChatSidebar()" class="hover:bg-white/20 rounded p-1 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- View Toggle (Orders List / Chat) --}}
        <div class="flex border-b border-gray-200">
            <button id="orders-tab"
                    onclick="switchTab('orders')"
                    class="flex-1 px-4 py-3 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 bg-blue-50">
                Órdenes Activas
            </button>
            <button id="chat-tab"
                    onclick="switchTab('chat')"
                    class="flex-1 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-blue-600">
                Chat
            </button>
        </div>

        {{-- Orders List View --}}
        <div id="orders-view" class="flex-1 overflow-y-auto">
            {{-- Search Bar --}}
            <div class="p-3 border-b border-gray-200">
                <input type="text"
                       placeholder="Buscar orden..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Orders List --}}
            <div class="divide-y divide-gray-200">
                {{-- Example Order Item --}}
                <div class="p-4 hover:bg-gray-50 cursor-pointer transition" onclick="openChat('ORD-001', 'Juan Pérez')">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-gray-900">#ORD-001</span>
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Pendiente</span>
                        </div>
                        <span class="text-xs text-gray-500">10:30 AM</span>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mb-1">Juan Pérez</p>
                    <p class="text-xs text-gray-500 truncate">2 Tacos al pastor, 1 Refresco</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-bold text-blue-600">$85.00 MXN</span>
                        <span class="flex items-center space-x-1 text-xs text-blue-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                            </svg>
                            <span>2 mensajes</span>
                        </span>
                    </div>
                </div>

                <div class="p-4 hover:bg-gray-50 cursor-pointer transition" onclick="openChat('ORD-002', 'María González')">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-gray-900">#ORD-002</span>
                            <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Listo</span>
                        </div>
                        <span class="text-xs text-gray-500">11:15 AM</span>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mb-1">María González</p>
                    <p class="text-xs text-gray-500 truncate">1 Burrito de pollo</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-bold text-blue-600">$65.00 MXN</span>
                    </div>
                </div>

                <div class="p-4 hover:bg-gray-50 cursor-pointer transition" onclick="openChat('ORD-003', 'Carlos Ramírez')">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-gray-900">#ORD-003</span>
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Pendiente</span>
                        </div>
                        <span class="text-xs text-gray-500">11:45 AM</span>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mb-1">Carlos Ramírez</p>
                    <p class="text-xs text-gray-500 truncate">3 Quesadillas, 2 Refrescos</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-bold text-blue-600">$120.00 MXN</span>
                        <span class="flex items-center space-x-1 text-xs text-red-600">
                            <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                            <span class="font-semibold">1 nuevo</span>
                        </span>
                    </div>
                </div>

                {{-- Empty State --}}
                <div class="hidden p-8 text-center" id="no-orders">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No hay órdenes activas</p>
                </div>
            </div>

            {{-- Info Note --}}
            <div class="p-4 bg-blue-50 border-t border-blue-100">
                <div class="flex items-start space-x-2 text-xs text-blue-800">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p>Solo se muestran órdenes activas de hoy. Los clientes pueden enviarte mensajes sobre sus órdenes.</p>
                </div>
            </div>
        </div>

        {{-- Chat View (Hidden by default) --}}
        <div id="chat-view" class="hidden flex-1 flex flex-col">
            {{-- Chat Header with Order Info --}}
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <button onclick="switchTab('orders')" class="flex items-center text-blue-600 hover:text-blue-700 mb-3">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="text-sm font-semibold">Volver a órdenes</span>
                </button>
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-gray-900" id="chat-order-number">#ORD-001</h4>
                        <p class="text-sm text-gray-600" id="chat-customer-name">Juan Pérez</p>
                    </div>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Pendiente</span>
                </div>
            </div>

            {{-- Messages Area --}}
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                {{-- Customer Message --}}
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-sm font-bold flex-shrink-0">
                        JP
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm max-w-[70%]">
                        <p class="text-sm text-gray-800">Hola, ¿cuánto tiempo falta para mi orden?</p>
                        <span class="text-xs text-gray-500 mt-1 block">10:35 AM</span>
                    </div>
                </div>

                {{-- Business Response --}}
                <div class="flex justify-end">
                    <div class="bg-blue-600 text-white rounded-lg p-3 shadow-sm max-w-[70%]">
                        <p class="text-sm">Tu orden estará lista en aproximadamente 10 minutos. Te avisaremos cuando esté lista.</p>
                        <span class="text-xs text-blue-100 mt-1 block">10:36 AM</span>
                    </div>
                </div>

                {{-- Customer Message --}}
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-sm font-bold flex-shrink-0">
                        JP
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm max-w-[70%]">
                        <p class="text-sm text-gray-800">Perfecto, gracias!</p>
                        <span class="text-xs text-gray-500 mt-1 block">10:37 AM</span>
                    </div>
                </div>
            </div>

            {{-- Message Input --}}
            <div class="border-t border-gray-200 p-4 bg-white">
                <form id="chat-form" onsubmit="sendMessage(event)" class="flex space-x-2">
                    <input type="text"
                           id="chat-input"
                           placeholder="Escribe un mensaje..."
                           class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           autocomplete="off">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 transition flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">Presiona Enter para enviar</p>
            </div>
        </div>
    </div>
</div>

{{-- Toggle Button (cuando está cerrado) --}}
<button id="chat-toggle-btn"
        class="fixed top-24 bg-blue-600 hover:bg-blue-700 text-white rounded-r-lg px-3 py-4 shadow-lg transition-all duration-200 z-40"
        style="left: 256px;"
        onclick="toggleChatSidebar()">
    <div class="flex flex-col items-center space-y-1">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <span class="text-xs font-semibold">Chat</span>
    </div>
    <span id="unread-badge-btn" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
        3
    </span>
</button>

<style>
    #chat-messages::-webkit-scrollbar {
        width: 6px;
    }
    #chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    #chat-messages::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    #orders-view::-webkit-scrollbar {
        width: 6px;
    }
    #orders-view::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    #orders-view::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
</style>

<script>
    function toggleChatSidebar() {
        const sidebar = document.getElementById('chat-sidebar');
        const toggleBtn = document.getElementById('chat-toggle-btn');

        sidebar.classList.toggle('-translate-x-full');

        if (sidebar.classList.contains('-translate-x-full')) {
            toggleBtn.style.left = '256px';
        } else {
            toggleBtn.style.left = '536px'; // 256px (sidebar principal) + 280px (chat sidebar)
        }
    }

    function switchTab(tab) {
        const ordersView = document.getElementById('orders-view');
        const chatView = document.getElementById('chat-view');
        const ordersTab = document.getElementById('orders-tab');
        const chatTab = document.getElementById('chat-tab');

        if (tab === 'orders') {
            ordersView.classList.remove('hidden');
            chatView.classList.add('hidden');
            ordersTab.classList.add('text-blue-600', 'border-blue-600', 'bg-blue-50');
            ordersTab.classList.remove('text-gray-600', 'border-transparent');
            chatTab.classList.add('text-gray-600', 'border-transparent');
            chatTab.classList.remove('text-blue-600', 'border-blue-600', 'bg-blue-50');
        } else {
            ordersView.classList.add('hidden');
            chatView.classList.remove('hidden');
            chatTab.classList.add('text-blue-600', 'border-blue-600', 'bg-blue-50');
            chatTab.classList.remove('text-gray-600', 'border-transparent');
            ordersTab.classList.add('text-gray-600', 'border-transparent');
            ordersTab.classList.remove('text-blue-600', 'border-blue-600', 'bg-blue-50');
        }
    }

    function openChat(orderNumber, customerName) {
        document.getElementById('chat-order-number').textContent = orderNumber;
        document.getElementById('chat-customer-name').textContent = customerName;
        switchTab('chat');

        // TODO: Cargar mensajes reales de la orden desde API
        // fetchOrderMessages(orderNumber);
    }

    function sendMessage(event) {
        event.preventDefault();

        const input = document.getElementById('chat-input');
        const message = input.value.trim();

        if (!message) return;

        // Add business message to chat
        addMessageToChat(message, 'business');

        // Clear input
        input.value = '';

        // TODO: Enviar mensaje a API
        // sendMessageToAPI(orderNumber, message);
    }

    function addMessageToChat(message, sender) {
        const messagesContainer = document.getElementById('chat-messages');
        const time = new Date().toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });

        const messageDiv = document.createElement('div');

        if (sender === 'business') {
            messageDiv.className = 'flex justify-end';
            messageDiv.innerHTML = `
                <div class="bg-blue-600 text-white rounded-lg p-3 shadow-sm max-w-[70%]">
                    <p class="text-sm">${message}</p>
                    <span class="text-xs text-blue-100 mt-1 block">${time}</span>
                </div>
            `;
        } else {
            messageDiv.className = 'flex items-start space-x-2';
            messageDiv.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-sm font-bold flex-shrink-0">
                    ${sender.substring(0, 2).toUpperCase()}
                </div>
                <div class="bg-white rounded-lg p-3 shadow-sm max-w-[70%]">
                    <p class="text-sm text-gray-800">${message}</p>
                    <span class="text-xs text-gray-500 mt-1 block">${time}</span>
                </div>
            `;
        }

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Allow Enter key to send message
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

        // Show unread badge by default for demo
        const badge = document.getElementById('unread-badge-btn');
        if (badge) {
            badge.classList.remove('hidden');
        }
    });

    // TODO: Integration points
    // - fetchActiveOrders() - Cargar órdenes activas desde API
    // - fetchOrderMessages(orderNumber) - Cargar mensajes de una orden
    // - sendMessageToAPI(orderNumber, message) - Enviar mensaje a cliente
    // - WebSocket listener for new messages
    // - Update order status when changed
</script>
@endif
