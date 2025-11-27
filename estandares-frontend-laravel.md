# Estándares de Frontend Laravel - CETAM
## Instrucciones para Claude Code

Este documento contiene los estándares de frontend que **DEBEN** cumplirse al 100% en todos los proyectos Laravel desarrollados. Los estándares de backend se asumen cumplidos; este documento se enfoca exclusivamente en frontend.

---

## 🎨 1. PLANTILLA BASE OBLIGATORIA

### Plantilla: Volt Laravel Admin Dashboard
- **Uso obligatorio** para todos los proyectos internos CETAM
- La plantilla define: colores, tipografías, espaciados y componentes visuales
- **Prohibido** alterar la estructura base (encabezado, barra lateral, pie de página)
- Personalizaciones permitidas: logotipo, títulos, secciones, iconos (solo elementos configurables)

---

## 🎨 2. PALETA DE COLORES INSTITUCIONAL

### Colores Obligatorios

```scss
// Primario - Gris oscuro tipo slate
$primary: #1F2937;
// Uso: Fondos principales, barras, encabezados, texto de alto contraste, títulos
// NO USAR: Para acciones/alertas, como texto sobre fondos oscuros

// Secundario - Naranja rojizo vibrante
$secondary: #FB503B;
// Uso: Elementos interactivos destacados, botones secundarios, íconos activos, indicadores
// NO USAR: Grandes áreas, bloques de texto, saturar fondos

// Terciario - Azul índigo oscuro
$tertiary: #31316A;
// Uso: Fondos secundarios, áreas destacadas complementarias, detalles gráficos
// NO USAR: Texto extenso, combinar con primario en jerarquía simultánea

// Ámbar (advertencias)
$warning: #FBA918;
// Uso: Advertencias, alertas no críticas, estados pendientes

// Colores de estado
$success: #10B981; // Verde (operaciones exitosas)
$danger: #EF4444;  // Rojo (errores críticos)
$info: #3B82F6;    // Azul (información general)
```

### Variables SCSS
```scss
// Archivo: resources/sass/_variables.scss
// Ejemplo de configuración de variables
$primary: #1F2937;
$secondary: #FB503B;
$tertiary: #31316A;
```

---

## 🔤 3. TIPOGRAFÍA

### Fuente Obligatoria
```css
/* Nunito Sans desde Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700&display=swap');

font-family: 'Nunito Sans', sans-serif;
```

### Jerarquías
- Usar etiquetas semánticas: `<h1>` a `<h6>`
- Aplicar clases auxiliares de Bootstrap para ajustes
- Mantener consistencia en tamaños y pesos

---

## 🧩 4. COMPONENTES DE LA PLANTILLA

### 4.1 Botones

#### Reglas de Uso por Tipo

| Tipo | Clase Bootstrap | Uso Permitido | NO Usar Para |
|------|----------------|---------------|--------------|
| **Primary** | `btn-primary` | Acción principal única por pantalla (guardar, enviar, confirmar) | Múltiples acciones principales, navegación |
| **Secondary** | `btn-secondary` | Acciones secundarias (cancelar, volver) | Acción principal de la pantalla |
| **Success** | `btn-success` | Confirmar operaciones exitosas, aprobar | Guardados normales (usar primary) |
| **Danger** | `btn-danger` | Eliminar, rechazar, operaciones irreversibles | Advertencias leves (usar warning) |
| **Warning** | `btn-warning` | Advertencias previas, acciones de precaución | Errores críticos (usar danger) |
| **Info** | `btn-info` | Información adicional, ayuda, detalles | Acciones que modifican datos |
| **Light** | `btn-light` | Opciones terciarias en fondos oscuros | Acción principal |
| **Dark** | `btn-dark` | Contraste especial, menús dropdown | Fondos claros estándar |

#### Tamaños de Botones
```html
<!-- Pequeño -->
<button class="btn btn-primary btn-sm">Texto</button>

<!-- Normal (por defecto) -->
<button class="btn btn-primary">Texto</button>

<!-- Grande -->
<button class="btn btn-primary btn-lg">Texto</button>
```

#### Botones con Iconos
```html
<!-- Icono a la izquierda -->
<button class="btn btn-primary">
    <x-icon name="save" /> Guardar
</button>

<!-- Icono a la derecha -->
<button class="btn btn-secondary">
    Cancelar <x-icon name="close" />
</button>

<!-- Solo icono -->
<button class="btn btn-primary btn-sm">
    <x-icon name="edit" />
</button>
```

#### Composición de Botones
- **Texto corto**: máximo 2-3 palabras
- **Verbo claro**: acción específica (Guardar, Eliminar, Enviar)
- **Un botón primary por pantalla**
- Agrupar botones relacionados con clases: `btn-group`

---

### 4.2 Tablas

```html
<!-- Tabla estándar con clases de plantilla -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-light">
            <tr>
                <th>Columna 1</th>
                <th>Columna 2</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dato 1</td>
                <td>Dato 2</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-info">
                        <x-icon name="view" />
                    </button>
                    <button class="btn btn-sm btn-primary">
                        <x-icon name="edit" />
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

#### Reglas de Tablas
- Siempre usar `table-responsive` para adaptabilidad
- Acciones al final (columna derecha)
- Alinear números a la derecha: `class="text-end"`
- Usar `table-hover` para mejorar usabilidad

---

### 4.3 Formularios

```html
<form method="POST" action="{{ route('ejemplo.store') }}">
    @csrf
    
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre completo</label>
        <input type="text" 
               class="form-control @error('nombre') is-invalid @enderror" 
               id="nombre" 
               name="nombre" 
               value="{{ old('nombre') }}"
               required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               id="email" 
               name="email" 
               value="{{ old('email') }}"
               required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('ejemplo.index') }}" class="btn btn-secondary">
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary">
            <x-icon name="save" /> Guardar
        </button>
    </div>
