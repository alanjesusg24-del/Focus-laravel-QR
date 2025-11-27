@extends('layouts.business-app')

@section('title', 'Crear Ticket de Soporte - Order QR System')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.dashboard.index') }}">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('business.support.index') }}">Tickets de Soporte</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Crear Ticket</li>
                </ol>
            </nav>
            <h2 class="h4">Crear Nuevo Ticket de Soporte</h2>
            <p class="mb-0">Complete el formulario para solicitar ayuda a nuestro equipo de soporte</p>
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

    <!-- Create Ticket Form -->
    <div class="row">
        <div class="col-12 col-xl-8 mx-auto">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <h5 class="mb-0">Informacion del Ticket</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('business.support.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Hidden Priority (always medium) -->
                        <input type="hidden" name="priority" value="medium">

                        <!-- Subject -->
                        <div class="mb-4">
                            <label for="subject" class="form-label">Asunto <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}"
                                   id="subject"
                                   name="subject"
                                   value="{{ old('subject') }}"
                                   placeholder="Breve descripcion del problema"
                                   maxlength="255"
                                   required>
                            @if($errors->has('subject'))
                                <div class="invalid-feedback d-block">{{ $errors->first('subject') }}</div>
                            @endif
                            <small class="form-text text-muted">Maximo 255 caracteres</small>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Descripcion del Problema <span class="text-danger">*</span></label>
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                      id="description"
                                      name="description"
                                      rows="8"
                                      maxlength="2000"
                                      placeholder="Describe detalladamente tu problema o consulta. Incluye toda la informacion relevante como pasos para reproducir el error, mensajes de error, etc."
                                      required>{{ old('description') }}</textarea>
                            @if($errors->has('description'))
                                <div class="invalid-feedback d-block">{{ $errors->first('description') }}</div>
                            @endif
                            <small class="form-text text-muted">
                                <span id="charCount">0</span>/2000 caracteres - Sea lo mas detallado posible
                            </small>
                        </div>

                        <!-- Attachment -->
                        <div class="mb-4">
                            <label for="attachment" class="form-label">Adjuntar Archivo (Opcional)</label>
                            <input type="file"
                                   class="form-control {{ $errors->has('attachment') ? 'is-invalid' : '' }}"
                                   id="attachment"
                                   name="attachment"
                                   accept=".jpg,.jpeg,.png,.pdf">
                            @if($errors->has('attachment'))
                                <div class="invalid-feedback d-block">{{ $errors->first('attachment') }}</div>
                            @endif
                            <small class="form-text text-muted">
                                <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Formatos permitidos: JPG, PNG, PDF. Tamano maximo: 5MB
                            </small>

                            <!-- Preview -->
                            <div id="filePreview" class="mt-3 d-none">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <svg class="icon icon-sm me-2 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div class="flex-grow-1">
                                                <span id="fileName" class="fw-bold"></span>
                                                <small id="fileSize" class="text-muted d-block"></small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="clearFileInput()">
                                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Help Text -->
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <svg class="icon icon-sm me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <strong>Consejos para obtener ayuda rapida:</strong>
                                <ul class="mb-0 mt-1 small">
                                    <li>Sea especifico sobre el problema que esta experimentando</li>
                                    <li>Incluya capturas de pantalla si es posible</li>
                                    <li>Mencione los pasos que ya intento para resolver el problema</li>
                                    <li>Nuestro equipo respondera en un plazo de 24-48 horas habiles</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('business.support.index') }}" class="btn btn-light">
                                <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <svg class="icon icon-xs me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Enviar Ticket
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

    // Update initial count if there's old input
    if (descriptionTextarea.value) {
        charCountSpan.textContent = descriptionTextarea.value.length;
    }

    // File input preview
    const fileInput = document.getElementById('attachment');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const size = (file.size / 1024 / 1024).toFixed(2); // Convert to MB

            fileName.textContent = file.name;
            fileSize.textContent = size + ' MB';
            filePreview.classList.remove('d-none');

            // Validate file size
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. El tamano maximo es 5MB.');
                clearFileInput();
            }
        }
    });
});

function clearFileInput() {
    const fileInput = document.getElementById('attachment');
    const filePreview = document.getElementById('filePreview');

    fileInput.value = '';
    filePreview.classList.add('d-none');
}
</script>
@endpush
@endsection
