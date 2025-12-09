<nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
    <div class="sidebar-inner px-2 pt-3">

        {{-- Header Móvil (Sin cambios) --}}
        <div class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-3">
            <div class="d-flex align-items-center">
                <div class="avatar-lg me-3">
                    <div class="avatar-lg rounded-circle d-flex align-items-center justify-content-center text-white bg-secondary border-white">
                        <span class="h3 mb-0">{{ substr(auth()->guard('business')->user()->business_name ?? 'B', 0, 1) }}</span>
                    </div>
                </div>
                <div class="d-block">
                    <h2 class="h6 mb-2">{{ auth()->guard('business')->user()->business_name ?? 'Business' }}</h2>
                    <form action="{{ route('business.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm d-inline-flex align-items-center">
                            <x-icon name="auth.logout" size="xs" class="me-1" /> 
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
            <div class="collapse-close d-md-none">
                <a href="#sidebarMenu" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"
                    aria-expanded="true" aria-label="Toggle navigation">
                    <x-icon name="nav.close" size="xs" />
                </a>
            </div>
        </div>

        {{-- Menú Principal --}}
        <ul class="nav flex-column nav-compact pt-3 pt-md-0">
            
            {{-- 1. Logo / Marca del Sistema --}}
            <li class="nav-item mb-2">
                <a href="{{ route('business.dashboard.index') }}" class="d-flex align-items-center px-2 py-2 text-decoration-none text-white">
                    <img src="{{ asset('assets/img/focus-icon.svg') }}"
                         alt="Focus QR"
                         class="me-2"
                         width="28"
                         height="28">
                    <span class="sidebar-text fw-bold">Focus QR System</span>
                </a>
            </li>

            {{-- 2. Dashboard --}}
            <li class="nav-item {{ request()->routeIs('business.dashboard.*') ? 'active' : '' }}">
                <a href="{{ route('business.dashboard.index') }}" class="nav-link d-flex align-items-center py-1 px-2">
                    <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
                        <x-icon name="nav.home" class="me-0" />
                    </span>
                    <span class="sidebar-text">Inicio</span>
                </a>
            </li>

            {{-- 3. Órdenes --}}
            <li class="nav-item {{ request()->routeIs('business.orders.*') ? 'active' : '' }}">
                <a href="{{ route('business.orders.index') }}" class="nav-link d-flex align-items-center py-1 px-2">
                    <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
                        <x-icon name="list.ordered" class="me-0" />
                    </span>
                    <span class="sidebar-text">Órdenes</span>
                </a>
            </li>

            {{-- 4. Suscripción --}}
            <li class="nav-item {{ request()->routeIs('business.payments.*') ? 'active' : '' }}">
                <a href="{{ route('business.payments.index') }}" class="nav-link d-flex align-items-center py-1 px-2">
                    <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
                        <x-icon name="money.invoice" class="me-0" />
                    </span>
                    <span class="sidebar-text">Suscripción</span>
                </a>
            </li>

            {{-- 5. Perfil --}}
            <li class="nav-item {{ request()->routeIs('business.profile.*') ? 'active' : '' }}">
                <a href="{{ route('business.profile.index') }}" class="nav-link d-flex align-items-center py-1 px-2">
                    <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
                        <x-icon name="user.profile" class="me-0" />
                    </span>
                    <span class="sidebar-text">Perfil</span>
                </a>
            </li>

            {{-- 6. Chat --}}
            @if(auth()->guard('business')->user()->plan && auth()->guard('business')->user()->plan->has_chat_module)
                <li class="nav-item {{ request()->routeIs('business.chat.*') ? 'active' : '' }}">
                    <a href="{{ route('business.chat.index') }}" class="nav-link d-flex align-items-center py-1 px-2">
                        <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
                            <x-icon name="msg.chat" class="me-0" />
                        </span>
                        <span class="sidebar-text">Chat</span>
                    </a>
                </li>
            @endif
            
        </ul>
    </div>
</nav>