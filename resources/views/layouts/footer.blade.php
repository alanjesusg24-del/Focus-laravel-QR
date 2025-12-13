{{--
  Company: CETAM
  Project: FQR
  File: register.blade.php
  Created on: 07/09/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno

  Changelog:
  - ID: 1 | Date: 12/12/2025 | 
    Modified by: Dafne Vanessa Castillo Moreno | 
    Description: Add modals for terms of use and privacy notice in the footer.
--}}

<footer class="bg-white rounded shadow p-5 mb-4 mt-4">
    <div class="row">
        <div class="col-12 col-md-4 col-xl-6 mb-4 mb-md-0">
            <p class="mb-0 text-center text-lg-start">
                © <span class="current-year"></span> 
                <a class="text-primary fw-normal" href="#" target="_blank">Focus</a> 
                QR System
            </p>
        </div>
        <div class="col-12 col-md-8 col-xl-6 text-center text-lg-start">
            <ul class="list-inline list-group-flush list-group-borderless text-md-end mb-0">
                
                <li class="list-inline-item px-0 px-sm-2">
                    <a href="#" class="text-decoration-none"
                       data-bs-toggle="modal" data-bs-target="#termsModal">
                        Términos de uso
                    </a>
                </li>
                <li class="list-inline-item px-0 px-sm-2">
                    <a href="#" class="text-decoration-none"
                       data-bs-toggle="modal" data-bs-target="#privacyModal">
                        Aviso de privacidad
                    </a>
                </li>
            </ul>
        </div>
    </div>
</footer>

{{-- Include modal terms and privacy --}}
@include('components.legal-modals')

<script>
    
    if (document.querySelector(".current-year")) {
        document.querySelector(".current-year").textContent = new Date().getFullYear();
    }
</script>