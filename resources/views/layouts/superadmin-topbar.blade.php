<nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark ps-0 pe-2 pb-0">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-end w-100" id="navbarSupportedContent">
      {{-- Navbar links --}}
      <ul class="navbar-nav align-items-center">
        {{-- Notifications --}}
        <li class="nav-item dropdown">
          <a class="nav-link text-dark notification-bell unread dropdown-toggle" data-unread-notifications="true" href="#" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
            <x-icon name="bell" class="text-gray-900" />
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-center mt-2 py-0">
            <div class="list-group list-group-flush">
              <a href="#" class="text-center text-primary fw-bold border-bottom border-light py-3">Notificaciones</a>

              <a href="#" class="list-group-item list-group-item-action border-bottom">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <div class="icon-shape icon-sm rounded me-3" style="background-color: #EF4444;">
                      <x-icon name="success" class="text-white" />
                    </div>
                  </div>
                  <div class="col ps-0 ms-2">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <h4 class="h6 mb-0 text-small">Nuevo negocio registrado</h4>
                      </div>
                      <div class="text-end">
                        <small style="color: #EF4444;">Hace 5 min</small>
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
              <x-icon name="user" class="text-gray-400 me-2" />
              Mi Perfil
            </a>
            <a class="dropdown-item d-flex align-items-center" href="#">
              <x-icon name="cog" class="text-gray-400 me-2" />
              Configuración
            </a>
            <div role="separator" class="dropdown-divider my-1"></div>
            <form action="{{ route('superadmin.logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item d-flex align-items-center">
                <x-icon name="arrowRight" class="me-2" style="color: #EF4444;" />
                Cerrar Sesión
              </button>
            </form>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
