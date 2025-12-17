{{--
  Company: CETAM
  Project: FQR
  File: register.blade.php
  Created on: 24/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}

<nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark ps-0 pe-2 pb-0" style="padding-top: 0 !important;">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-end w-100" id="navbarSupportedContent">
      {{-- Navbar links --}}
      <ul class="navbar-nav align-items-center">
        
        

        {{-- User menu --}}
        <li class="nav-item dropdown ms-lg-3">
          <a class="nav-link dropdown-toggle pt-1 px-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="media d-flex align-items-center">
              <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="background-color: #EF4444;">
                <span class="h6 mb-0">{{ substr(auth()->guard('superadmin')->user()->full_name ?? 'SA', 0, 2) }}</span>
              </div>
              <div class="media-body ms-2 text-dark align-items-center d-none d-lg-block">
                <span class="mb-0 font-small fw-bold text-gray-900">{{ auth()->guard('superadmin')->user()->full_name ?? 'Super Admin' }}</span>
              </div>
            </div>
          </a>
          <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
            <a class="dropdown-item d-flex align-items-center" href="{{ route('superadmin.profile.index') }}">
              <x-icon name="user.profile" class="dropdown-icon text-gray-400 me-2" />
              Mi Perfil
            </a>
            <div role="separator" class="dropdown-divider my-1"></div>
            <form action="{{ route('superadmin.logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item d-flex align-items-center">
                <x-icon name="auth.logout" class="dropdown-icon text-danger me-2" />
                Cerrar Sesión
              </button>
            </form>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