</form>
```

#### Reglas de Formularios
- **Label obligatorio** para cada input
- Usar `@error` para validaciones de Laravel
- Preservar valores con `old()`
- Clase `is-invalid` para campos con error
- Agrupar botones al final con `d-flex justify-content-end gap-2`

---

### 4.4 Modales

```html
<!-- Botón que abre modal -->
<button type="button" 
        class="btn btn-primary" 
        data-bs-toggle="modal" 
        data-bs-target="#ejemploModal">
    Abrir Modal
</button>

<!-- Estructura del Modal -->
<div class="modal fade" id="ejemploModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Título del Modal</h5>
                <button type="button" 
                        class="btn-close" 
                        data-bs-dismiss="modal" 
                        aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <!-- Contenido del modal -->
                <p>Contenido aquí</p>
            </div>
            <div class="modal-footer">
                <button type="button" 
                        class="btn btn-secondary" 
                        data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-primary">
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>
```

#### Tamaños de Modales
```html
<!-- Modal pequeño -->
<div class="modal-dialog modal-sm">

<!-- Modal normal (por defecto) -->
<div class="modal-dialog">

<!-- Modal grande -->
<div class="modal-dialog modal-lg">

<!-- Modal extra grande -->
<div class="modal-dialog modal-xl">
```

---

### 4.5 Sidebar (Barra Lateral)

#### Estructura Base
```html
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>
    
    <nav class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" 
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <x-icon name="home" /> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}" 
                   class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <x-icon name="users" /> Usuarios
                </a>
            </li>
        </ul>
    </nav>
</aside>
```

#### Reglas de Sidebar
- Responsive por defecto (colapsa en móviles)
- Iconos + texto para claridad
- Clase `active` en ruta actual
- Personalizar solo: logotipo, colores institucionales

---

## ✨ 5. ICONOS (FONT AWESOME - OBLIGATORIO)

### 5.1 Biblioteca Única
```html
<!-- Font Awesome Classic Solid ÚNICAMENTE -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

**PROHIBIDO:**
- ❌ Font Awesome Regular, Duotone, Thin
- ❌ Otras bibliotecas de iconos (Material Icons, Feather, etc.)

---

### 5.2 Componente Blade `<x-icon>`

#### Implementación del Componente

```php
// app/View/Components/Icon.php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public $name;
    public $class;

    public function __construct($name, $class = '')
    {
        $this->name = $name;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.icon');
    }

    public function getIconClass()
    {
        $icons = config('icons.icons');
        return $icons[$this->name] ?? 'fa-solid fa-question';
    }
}
```

```php
// resources/views/components/icon.blade.php
<i class="{{ $getIconClass() }} {{ $class }}" aria-hidden="true"></i>
```

---

### 5.3 Archivo de Configuración

