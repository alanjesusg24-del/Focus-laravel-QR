{{--
  Company: CETAM
  Project: FQR
  File: register.blade.php
  Created on: 07/09/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno
--}}
<nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
  <div class="sidebar-inner px-3 pt-3">

    {{-- HEADER --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-white text-decoration-none">
            <div class="me-3 d-flex align-items-center justify-content-center rounded-circle fw-bold"
                 style="width: 36px; height: 36px; background-color:#fb503b;">
                T
            </div>
            <span class="fs-6 fw-bold">Tacos Pastor</span>
        </a>
    </div>

    {{-- Core group --}}
    <div class="sidebar-heading text-uppercase text-gray-400 small px-3 mb-2">
        Core
    </div>

    <ul class="nav flex-column mb-3">

        <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="nav-link d-flex align-items-center">
                <span class="sidebar-icon">
                   <svg class="icon icon-xs me-3" fill="currentColor" viewBox="0 0 20 20">
                     <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                     <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                   </svg>
                </span>
                Dashboard
            </a>
        </li>

        <li class="nav-item {{ Request::is('orders*') ? 'active' : '' }}">
            <a href="{{ route('orders.index') }}" class="nav-link d-flex align-items-center">
                <span class="sidebar-icon">
                  <svg class="icon icon-xs me-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 001-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4z"/>
                  </svg>
                </span>
                Órdenes
            </a>
        </li>

        <li class="nav-item {{ Request::is('payments*') ? 'active' : '' }}">
            <a href="{{ route('payments.index') }}" class="nav-link d-flex align-items-center">
                <span class="sidebar-icon">
                   <svg class="icon icon-xs me-3" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                      <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9z"/>
                   </svg>
                </span>
                Pagos
            </a>
        </li>

    </ul>

    {{-- DIVISOR --}}
    <hr class="my-3">

    {{-- Account group --}}
    <div class="sidebar-heading text-uppercase text-gray-400 small px-3 mb-2">
        Account
    </div>

    <ul class="nav flex-column">

        <li class="nav-item {{ Request::is('profile*') ? 'active' : '' }}">
            <a href="{{ route('profile') }}" class="nav-link d-flex align-items-center">
                <span class="sidebar-icon">
                   <svg class="icon icon-xs me-3" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6z"/>
                      <path fill-rule="evenodd" d="M3 18a7 7 0 1114 0z"/>
                   </svg>
                </span>
                Perfil
            </a>
        </li>

        <li class="nav-item {{ Request::is('chat*') ? 'active' : '' }}">
            <a href="{{ route('chat') }}" class="nav-link d-flex align-items-center">
                <span class="sidebar-icon">
                   <svg class="icon icon-xs me-3" fill="currentColor" viewBox="0 0 20 20">
                     <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7z"/>
                   </svg>
                </span>
                Chat
            </a>
        </li>

        <li class="nav-item mt-3">
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="nav-link d-flex align-items-center text-danger">
                <span class="sidebar-icon">
                    <svg class="icon icon-xs me-3" fill="currentColor" viewBox="0 0 20 20">
                       <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h6z"/>
                    </svg>
                </span>
                Cerrar sesión
            </a>
        </li>

    </ul>

  </div>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST">
  @csrf
</form>
