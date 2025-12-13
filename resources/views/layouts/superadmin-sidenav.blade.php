{{--
  Company: CETAM
  Project: FQR
  File: register.blade.php
  Created on: 25/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}

<nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
  <div class="sidebar-inner px-2 pt-3">

    {{-- Header --}}
    <div class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-3">
      <div class="d-flex align-items-center">
        <div class="avatar-lg me-3">
          <div class="avatar-lg rounded-circle d-flex align-items-center justify-content-center text-white bg-secondary border-white">
            <span class="h3 mb-0">{{ substr(auth()->guard('superadmin')->user()->full_name ?? 'SA', 0, 2) }}</span>
          </div>
        </div>
        <div class="d-block">
          <h2 class="h6 mb-2">{{ auth()->guard('superadmin')->user()->full_name ?? 'Super Admin' }}</h2>
          <form action="{{ route('superadmin.logout') }}" method="POST" class="d-inline">
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

    {{-- Main Menu --}}
    <ul class="nav flex-column nav-compact pt-3 pt-md-0">

      {{-- 1. Logo / System Brand --}}
      <li class="nav-item mb-2">
        <a href="{{ route('superadmin.dashboard') }}" class="d-flex align-items-center px-2 py-2 text-decoration-none text-white">
          <img src="{{ asset('assets/img/focus-icon.svg') }}"
               alt="Focus QR"
               class="me-2"
               width="28"
               height="28">
          <span class="sidebar-text fw-bold">Focus QR System</span>
        </a>
      </li>

      {{-- 2. Dashboard --}}
      <li class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('superadmin.dashboard') }}" class="nav-link d-flex align-items-center py-1 px-2">
          <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
            <x-icon name="nav.home" class="me-0" />
          </span>
          <span class="sidebar-text">Inicio</span>
        </a>
      </li>

      {{-- 3. Businesses --}}
      <li class="nav-item {{ request()->routeIs('superadmin.businesses.*') ? 'active' : '' }}">
        <a href="{{ route('superadmin.businesses.index') }}" class="nav-link d-flex align-items-center py-1 px-2">
          <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
            <x-icon name="business.list" class="me-0" />
          </span>
          <span class="sidebar-text">Negocios</span>
        </a>
      </li>

      {{-- 4. Plans --}}
      <li class="nav-item {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
        <a href="{{ route('superadmin.plans.index') }}" class="nav-link d-flex align-items-center py-1 px-2">
          <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
            <x-icon name="plan.subscription" class="me-0" />
          </span>
          <span class="sidebar-text">Planes</span>
        </a>
      </li>

      {{-- 5. Payments and Subscriptions --}}
      <li class="nav-item {{ request()->routeIs('superadmin.payments.*') ? 'active' : '' }}">
        <a href="{{ route('superadmin.payments.index') }}" class="nav-link d-flex align-items-center py-1 px-2">
          <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
            <x-icon name="money.invoice" class="me-0" />
          </span>
          <span class="sidebar-text">Pagos y Suscripciones</span>
        </a>
      </li>

      {{-- 6. Profile --}}
      <li class="nav-item {{ request()->routeIs('superadmin.profile.*') ? 'active' : '' }}">
        <a href="{{ route('superadmin.profile.index') }}" class="nav-link d-flex align-items-center py-1 px-2">
          <span class="sidebar-icon d-flex align-items-center justify-content-center me-2">
            <x-icon name="user.profile" class="me-0" />
          </span>
          <span class="sidebar-text">Perfil</span>
        </a>
      </li>

    </ul>
  </div>
</nav>