```php
// config/icons.php
<?php

return [
    'icons' => [
        // Usuarios y Roles
        'user' => 'fa-solid fa-user',
        'userCircle' => 'fa-solid fa-circle-user',
        'users' => 'fa-solid fa-users',
        'userPlus' => 'fa-solid fa-user-plus',
        'userMinus' => 'fa-solid fa-user-minus',
        'userEdit' => 'fa-solid fa-user-pen',
        'userCheck' => 'fa-solid fa-user-check',
        'userClock' => 'fa-solid fa-user-clock',
        'userGroup' => 'fa-solid fa-users-gear',
        'userShield' => 'fa-solid fa-user-shield',
        'userTie' => 'fa-solid fa-user-tie',
        'idCard' => 'fa-solid fa-id-card',
        
        // Acciones (CRUD)
        'add' => 'fa-solid fa-plus',
        'edit' => 'fa-solid fa-pen-to-square',
        'delete' => 'fa-solid fa-trash',
        'view' => 'fa-solid fa-eye',
        'save' => 'fa-solid fa-floppy-disk',
        'cancel' => 'fa-solid fa-xmark',
        'search' => 'fa-solid fa-magnifying-glass',
        'copy' => 'fa-solid fa-copy',
        'cut' => 'fa-solid fa-scissors',
        'paste' => 'fa-solid fa-clipboard',
        'refresh' => 'fa-solid fa-arrows-rotate',
        'undo' => 'fa-solid fa-rotate-left',
        'redo' => 'fa-solid fa-rotate-right',
        'download' => 'fa-solid fa-download',
        'upload' => 'fa-solid fa-upload',
        'export' => 'fa-solid fa-file-export',
        'import' => 'fa-solid fa-file-import',
        'share' => 'fa-solid fa-share-nodes',
        'link' => 'fa-solid fa-link',
        
        // Estados y Alertas
        'success' => 'fa-solid fa-circle-check',
        'error' => 'fa-solid fa-circle-xmark',
        'warning' => 'fa-solid fa-triangle-exclamation',
        'info' => 'fa-solid fa-circle-info',
        'question' => 'fa-solid fa-circle-question',
        'bell' => 'fa-solid fa-bell',
        'bellSlash' => 'fa-solid fa-bell-slash',
        'checkCircle' => 'fa-solid fa-circle-check',
        'timesCircle' => 'fa-solid fa-circle-xmark',
        'exclamation' => 'fa-solid fa-circle-exclamation',
        'flag' => 'fa-solid fa-flag',
        'bookmark' => 'fa-solid fa-bookmark',
        
        // Archivos y Directorios
        'file' => 'fa-solid fa-file',
        'folder' => 'fa-solid fa-folder',
        'folderOpen' => 'fa-solid fa-folder-open',
        'filePdf' => 'fa-solid fa-file-pdf',
        'fileWord' => 'fa-solid fa-file-word',
        'fileExcel' => 'fa-solid fa-file-excel',
        'fileImage' => 'fa-solid fa-file-image',
        'fileVideo' => 'fa-solid fa-file-video',
        'fileAudio' => 'fa-solid fa-file-audio',
        'fileZip' => 'fa-solid fa-file-zipper',
        'fileCode' => 'fa-solid fa-file-code',
        'fileText' => 'fa-solid fa-file-lines',
        'fileContract' => 'fa-solid fa-file-contract',
        
        // Navegación
        'home' => 'fa-solid fa-house',
        'dashboard' => 'fa-solid fa-gauge',
        'menu' => 'fa-solid fa-bars',
        'close' => 'fa-solid fa-xmark',
        'arrowLeft' => 'fa-solid fa-arrow-left',
        'arrowRight' => 'fa-solid fa-arrow-right',
        'arrowUp' => 'fa-solid fa-arrow-up',
        'arrowDown' => 'fa-solid fa-arrow-down',
        'chevronLeft' => 'fa-solid fa-chevron-left',
        'chevronRight' => 'fa-solid fa-chevron-right',
        'chevronUp' => 'fa-solid fa-chevron-up',
        'chevronDown' => 'fa-solid fa-chevron-down',
        'angleLeft' => 'fa-solid fa-angle-left',
        'angleRight' => 'fa-solid fa-angle-right',
        'angleUp' => 'fa-solid fa-angle-up',
        'angleDown' => 'fa-solid fa-angle-down',
        
        // Procesos y Configuración
        'cog' => 'fa-solid fa-gear',
        'cogs' => 'fa-solid fa-gears',
        'spinner' => 'fa-solid fa-spinner',
        'sync' => 'fa-solid fa-arrows-rotate',
        'loading' => 'fa-solid fa-circle-notch',
        'hourglass' => 'fa-solid fa-hourglass-half',
        'stopwatch' => 'fa-solid fa-stopwatch',
        'clock' => 'fa-solid fa-clock',
        'calendar' => 'fa-solid fa-calendar',
        'calendarDay' => 'fa-solid fa-calendar-day',
        'calendarCheck' => 'fa-solid fa-calendar-check',
        
        // Finanzas
        'dollar' => 'fa-solid fa-dollar-sign',
        'money' => 'fa-solid fa-money-bill',
        'coins' => 'fa-solid fa-coins',
        'wallet' => 'fa-solid fa-wallet',
        'receipt' => 'fa-solid fa-receipt',
        'invoice' => 'fa-solid fa-file-invoice-dollar',
        'creditCard' => 'fa-solid fa-credit-card',
        'piggyBank' => 'fa-solid fa-piggy-bank',
        'chartLine' => 'fa-solid fa-chart-line',
        'chartBar' => 'fa-solid fa-chart-bar',
        'chartPie' => 'fa-solid fa-chart-pie',
        
        // Comunicación
        'envelope' => 'fa-solid fa-envelope',
        'phone' => 'fa-solid fa-phone',
        'mobile' => 'fa-solid fa-mobile',
        'comment' => 'fa-solid fa-comment',
        'comments' => 'fa-solid fa-comments',
        'message' => 'fa-solid fa-message',
        'inbox' => 'fa-solid fa-inbox',
        'paperPlane' => 'fa-solid fa-paper-plane',
        
        // Listas y Ordenamiento
        'list' => 'fa-solid fa-list',
        'listCheck' => 'fa-solid fa-list-check',
        'listOl' => 'fa-solid fa-list-ol',
        'listUl' => 'fa-solid fa-list-ul',
        'table' => 'fa-solid fa-table',
        'sortUp' => 'fa-solid fa-sort-up',
        'sortDown' => 'fa-solid fa-sort-down',
        'sort' => 'fa-solid fa-sort',
        'filter' => 'fa-solid fa-filter',
        
        // Acceso y Seguridad
        'lock' => 'fa-solid fa-lock',
        'lockOpen' => 'fa-solid fa-lock-open',
        'key' => 'fa-solid fa-key',
        'shield' => 'fa-solid fa-shield-halved',
        'shieldCheck' => 'fa-solid fa-shield-check',
        'eye' => 'fa-solid fa-eye',
        'eyeSlash' => 'fa-solid fa-eye-slash',
        'fingerprint' => 'fa-solid fa-fingerprint',
        
        // Reportes
        'print' => 'fa-solid fa-print',
        'fileChart' => 'fa-solid fa-file-chart-line',
        'fileReport' => 'fa-solid fa-file-lines',
        'analytics' => 'fa-solid fa-chart-simple',
        'database' => 'fa-solid fa-database',
    ],
];
```

---

### 5.4 Uso del Componente

