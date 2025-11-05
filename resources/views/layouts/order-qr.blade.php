<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Order QR System') - {{ config('app.name') }}</title>

    <!-- Volt CSS (includes Bootstrap + custom styles) -->
    <link href="{{ mix('css/volt.css') }}" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS CDN (temporal para Order QR) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'institutional-blue': '#1d4976',
                        'institutional-orange': '#de5629',
                        'institutional-gray': '#7b96ab',
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <!-- Sidebar -->
    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 bg-institutional-blue text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 bg-institutional-blue border-b border-white/10">
                <div class="flex items-center space-x-3">
                    @auth
                        @if(auth()->user()->logo_url)
                            <img src="{{ auth()->user()->logo_url }}" alt="Logo" class="h-10 w-10 rounded-full object-cover">
                        @else
                            <div class="h-10 w-10 bg-institutional-orange rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-lg">{{ substr(auth()->user()->business_name ?? 'Q', 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="text-lg font-semibold truncate">{{ auth()->user()->business_name ?? 'Order QR' }}</span>
                    @else
                        <span class="text-lg font-semibold">Order QR</span>
                    @endauth
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition {{ request()->routeIs('dashboard.*') ? 'bg-white/20' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition {{ request()->routeIs('orders.*') ? 'bg-white/20' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Órdenes
                </a>

                <a href="{{ route('order-qr.payment.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition {{ request()->routeIs('order-qr.payment.*') ? 'bg-white/20' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Pagos
                </a>

                @auth
                    @if(auth()->user()->has_chat_module)
                        <a href="{{ route('chat.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition {{ request()->routeIs('chat.*') ? 'bg-white/20' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Chat
                            <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">3</span>
                        </a>
                    @endif
                @endauth

                <a href="{{ route('support.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition {{ request()->routeIs('support.*') ? 'bg-white/20' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Soporte
                </a>

                <a href="{{ route('business.profile') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition {{ request()->routeIs('business.*') ? 'bg-white/20' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Mi Negocio
                </a>
            </nav>

            <!-- User Profile -->
            @auth
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-institutional-orange rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">{{ substr(auth()->user()->email, 0, 2) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->business_name }}</p>
                            <p class="text-xs text-gray-300 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-white hover:text-gray-200" title="Cerrar sesión">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-64">
        <!-- Header -->
        <header class="sticky top-0 z-40 bg-white border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-6">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <h1 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>

                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-institutional-orange rounded-full">3</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6">
            <!-- Flash Messages -->
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @if(session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            @if($errors->any())
                <x-alert type="error" message="Por favor corrige los errores en el formulario." />
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="py-6 px-6 bg-white border-t border-gray-200">
            <div class="text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} Order QR System - Centro de Desarrollo Tecnológico Aplicado de México (CETAM)</p>
            </div>
        </footer>
    </div>

    <!-- Overlay para cerrar sidebar en móvil -->
    <div
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/50 lg:hidden"
        style="display: none;"
    ></div>

    <!-- Volt JS -->
    <script src="{{ mix('js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
