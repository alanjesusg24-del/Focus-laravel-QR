{{--
  Company: CETAM
  Project: FQR
  File: footer.blade.php
  Created on: 19/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}
<footer class="py-3 w-100">
    <div class="container">
        <div class="row justify-content-center mb-2">
            <div class="col-12 text-center">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item mx-2">
                        <a href="#" class="text-info text-decoration-none"
                           data-bs-toggle="modal" data-bs-target="#termsModal">
                            Términos de uso
                        </a>
                    </li>
                    <li class="list-inline-item mx-2">
                        <a href="#" class="text-info text-decoration-none"
                           data-bs-toggle="modal" data-bs-target="#privacyModal">
                            Aviso de Privacidad
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <p class="mb-0  text-primary">
                    © <span class="current-year">{{ date('Y') }}</span>
                    Focus - Sistema de Gestión de avisos
                </p>
            </div>
        </div>
    </div>
</footer>

{{-- Include modal terms and privacy --}}
@include('components.legal-modals')