```html
<!-- Uso básico -->
<x-icon name="user" />

<!-- Con clases adicionales -->
<x-icon name="save" class="me-2" />

<!-- En botones -->
<button class="btn btn-primary">
    <x-icon name="save" /> Guardar
</button>

<!-- En enlaces -->
<a href="#" class="nav-link">
    <x-icon name="home" /> Inicio
</a>

<!-- En tablas -->
<button class="btn btn-sm btn-info">
    <x-icon name="view" />
</button>
```

---

### 5.5 Reglas de Iconos

**OBLIGATORIO:**
1. ✅ Usar SOLO el componente `<x-icon>`
2. ✅ Todos los iconos deben estar definidos en `config/icons.php`
3. ✅ Usar alias en inglés (camelCase)
4. ✅ Font Awesome Solid únicamente

**PROHIBIDO:**
1. ❌ Clases Font Awesome directamente: `<i class="fa-solid fa-user"></i>`
2. ❌ Iconos fuera del catálogo oficial
3. ❌ Estilos inline en iconos
4. ❌ Iconos de otras bibliotecas

---

## 📝 6. BLADE: NOMENCLATURA Y ESTRUCTURA

### 6.1 Nombres de Archivos
```
// Nomenclatura: kebab-case
user-profile.blade.php          ✅
user_profile.blade.php          ❌
UserProfile.blade.php           ❌
userProfile.blade.php           ❌
```

### 6.2 Estructura de Carpetas
```
resources/views/
├── <proj-slug>/               # Prefijo del proyecto
│   ├── layouts/
│   │   └── app.blade.php
│   ├── modules/
│   │   ├── users/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   └── orders/
│   │       └── ...
│   └── components/
│       └── user-card.blade.php
```

### 6.3 Layouts Base

```blade
{{-- resources/views/<proj-slug>/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    
    {{-- Volt CSS --}}
    <link rel="stylesheet" href="{{ asset('css/volt.css') }}">
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    @include('<proj-slug>.partials.sidebar')
    
    <main class="content">
        @include('<proj-slug>.partials.navbar')
        
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>
    
    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Volt JS --}}
    <script src="{{ asset('js/volt.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
```

### 6.4 Vista que Extiende Layout

