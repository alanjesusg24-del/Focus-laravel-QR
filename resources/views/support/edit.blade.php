@extends('layouts.business-app')

@section('title', 'Editar Ticket #' . $supportTicket->support_ticket_id . ' - Order QR System')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}" class="text-primary">
                            <x-icon name="home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.support.index') }}" class="text-primary">Tickets de Soporte</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.support.show', $supportTicket->support_ticket_id) }}" class="text-primary">
                            Ticket #{{ $supportTicket->support_ticket_id }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Editar Ticket #{{ $supportTicket->support_ticket_id }}</h2>
            <p class="mb-0 text-muted">Modifica la descripción de tu ticket de soporte</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.support.show', $supportTicket->support_ticket_id) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <x-icon name="back" class="me-2"/>
                Volver al ticket
            </a>
        </div>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <strong>Por favor corrija los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="row">
        <div class="col-12 col-xl-8 mx-auto">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <h5 class="mb-0">Editar Ticket</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('business.support.update', $supportTicket->support_ticket_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Subject (Read-only) -->
                        <div class="mb-4">
                            <label for="subject" class="form-label">Asunto</label>
                            <input type="text"
                                   class="form-control"
                                   id="subject"
                                   value="{{ $supportTicket->subject }}"
                                   disabled
                                   readonly>
                            <small class="form-text text-muted">
                                <x-icon name="info" class="icon-xs me-1" />
                                El asunto no puede ser modificado
                            </small>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Descripcion del Problema <span class="text-danger">*</span></label>
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                      id="description"
                                      name="description"
                                      rows="8"
                                      maxlength="2000"
                                      placeholder="Describe detalladamente tu problema o consulta"
                                      required>{{ old('description', $supportTicket->description) }}</textarea>
                            @if($errors->has('description'))
                                <div class="invalid-feedback d-block">{{ $errors->first('description') }}</div>
                            @endif
                            <small class="form-text text-muted">
                                <span id="charCount">{{ strlen(old('description', $supportTicket->description)) }}</span>/2000 caracteres
                            </small>
                        </div>

                        <!-- Info Alert -->
                    <div class="alert alert-info d-flex align-items-start" role="alert">
                        <x-icon name="info" class="me-2 mt-1 flex-shrink-0" />
                        <div>
                            <strong>Nota:</strong> Solo puedes editar la descripción del ticket. El asunto y los archivos adjuntos no pueden ser modificados una vez creado el ticket.
                        </div>
                    </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-start align-items-center mt-4 gap-3">
                            <a href="{{ route('business.support.show', $supportTicket->support_ticket_id) }}" class="btn btn-secondary">
                                <x-icon name="action.cancel" class="me-2"/>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <x-icon name="action.save" class="me-2"/>
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for description
    const descriptionTextarea = document.getElementById('description');
    const charCountSpan = document.getElementById('charCount');

    descriptionTextarea.addEventListener('input', function() {
        charCountSpan.textContent = this.value.length;
    });
});
</script>
@endpush
@endsection
