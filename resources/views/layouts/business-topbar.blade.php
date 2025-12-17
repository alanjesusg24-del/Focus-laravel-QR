{{--
  Company: CETAM
  Project: FQR
  File: register.blade.php
  Created on: 26/10/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno

  Changelog:
  - ID: 1 | Date: 17/11/2025 | 
    Modified by: Alan Jesus Garcia Nava | 
    Description: Changed notification bell color to primary and adjusted dropdown menu styles.
--}}



<nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark ps-0 pe-2 pb-0">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center w-100" id="navbarSupportedContent">
      <div></div>

      <ul class="navbar-nav align-items-center">
      

        <li class="nav-item dropdown ms-lg-3">
          <a class="nav-link dropdown-toggle pt-1 px-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="media d-flex align-items-center">
              
              <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="background-color: #FB503B;">
                <span class="h6 mb-0">{{ substr(auth()->guard('business')->user()->business_name ?? 'B', 0, 1) }}</span>
              </div>
              
              <div class="media-body ms-2 text-dark align-items-center d-none d-lg-block">
                <span class="mb-0 font-small fw-bold text-gray-900">{{ auth()->guard('business')->user()->business_name ?? 'Business' }}</span>
              </div>
            </div>
          </a>
          <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
            <a class="dropdown-item d-flex align-items-center" href="{{ route('business.profile.index') }}">
              <x-icon name="user.profile" class="dropdown-icon text-gray-400 me-2" />
              Mi Perfil
            </a>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('business.profile.change-password') }}">
              <x-icon name="access.lock" class="dropdown-icon text-gray-400 me-2" />
              Cambiar Contraseña
            </a>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('business.support.index') }}">
              <x-icon name="state.info" class="dropdown-icon text-gray-400 me-2" />
              Soporte
            </a>
            <div role="separator" class="dropdown-divider my-1"></div>
            <form action="{{ route('business.logout') }}" method="POST">
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