```blade
{{-- resources/views/<proj-slug>/modules/users/index.blade.php --}}
@extends('<proj-slug>.layouts.app')

@section('title', 'Listado de Usuarios')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Usuarios</h5>
                <a href="{{ route('<proj-slug>.users.create') }}" class="btn btn-primary">
                    <x-icon name="userPlus" /> Nuevo Usuario
                </a>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('<proj-slug>.users.show', $user) }}" 
                                           class="btn btn-sm btn-info">
                                            <x-icon name="view" />
                                        </a>
                                        <a href="{{ route('<proj-slug>.users.edit', $user) }}" 
                                           class="btn btn-sm btn-primary">
                                            <x-icon name="edit" />
                                        </a>
                                        <form action="{{ route('<proj-slug>.users.destroy', $user) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar usuario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <x-icon name="delete" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay usuarios registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 6.5 Componentes Blade

```php
// app/View/Components/UserCard.php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UserCard extends Component
{
    public $userId;
    public $userName;
    public $userEmail;

    public function __construct($userId, $userName, $userEmail)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
    }

    public function render()
    {
        return view('components.<proj-slug>-user-card');
    }
}
```

```blade
{{-- resources/views/components/<proj-slug>-user-card.blade.php --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <x-icon name="userCircle" class="fs-1 text-primary" />
            </div>
            <div>
                <h5 class="mb-1">{{ $userName }}</h5>
                <p class="mb-0 text-muted">{{ $userEmail }}</p>
            </div>
        </div>
    </div>
</div>
```

**Uso del componente:**
```blade
{{-- Prefijo del proyecto obligatorio --}}
<x-cs-user-card 
    :user-id="$user->id" 
    :user-name="$user->name" 
    :user-email="$user->email" 
/>
```

---

## 🎯 7. NOTIFICACIONES Y ALERTAS

### 7.1 SweetAlert2 (Modales de Confirmación)

```html
<!-- Incluir SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

#### Ejemplos de Uso

```javascript
// Confirmación de eliminación
Swal.fire({
    title: '¿Eliminar usuario?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    iconColor: '#FBA918', // Color institucional ámbar
    showCancelButton: true,
    confirmButtonColor: '#EF4444', // Danger
    cancelButtonColor: '#6B7280', // Secondary
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
}).then((result) => {
    if (result.isConfirmed) {
        // Ejecutar eliminación
        document.getElementById('delete-form').submit();
    }
});

// Confirmación exitosa
Swal.fire({
    title: 'Usuario guardado',
    text: 'Los datos se guardaron correctamente',
    icon: 'success',
    iconColor: '#10B981', // Success
    confirmButtonColor: '#1F2937', // Primary
    confirmButtonText: 'Aceptar'
});

// Error crítico
Swal.fire({
    title: 'Error al guardar',
    text: 'Ocurrió un problema al procesar la solicitud',
    icon: 'error',
    iconColor: '#EF4444', // Danger
    confirmButtonColor: '#1F2937',
    confirmButtonText: 'Entendido'
});
```

#### Reglas SweetAlert2
- **Usar para:** confirmaciones destructivas, errores críticos, éxitos importantes
- **Colores obligatorios:** institucionales (ver paleta)
- **Textos:** máximo 15 palabras
- **Botones:** verbos claros (Eliminar, Guardar, Cancelar)

---

### 7.2 Notyf (Notificaciones Flotantes)

```html
<!-- Incluir Notyf -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
```

#### Configuración Base

```javascript
// Inicializar Notyf con colores institucionales
const notyf = new Notyf({
    duration: 4000,
    position: {
        x: 'right',
        y: 'bottom',
    },
    types: [
        {
            type: 'success',
            background: '#10B981', // Verde institucional
            icon: {
                className: 'fa-solid fa-circle-check',
                tagName: 'i',
                color: 'white'
            }
        },
        {
            type: 'error',
            background: '#EF4444', // Rojo institucional
            icon: {
                className: 'fa-solid fa-circle-xmark',
                tagName: 'i',
                color: 'white'
            }
        },
        {
            type: 'warning',
            background: '#FBA918', // Ámbar institucional
            icon: {
                className: 'fa-solid fa-triangle-exclamation',
                tagName: 'i',
                color: 'white'
            }
        },
        {
            type: 'info',
            background: '#1F2937', // Gris oscuro institucional
            icon: {
                className: 'fa-solid fa-circle-info',
                tagName: 'i',
                color: 'white'
            }
        }
    ]
});
```

#### Ejemplos de Uso

```javascript
// Éxito (verde - esquina inferior derecha)
notyf.success('Datos guardados correctamente');

// Error (rojo - esquina inferior derecha)
notyf.error('Error al procesar la solicitud');

// Advertencia (ámbar - esquina inferior izquierda)
notyf.open({
    type: 'warning',
    message: 'Campos pendientes de completar',
    position: {x: 'left', y: 'bottom'}
});

// Información (gris - esquina inferior derecha)
notyf.open({
    type: 'info',
    message: 'Sin cambios por guardar'
});
```

#### Posiciones por Tipo

| Tipo | Color | Posición | Duración |
|------|-------|----------|----------|
| **Success** | Verde `#10B981` | Inferior derecha | 4s |
| **Error** | Rojo `#EF4444` | Inferior derecha | 5s |
| **Warning** | Ámbar `#FBA918` | Inferior izquierda | 5s |
| **Info** | Gris `#1F2937` | Inferior derecha | 3-4s |

#### Reglas Notyf
- ✅ Máximo 10 palabras por notificación
- ✅ Una notificación a la vez
- ✅ Solo para confirmaciones rápidas
- ❌ NO usar para acciones destructivas (usar SweetAlert2)
- ❌ NO incluir botones
- ❌ NO usar en ciclos automáticos

---

### 7.3 Cuándo Usar Cada Tipo

#### SweetAlert2 (Modales)
- ✅ Confirmaciones de eliminación/rechazo
- ✅ Advertencias antes de acciones riesgosas
- ✅ Errores críticos que requieren confirmación
- ✅ Mensajes importantes que deben leerse

#### Notyf (Flotantes)
- ✅ Confirmaciones rápidas de guardado
- ✅ Errores menores no bloqueantes
- ✅ Advertencias leves
- ✅ Información del sistema

---

## 📐 8. BOOTSTRAP: ORDEN DE CLASES

### Regla de Orden Obligatorio

```html
<!-- ORDEN CORRECTO -->
<div class="container mt-4 p-3 bg-light text-dark border rounded shadow">
    <!-- Contenido -->
</div>

<!-- 1. Layout (container, row, col-*)
    2. Espaciado (m-*, p-*, g-*)
    3. Dimensiones (w-*, h-*)
    4. Colores (bg-*, text-*)
    5. Bordes (border, rounded)
    6. Display (d-*, flex-*, justify-content-*, align-items-*)
    7. Otros (shadow, position-*, etc.)
-->
```

### Ejemplos Aplicados

```html
<!-- Tarjeta de usuario -->
<div class="card mb-4 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-primary">Usuario</h5>
            <span class="badge bg-success">Activo</span>
        </div>
        <p class="mb-2 text-muted">Información del usuario</p>
    </div>
</div>

<!-- Botón con clases ordenadas -->
<button class="btn btn-primary mt-3 px-4 d-flex align-items-center">
    <x-icon name="save" class="me-2" />
    Guardar cambios
</button>
```

---

## 📱 9. DISEÑO RESPONSIVE

### Breakpoints de Bootstrap
```scss
// Breakpoints institucionales
$grid-breakpoints: (
  xs: 0,       // Extra small (<576px)
  sm: 576px,   // Small (≥576px)
  md: 768px,   // Medium (≥768px)
  lg: 992px,   // Large (≥992px)
  xl: 1200px,  // Extra large (≥1200px)
  xxl: 1400px  // Extra extra large (≥1400px)
);
```

### Uso de Clases Responsive

```html
<!-- Columnas adaptables -->
<div class="row">
    <div class="col-12 col-md-6 col-lg-4">
        <!-- En móvil: 100%, tablet: 50%, desktop: 33% -->
    </div>
</div>

<!-- Visibilidad condicional -->
<div class="d-none d-md-block">
    <!-- Solo visible en tablet y superiores -->
</div>

<div class="d-block d-md-none">
    <!-- Solo visible en móviles -->
</div>

<!-- Botones responsivos -->
<button class="btn btn-primary btn-sm btn-md-lg">
    <!-- Pequeño en móvil, grande en desktop -->
</button>
```

### Reglas Responsive
- ✅ Probar en todos los breakpoints
- ✅ Sidebar colapsable en móviles
- ✅ Tablas con `table-responsive`
- ✅ Formularios en columna única en móviles

---

## 💬 10. MICROCOPY Y TEXTOS DE INTERFAZ

### 10.1 Principios de Microcopy

1. **Claridad**: lenguaje simple y directo
2. **Brevedad**: máximo 10 palabras por botón/notificación
3. **Tono institucional**: formal pero amigable
4. **Consistencia**: mismos términos para acciones equivalentes

### 10.2 Verbos Estándar para Botones

| Acción | Verbo | Ejemplo |
|--------|-------|---------|
| Crear | Guardar / Crear | "Guardar usuario" |
| Editar | Actualizar / Guardar cambios | "Actualizar datos" |
| Eliminar | Eliminar | "Eliminar registro" |
| Buscar | Buscar | "Buscar usuarios" |
| Confirmar | Aceptar / Confirmar | "Confirmar cambios" |
| Cancelar | Cancelar / Volver | "Cancelar operación" |
| Descargar | Descargar | "Descargar PDF" |
| Ver detalles | Ver más / Ver detalles | "Ver más información" |

### 10.3 Mensajes de Confirmación

```javascript
// ✅ CORRECTO
"¿Eliminar usuario?"
"Esta acción no se puede deshacer"

// ❌ INCORRECTO
"¿Estás completamente seguro de que deseas eliminar permanentemente este usuario del sistema?"
```

### 10.4 Mensajes de Éxito/Error

```javascript
// ✅ CORRECTO
"Usuario guardado correctamente"
"Error al guardar usuario"
"Campos pendientes de completar"

// ❌ INCORRECTO
"¡Felicidades! El usuario ha sido guardado exitosamente en la base de datos"
"Ups! Algo salió mal... No pudimos guardar el usuario :("
```

---

## ✅ 11. CHECKLIST DE CUMPLIMIENTO FRONTEND

### Antes de Entregar Cualquier Vista

- [ ] **Plantilla Volt aplicada** (estructura base intacta)
- [ ] **Paleta de colores institucional** (SCSS configurado)
- [ ] **Tipografía Nunito Sans** (importada desde Google Fonts)
- [ ] **Font Awesome Solid** (único biblioteca de iconos)
- [ ] **Componente `<x-icon>`** funcionando con `config/icons.php`
- [ ] **Iconos SOLO desde catálogo oficial** (prohibido clases directas)
- [ ] **Botones con colores correctos** (primary, secondary, danger, etc.)
- [ ] **Formularios con validación** (`@error`, `old()`, `is-invalid`)
- [ ] **Tablas con `table-responsive`** y acciones alineadas a la derecha
- [ ] **Modales con estructura correcta** (header, body, footer)
- [ ] **Sidebar responsive** con clase `active` en ruta actual
- [ ] **Notificaciones configuradas** (SweetAlert2 + Notyf con colores institucionales)
- [ ] **Clases Bootstrap en orden** (layout → espaciado → colores → display)
- [ ] **Responsive en todos los breakpoints** (xs, sm, md, lg, xl, xxl)
- [ ] **Microcopy consistente** (verbos claros, máximo 10 palabras)
- [ ] **Nombres de archivos kebab-case** (user-profile.blade.php)
- [ ] **Variables camelCase en Blade** (`$userName`, `$userId`)
- [ ] **Componentes con prefijo de proyecto** (`<x-cs-user-card>`)
- [ ] **Comentarios Blade obligatorios** en lógica compleja (`{{-- --}}`)
- [ ] **Sin estilos inline** (todo debe ser clases CSS/Bootstrap)
- [ ] **Accesibilidad básica** (`aria-hidden`, `aria-label` donde aplique)

---

## 🚫 12. PROHIBICIONES ABSOLUTAS

### ❌ NUNCA Hacer lo Siguiente

1. **Alterar estructura base de Volt** (header, sidebar, footer)
2. **Usar iconos fuera del catálogo** (Font Awesome Solid único)
3. **Clases Font Awesome directamente** (usar `<x-icon>`)
4. **Colores fuera de la paleta institucional**
5. **Otras bibliotecas de iconos** (Material, Feather, etc.)
6. **Estilos inline en HTML** (`style=""`)
7. **JavaScript inline** (`onclick=""`)
8. **Fuentes distintas a Nunito Sans**
9. **Bootstrap 4 o anterior** (5.3.x obligatorio)
10. **Clases Bootstrap desordenadas**
11. **Más de un botón primary por pantalla**
12. **Notificaciones sin colores institucionales**
13. **Formularios sin labels obligatorios**
14. **Tablas sin `table-responsive`**
15. **Componentes sin prefijo de proyecto**
16. **Archivos con nombres en PascalCase o camelCase**

---

## 📦 13. CONFIGURACIÓN DE ENTORNO

### 13.1 Dependencias NPM Obligatorias

```json
{
  "devDependencies": {
    "bootstrap": "^5.3.0",
    "laravel-mix": "^6.0.0",
    "sass": "^1.50.0",
    "sass-loader": "^12.0.0",
    "webpack": "^5.0.0",
    "webpack-cli": "^4.0.0"
  },
  "dependencies": {
    "@fortawesome/fontawesome-free": "^6.4.0",
    "notyf": "^3.10.0",
    "sweetalert2": "^11.0.0"
  }
}
```

### 13.2 Compilación de Assets

```javascript
// webpack.mix.js
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts')
   .version();
```

### 13.3 Archivo Principal SCSS

```scss
// resources/sass/app.scss

// Variables institucionales
@import 'variables';

// Bootstrap
@import '~bootstrap/scss/bootstrap';

// Font Awesome
@import '~@fortawesome/fontawesome-free/scss/fontawesome';
@import '~@fortawesome/fontawesome-free/scss/solid';

// Plantilla Volt
@import 'volt/volt';

// Componentes personalizados
@import 'components/buttons';
@import 'components/forms';
@import 'components/tables';
@import 'components/modals';
@import 'components/sidebar';
```

### 13.4 Variables SCSS Institucionales

```scss
// resources/sass/_variables.scss

// Colores institucionales
$primary: #1F2937;    // Gris oscuro
$secondary: #FB503B;  // Naranja rojizo
$tertiary: #31316A;   // Azul índigo
$success: #10B981;    // Verde
$danger: #EF4444;     // Rojo
$warning: #FBA918;    // Ámbar
$info: #3B82F6;       // Azul

// Tipografía
$font-family-sans-serif: 'Nunito Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
$font-size-base: 1rem;
$line-height-base: 1.5;

// Espaciado
$spacer: 1rem;
$spacers: (
  0: 0,
  1: $spacer * .25,
  2: $spacer * .5,
  3: $spacer,
  4: $spacer * 1.5,
  5: $spacer * 3,
);

// Breakpoints
$grid-breakpoints: (
  xs: 0,
  sm: 576px,
  md: 768px,
  lg: 992px,
  xl: 1200px,
  xxl: 1400px
);

// Bordes
$border-radius: 0.375rem;
$border-width: 1px;
$border-color: #E5E7EB;

// Sombras
$box-shadow-sm: 0 .125rem .25rem rgba(0, 0, 0, .075);
$box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
$box-shadow-lg: 0 1rem 3rem rgba(0, 0, 0, .175);
```

---

## 🎓 14. EJEMPLOS COMPLETOS

### 14.1 CRUD Completo de Usuario

#### Index (Listado)

```blade
@extends('cs.layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-dark fw-bold">
                        <x-icon name="users" class="me-2" />
                        Listado de Usuarios
                    </h5>
                    <a href="{{ route('cs.users.create') }}" class="btn btn-primary">
                        <x-icon name="userPlus" class="me-2" />
                        Nuevo Usuario
                    </a>
                </div>
                
                <div class="card-body">
                    {{-- Búsqueda --}}
                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <form action="{{ route('cs.users.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           name="search" 
                                           placeholder="Buscar por nombre o email..."
                                           value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <x-icon name="search" />
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    {{-- Tabla --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="text-muted">#{{ $user->id }}</td>
                                        <td class="fw-semibold">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $user->role }}</span>
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">
                                                    <x-icon name="checkCircle" class="me-1" />
                                                    Activo
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <x-icon name="timesCircle" class="me-1" />
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('cs.users.show', $user) }}" 
                                                   class="btn btn-sm btn-info"
                                                   title="Ver detalles">
                                                    <x-icon name="view" />
                                                </a>
                                                <a href="{{ route('cs.users.edit', $user) }}" 
                                                   class="btn btn-sm btn-primary"
                                                   title="Editar">
                                                    <x-icon name="edit" />
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete({{ $user->id }})"
                                                        title="Eliminar">
                                                    <x-icon name="delete" />
                                                </button>
                                            </div>
                                            
                                            <form id="delete-form-{{ $user->id }}" 
                                                  action="{{ route('cs.users.destroy', $user) }}" 
                                                  method="POST" 
                                                  class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <x-icon name="info" class="fs-3 mb-2 d-block" />
                                            No se encontraron usuarios
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginación --}}
                    @if($users->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Mostrando {{ $users->firstItem() }} - {{ $users->lastItem() }} 
                                de {{ $users->total() }} registros
                            </div>
                            <div>
                                {{ $users->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Configurar notificaciones
const notyf = new Notyf({
    duration: 4000,
    position: {x: 'right', y: 'bottom'},
    types: [
        {
            type: 'success',
            background: '#10B981',
            icon: {className: 'fa-solid fa-circle-check', tagName: 'i', color: 'white'}
        },
        {
            type: 'error',
            background: '#EF4444',
            icon: {className: 'fa-solid fa-circle-xmark', tagName: 'i', color: 'white'}
        }
    ]
});

// Confirmar eliminación
function confirmDelete(userId) {
    Swal.fire({
        title: '¿Eliminar usuario?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        iconColor: '#FBA918',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + userId).submit();
        }
    });
}

// Mostrar notificaciones de sesión
@if(session('success'))
    notyf.success('{{ session('success') }}');
@endif

@if(session('error'))
    notyf.error('{{ session('error') }}');
@endif
</script>
@endpush
```

#### Create/Edit (Formulario)

```blade
@extends('cs.layouts.app')

@section('title', isset($user) ? 'Editar Usuario' : 'Nuevo Usuario')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-dark fw-bold">
                        <x-icon name="{{ isset($user) ? 'edit' : 'userPlus' }}" class="me-2" />
                        {{ isset($user) ? 'Editar Usuario' : 'Nuevo Usuario' }}
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" 
                          action="{{ isset($user) ? route('cs.users.update', $user) : route('cs.users.store') }}">
                        @csrf
                        @if(isset($user))
                            @method('PUT')
                        @endif
                        
                        {{-- Nombre completo --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Nombre completo
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name ?? '') }}"
                                   placeholder="Ingrese el nombre completo"
                                   required 
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Correo electrónico
                                <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email ?? '') }}"
                                   placeholder="usuario@ejemplo.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Contraseña (solo en crear) --}}
                        @if(!isset($user))
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    Contraseña
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Mínimo 8 caracteres"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    Confirmar contraseña
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Repita la contraseña"
                                       required>
                            </div>
                        @endif
                        
                        {{-- Rol --}}
                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">
                                Rol
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role" 
                                    required>
                                <option value="">Seleccione un rol</option>
                                <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>
                                    Administrador
                                </option>
                                <option value="user" {{ old('role', $user->role ?? '') == 'user' ? 'selected' : '' }}>
                                    Usuario
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Estado --}}
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Usuario activo
                                </label>
                            </div>
                        </div>
                        
                        {{-- Botones --}}
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('cs.users.index') }}" class="btn btn-secondary">
                                <x-icon name="cancel" class="me-2" />
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <x-icon name="save" class="me-2" />
                                {{ isset($user) ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 🎯 15. INSTRUCCIONES PARA CLAUDE CODE

### Paso a Paso para Implementación

1. **Leer este documento completo** antes de comenzar cualquier desarrollo
2. **Verificar plantilla Volt** instalada y configurada correctamente
3. **Crear/actualizar `config/icons.php`** con el catálogo completo de iconos
4. **Implementar componente `<x-icon>`** (clase + vista Blade)
5. **Configurar variables SCSS** en `resources/sass/_variables.scss`
6. **Configurar notificaciones** (SweetAlert2 + Notyf) en layout principal
7. **Aplicar estándares a vistas existentes**:
   - Reemplazar clases Font Awesome por `<x-icon>`
   - Verificar paleta de colores institucional
   - Ordenar clases Bootstrap correctamente
   - Agregar responsive classes donde falten
8. **Probar en todos los breakpoints** (móvil, tablet, desktop)
9. **Validar con checklist** de cumplimiento (sección 11)
10. **Documentar cambios** en commits

### Comando para Verificar Cumplimiento

```bash
# Buscar usos incorrectos de iconos (clases directas)
grep -r "fa-solid\|fa-regular\|fa-duotone" resources/views/

# Buscar colores fuera de la paleta
grep -r "#[0-9a-fA-F]\{6\}" resources/sass/ resources/views/

# Buscar estilos inline prohibidos
grep -r 'style="' resources/views/

# Buscar JavaScript inline prohibido
grep -r 'onclick="' resources/views/
```

---

## 📚 16. RECURSOS ADICIONALES

### Documentación Oficial
- [Bootstrap 5.3](https://getbootstrap.com/docs/5.3/)
- [Font Awesome](https://fontawesome.com/icons)
- [Laravel Blade](https://laravel.com/docs/12.x/blade)
- [SweetAlert2](https://sweetalert2.github.io/)
- [Notyf](https://github.com/caroso1222/notyf)

### Plantilla Base
- **Volt Laravel Admin Dashboard** (proporcionada por CETAM)

---

## ✅ RESUMEN EJECUTIVO

### Lo Más Importante

1. ✅ **Plantilla Volt obligatoria** - NO modificar estructura base
2. ✅ **Iconos SOLO con `<x-icon>`** - Font Awesome Solid único
3. ✅ **Paleta institucional** - 7 colores definidos, PROHIBIDO otros
4. ✅ **Bootstrap 5.3.x** - Orden de clases obligatorio
5. ✅ **Tipografía Nunito Sans** - Única fuente permitida
6. ✅ **Notificaciones estándar** - SweetAlert2 + Notyf con colores institucionales
7. ✅ **Responsive obligatorio** - Probar en todos los breakpoints
8. ✅ **Nomenclatura kebab-case** - Archivos Blade
9. ✅ **Componentes con prefijo** - `<x-{proj-slug}-component>`
10. ✅ **Checklist completo** - Antes de entregar

---

**Versión:** 1.0  
**Última actualización:** Noviembre 2025  
**Mantenedor:** CETAM - Centro de Desarrollo Tecnológico Aplicado de México

---

## 🔍 VALIDACIÓN FINAL

Antes de considerar cualquier vista como "terminada", **DEBES** poder responder "SÍ" a todas estas preguntas:

1. ¿Usa la plantilla Volt sin modificar su estructura base?
2. ¿Todos los iconos usan `<x-icon>` en lugar de clases directas?
3. ¿Todos los colores provienen de la paleta institucional?
4. ¿La tipografía es Nunito Sans en todo el sitio?
5. ¿Las clases Bootstrap están en el orden correcto?
6. ¿Es completamente responsive (probado en todos los breakpoints)?
7. ¿Los botones tienen los colores correctos según su función?
8. ¿Los formularios tienen labels, validación y preservan valores?
9. ¿Las notificaciones usan SweetAlert2/Notyf con colores institucionales?
10. ¿El microcopy es claro, breve y consistente?
11. ¿Los archivos Blade usan nomenclatura kebab-case?
12. ¿Los componentes tienen el prefijo del proyecto?
13. ¿No hay estilos inline ni JavaScript inline?
14. ¿Las tablas tienen `table-responsive`?
15. ¿El sidebar es responsive y tiene la clase `active` en la ruta actual?

**Si la respuesta a cualquiera es "NO", el trabajo NO está completo.**

---

*Fin del documento de estándares de frontend Laravel - CETAM*
