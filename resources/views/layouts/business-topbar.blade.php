<nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark ps-0 pe-2 pb-0">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center w-100" id="navbarSupportedContent">
      {{-- Espacio vacío para balance --}}
      <div></div>

      <ul class="navbar-nav align-items-center">
        
        <li class="nav-item dropdown notifications-dropdown me-2">
          <a class="nav-link text-dark notification-bell dropdown-toggle" href="#"role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
            
            <div class="d-inline-block position-relative">
                
                <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                </svg>
                
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                  <span class="visually-hidden">unread messages</span>
                </span>

            </div>

          </a>
          
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-center mt-2 py-0">
            <div class="list-group list-group-flush">
              <a href="#" class="text-center fw-bold border-bottom border-light py-3" style="color: #FB503B;">Notificaciones</a>

              <a href="#" class="list-group-item list-group-item-action border-bottom">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <div class="icon-shape icon-sm rounded me-3" style="background-color: #10B981;">
                      <x-icon name="success" class="text-white" />
                    </div>
                  </div>
                  <div class="col ps-0 ms-2">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <h4 class="h6 mb-0 text-small">Nueva orden recibida</h4>
                      </div>
                      <div class="text-end">
                        <small style="color: #FB503B;">Hace 2 min</small>
                      </div>
                    </div>
                  </div>
                </div>
              </a>

              <a href="#" class="dropdown-item text-center fw-bold rounded-bottom py-3">
                <x-icon name="view" class="text-gray-400 me-1" />
                Ver todas
              </a>
            </div>
          </div>
        </li>

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
              <x-icon name="user" class="dropdown-icon text-gray-400 me-2" />
              Mi Perfil
            </a>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('business.profile.change-password') }}">
              <x-icon name="lock" class="dropdown-icon text-gray-400 me-2" />
              Cambiar Contraseña
            </a>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('business.support.index') }}">
              <x-icon name="info" class="dropdown-icon text-gray-400 me-2" />
              Soporte
            </a>
            <div role="separator" class="dropdown-divider my-1"></div>
            <form action="{{ route('business.logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item d-flex align-items-center">
                <x-icon name="arrowRight" class="dropdown-icon text-danger me-2" />
                Cerrar Sesión
              </button>
            </form>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>