{{--
============================================
CETAM - Alert Component View
============================================

@props type - Tipo de alerta (success, danger, warning, info)
@props message - Mensaje a mostrar
@props dismissible - Permite cerrar la alerta

============================================
--}}

<div class="alert {{ $alertClass }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
    <x-icon :name="$iconName" class="me-2" />
    {{ $message }}

    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
