{{--
============================================
CETAM - Business Sidebar Navigation
============================================

@project     Centro de Servicios (CS)
@file        business-sidenav.blade.php
@description Barra lateral de navegación para panel de negocios
@created     2025-11-22

============================================
--}}

<nav id="sidebarMenu" class="sidebar d-lg-block text-white collapse" data-simplebar style="background-color: #1F2937;">
  <div class="sidebar-inner px-2 pt-3">
    <div class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4">
      <div class="d-flex align-items-center">
        <div class="avatar-lg me-4">
          <div class="avatar-lg rounded-circle d-flex align-items-center justify-content-center text-white" style="background-color: #FB503B;">
            <span class="h3 mb-0">{{ substr(auth()->guard('business')->user()->business_name ?? 'B', 0, 1) }}</span>
          </div>
        </div>
        <div class="d-block">
          <h2 class="h6 mb-3">{{ auth()->guard('business')->user()->business_name ?? 'Business' }}</h2>
          <form action="{{ route('business.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm d-inline-flex align-items-center">
              <x-icon name="arrowRight" class="me-1" />
              Cerrar Sesión
            </button>
          </form>
        </div>
      </div>
      <div class="collapse-close d-md-none">
        <a href="#sidebarMenu" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"
          aria-expanded="true" aria-label="Toggle navigation">
          <x-icon name="close" />
        </a>
      </div>
    </div>

    <ul class="nav flex-column pt-3 pt-md-0">
      {{-- Orders --}}
      <li class="nav-item {{ request()->routeIs('business.orders.*') ? 'active' : '' }}">
        <a href="{{ route('business.orders.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <x-icon name="listCheck" class="me-2" />
          </span>
          <span class="sidebar-text">Órdenes</span>
        </a>
      </li>

      {{-- Dashboard --}}
      <li class="nav-item {{ request()->routeIs('business.dashboard.*') ? 'active' : '' }}">
        <a href="{{ route('business.dashboard.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <x-icon name="dashboard" class="me-2" />
          </span>
          <span class="sidebar-text">Dashboard</span>
        </a>
      </li>

      {{-- Payments --}}
      <li class="nav-item {{ request()->routeIs('business.payments.*') ? 'active' : '' }}">
        <a href="{{ route('business.payments.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <x-icon name="creditCard" class="me-2" />
          </span>
          <span class="sidebar-text">Pagos</span>
        </a>
      </li>

      {{-- DESACTIVADO: Support --}}
      {{-- <li class="nav-item {{ request()->routeIs('business.support.*') ? 'active' : '' }}">
        <a href="{{ route('business.support.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd"></path>
            </svg>
          </span>
          <span class="sidebar-text">Soporte</span>
        </a>
      </li> --}}

      <li role="separator" class="dropdown-divider mt-4 mb-3 border-gray-700"></li>

      {{-- Profile --}}
      <li class="nav-item {{ request()->routeIs('business.profile.*') ? 'active' : '' }}">
        <a href="{{ route('business.profile.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <x-icon name="user" class="me-2" />
          </span>
          <span class="sidebar-text">Perfil</span>
        </a>
      </li>

      {{-- Chat (solo si el plan lo incluye) --}}
      @if(auth()->guard('business')->user()->plan && auth()->guard('business')->user()->plan->has_chat_module)
      <li class="nav-item {{ request()->routeIs('business.chat.*') ? 'active' : '' }}">
        <a href="{{ route('business.chat.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <x-icon name="comments" class="me-2" />
          </span>
          <span class="sidebar-text">Chat</span>
        </a>
      </li>
      @endif
    </ul>
  </div>
</nav>
