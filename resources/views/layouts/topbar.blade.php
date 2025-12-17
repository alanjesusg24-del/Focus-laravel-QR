<nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark ps-0 pe-2 pb-0">
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-end w-100" id="navbarSupportedContent">
            <ul class="navbar-nav align-items-center">
            
                {{-- Notification Bell --}}
                <li class="nav-item dropdown">
                    <a class="nav-link text-dark notification-bell dropdown-toggle" href="#" role="button" 
                       data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        <svg class="icon icon-sm text-gray-900" fill="currentColor" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-center mt-2 py-0 shadow-lg border-0">
                        <div class="list-group list-group-flush">
                            <div class="text-center text-primary fw-bold border-bottom border-light py-3">Notificaciones</div>
                            <div class="text-center py-4 text-muted">
                                <small>No hay notificaciones nuevas</small>
                            </div>
                        </div>
                    </div>
                </li>

                {{-- User Profile --}}
                <li class="nav-item dropdown ms-lg-3">
                    <a class="nav-link dropdown-toggle pt-1 px-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="media d-flex align-items-center">
                            @php
                                $business = auth('business')->check() ? auth('business')->user() : auth()->user();
                                $hasLogo = $business && isset($business->logo_url) && $business->logo_url;
                                $businessName = $business->business_name ?? $business->name ?? ($business->first_name . ' ' . $business->last_name) ?? 'Usuario';
                            @endphp

                            @if($hasLogo)
                                @php
                                    $logoUrl = str_starts_with($business->logo_url, 'storage/') 
                                        ? asset($business->logo_url) 
                                        : asset('storage/' . $business->logo_url);
                                @endphp
                                <img id="topbar-profile-photo"
                                     class="avatar rounded-circle border border-white shadow-sm"
                                     alt="{{ $businessName }}"
                                     src="{{ $logoUrl }}"
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                @php
                                    $words = explode(' ', trim($businessName));
                                    $initials = '';
                                    if (count($words) >= 2) {
                                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                    } else {
                                        $initials = strtoupper(substr($businessName, 0, 2));
                                    }
                                @endphp
                                <div id="topbar-profile-photo"
                                     class="avatar rounded-circle border border-white shadow-sm bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 40px; height: 40px; font-size: 1rem;">
                                    {{ $initials }}
                                </div>
                            @endif

                            <div class="media-body ms-2 text-dark align-items-center d-none d-lg-block">
                                <span class="mb-0 font-small fw-bold text-gray-900">
                                    {{ $businessName }}
                                </span>
                            </div>
                        </div>
                    </a>
                    
                    <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1 shadow-lg border-0">
                        <a class="dropdown-item d-flex align-items-center" href="{{ route(config('proj.route_name_prefix', 'proj') . '.profile.index') }}">
                            <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                      clip-rule="evenodd"></path>
                            </svg>
                            Mi Perfil
                        </a>
                        <div role="separator" class="dropdown-divider my-1"></div>
                        
                        {{-- Componente Logout --}}
                        <livewire:logout />
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

@push('scripts')
<script>
    function updateTopbarPhoto(logoUrl) {
        const photoElement = document.getElementById('topbar-profile-photo');
        if (photoElement && logoUrl) {
            let newSrc = logoUrl;
            if (!newSrc.startsWith('http')) {
                newSrc = '{{ asset('storage') }}/' + newSrc.replace('/storage/', '').replace('storage/', '');
            }

            console.log('Actualizando foto del topbar:', newSrc);

            // If it's a div with initials, replace it with an image
            if (photoElement.tagName === 'DIV') {
                const img = document.createElement('img');
                img.id = 'topbar-profile-photo';
                img.className = 'avatar rounded-circle border border-white shadow-sm';
                img.alt = 'Logo';
                img.style.width = '40px';
                img.style.height = '40px';
                img.style.objectFit = 'cover';
                img.src = newSrc;
                photoElement.replaceWith(img);
            } else {
                // If it's already an image, just update the src
                photoElement.src = newSrc;
            }
        }
    }

    // Escuchar evento Livewire
    document.addEventListener('livewire:init', () => {
        if (typeof Livewire !== 'undefined') {
            Livewire.on('profile-photo-updated', (event) => {
                updateTopbarPhoto(event.logoUrl);
            });
        }
    });

    // Escuchar evento DOM como fallback
    window.addEventListener('profile-photo-updated', (event) => {
        updateTopbarPhoto(event.detail.logoUrl);
    });
</script>
@endpush