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
            <div class="d-flex align-items-center">
              @if(auth()->guard('business')->user()->logo_url)
                <img id="topbar-user-avatar" src="{{ asset('storage/' . auth()->guard('business')->user()->logo_url) }}" 
                     alt="Logo" 
                     class="rounded-circle me-2" 
                     style="width: 40px; height: 40px; object-fit: cover;">
              @else
                <div id="topbar-user-avatar" class="rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="background-color: #FB503B; width: 40px; height: 40px;">
                  <span class="small fw-bold">{{ substr(auth()->guard('business')->user()->business_name ?? 'B', 0, 1) }}</span>
                </div>
              @endif
              
              <span class="mb-0 font-small fw-bold text-gray-900 d-none d-lg-inline">{{ auth()->guard('business')->user()->business_name ?? 'Business' }}</span>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar la foto del topbar
    function updateTopbarPhoto(imageSrc) {
        const avatar = document.getElementById('topbar-user-avatar');
        if (avatar) {
            if (imageSrc) {
                // Si es una imagen, reemplazar con img
                const newImg = document.createElement('img');
                newImg.id = 'topbar-user-avatar';
                newImg.src = imageSrc.startsWith('/storage') ? imageSrc : '/storage/' + imageSrc;
                newImg.alt = 'Logo';
                newImg.className = 'rounded-circle me-2';
                newImg.style.cssText = 'width: 40px; height: 40px; object-fit: cover;';
                avatar.parentNode.replaceChild(newImg, avatar);
            }
        }
    }

    // Escuchar eventos de Livewire
    if (window.Livewire) {
        Livewire.on('profile-photo-updated', function(data) {
            if (data && data.logo_url) {
                updateTopbarPhoto(data.logo_url);
            }
        });
    }

    // Escuchar eventos DOM como fallback
    document.addEventListener('profile-photo-updated', function(event) {
        if (event.detail && event.detail.logo_url) {
            updateTopbarPhoto(event.detail.logo_url);
        }
    });
});
</script>