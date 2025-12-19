{{--
  Company: CETAM
  Project: FQR
  File: legal-modals.blade.php
  Created on: 13/12/2025
  Created by: Dafne Vanessa Castillo Moreno
  Approved by: Dafne Vanessa Castillo Moreno
--}}

<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 justify-content-center">
                <h4 class="modal-title fw-bold" id="termsModalLabel">Términos y Condiciones</h4>
            </div>

            <div class="modal-body pt-2">
                <p class="text-center text-muted mb-4">Por favor, lee cuidadosamente nuestros términos y condiciones antes de continuar.</p>
                
                
                <div class="border rounded p-3 bg-white" style="height: 350px; overflow-y: auto;">
                    @include('components.legal-content.terms')
                </div>
            </div>

            <div class="modal-footer border-0 pt-2 pb-4 px-4">
                
                <button type="button" class="btn btn-primary w-100 py-2" data-bs-dismiss="modal">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 justify-content-center">
                <h4 class="modal-title fw-bold" id="privacyModalLabel">Aviso de Privacidad</h4>
            </div>

            <div class="modal-body pt-2">
                <p class="text-center text-muted mb-4">Por favor, lee cuidadosamente nuestro aviso de privacidad antes de continuar.</p>
                
                
                <div class="border rounded p-3 bg-white" style="height: 350px; overflow-y: auto;">
                    @include('components.legal-content.privacy')
                </div>
            </div>

            <div class="modal-footer border-0 pt-2 pb-4 px-4">
                
                <button type="button" class="btn btn-primary w-100 py-2" data-bs-dismiss="modal">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>