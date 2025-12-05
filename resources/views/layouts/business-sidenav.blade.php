{{--
 * Company: CETAM
 * Project: Focus QR System
 * File: business-sidenav.blade.php
 * Created on: 05/11/2025
 * Created by: Dafne Vanessa Castillo Moreno
 * Approved by: Alan Jesus 
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
      
      <li class="nav-item mb-4">
        <a href="{{ route('business.dashboard.index') }}" class="nav-link d-flex align-items-center">
          <span class="sidebar-icon me-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold" 
                 style="width: 30px; height: 30px; background-color: #fb503b;">
                {{ substr(auth()->guard('business')->user()->business_name ?? 'B', 0, 1) }}
            </div>
          </span>
          <span class="mt-1 ms-1 sidebar-text fw-bold">
            {{ auth()->guard('business')->user()->business_name ?? 'Mi Negocio' }}
          </span>
        </a>
      </li>

      <li class="nav-item {{ request()->routeIs('business.dashboard.*') ? 'active' : '' }}">
        <a href="{{ route('business.dashboard.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <x-icon name="dashboard" class="me-2" />
          </span>
          <span class="sidebar-text">Dashboard</span>
        </a>
      </li>

      <li class="nav-item {{ request()->routeIs('business.orders.*') ? 'active' : '' }}">
        <a href="{{ route('business.orders.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <x-icon name="listCheck" class="me-2" />
          </span>
          <span class="sidebar-text">Órdenes</span>
        </a>
      </li>

      <li class="nav-item {{ request()->routeIs('business.payments.*') ? 'active' : '' }}">
        <a href="{{ route('business.payments.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <x-icon name="creditCard" class="me-2" />
          </span>
          <span class="sidebar-text">Suscripción</span>
        </a>
      </li>

      <li class="nav-item" role="separator" style="list-style: none;">
         <div style="height: 1px !important; background-color: rgba(255, 255, 255, 0.3) !important; margin: 1.5rem 1rem;"></div>
      </li>


      <li class="nav-item {{ request()->routeIs('business.profile.*') ? 'active' : '' }}">
        <a href="{{ route('business.profile.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <x-icon name="user" class="me-2" />
          </span>
          <span class="sidebar-text">Perfil</span>
        </a>
      </li>

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