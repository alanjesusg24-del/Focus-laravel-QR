{{--
============================================
CETAM - Legal Modals Component
============================================

@project     Focus QR System (FQR)
@file        legal-modals.blade.php
@description Modales de términos y condiciones y aviso de privacidad
@author      CETAM Dev Team
@created     2025-12-13
@version     1.0.0
@copyright   CETAM © 2025

============================================
--}}

<!-- Modal Términos de Uso -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Términos y Condiciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                @include('components.legal-content.terms')
            </div>

            <div class="modal-footer justify-content-start">
                <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">
                    <x-icon name="nav.back" class="me-2"/> Atrás
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aviso de Privacidad -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Aviso de Privacidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                @include('components.legal-content.privacy')
            </div>

            <div class="modal-footer justify-content-start">
                <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">
                    <x-icon name="nav.back" class="me-2"/> Atrás
                </button>
            </div>
        </div>
    </div>
</div>