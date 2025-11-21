# PLANTILLAS DE CABECERAS CETAM

Este documento contiene las plantillas de cabeceras institucionales que deben agregarse a todos los archivos del proyecto según los estándares CETAM.

---

## CABECERA PARA ARCHIVOS PHP

### Controladores

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Controlador]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreControlador].php
 * @description [Breve descripción del propósito del controlador]
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
// ... resto del código
```

### Modelos

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Modelo]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreModelo].php
 * @description Modelo de [descripción de la entidad]
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// ... resto del código
```

### Requests

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Request]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreRequest].php
 * @description Validación para [descripción]
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 *
 * ============================================
 */

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;
// ... resto del código
```

### Migraciones

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre de la Migración]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [fecha]_[nombre_tabla].php
 * @description Migración para [descripción]
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 *
 * ============================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// ... resto del código
```

### Seeders

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Seeder]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreSeeder].php
 * @description Carga [descripción de los datos]
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 *
 * ============================================
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// ... resto del código
```

### Servicios

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Service]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreService].php
 * @description Lógica de negocio para [descripción]
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 *
 * ============================================
 */

namespace App\Services\CS;

// ... resto del código
```

### Componentes Blade (Clases)

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Componente]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreComponente].php
 * @description Componente [descripción]
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 *
 * ============================================
 */

namespace App\View\Components;

use Illuminate\View\Component;
// ... resto del código
```

---

## CABECERA PARA ARCHIVOS BLADE

### Vistas Principales

```blade
{{--
============================================
CETAM - [Nombre de la Vista]
============================================

@project     Centro de Servicios (CS)
@file        [nombre-vista].blade.php
@description [Descripción de la vista]
@created     2025-11-20

============================================
--}}

@extends('layouts.dashboard')

@section('content')
    {{-- Contenido de la vista --}}
@endsection
```

### Layouts

```blade
{{--
============================================
CETAM - [Nombre del Layout]
============================================

@project     Centro de Servicios (CS)
@file        [nombre-layout].blade.php
@description Layout [descripción]
@author      [Nombre del Desarrollador]
@created     2025-11-20
@version     1.0.0
@copyright   CETAM © 2025

============================================
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
{{-- resto del código --}}
```

### Componentes Blade (Vistas)

```blade
{{--
============================================
CETAM - [Nombre del Componente]
============================================

@props [prop1] - Descripción
@props [prop2] - Descripción

============================================
--}}

<div>
    {{-- Contenido del componente --}}
</div>
```

### Partials

```blade
{{--
============================================
CETAM - [Nombre del Partial]
============================================

@project     Centro de Servicios (CS)
@file        [nombre-partial].blade.php
@description [Descripción]
@created     2025-11-20

============================================
--}}

{{-- Contenido del partial --}}
```

---

## CABECERA PARA ARCHIVOS DE CONFIGURACIÓN

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre de la Configuración]
 * ============================================
 *
 * @file        [nombre-config].php
 * @description Configuración de [descripción]
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 *
 * ============================================
 */

return [
    // Configuraciones
];
```

---

## CABECERA PARA ARCHIVOS DE RUTAS

```php
<?php

/**
 * ============================================
 * CETAM - Rutas [Web/API]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [web/api].php
 * @description Definición de rutas [web/API] del proyecto
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 *
 * ============================================
 */

use Illuminate\Support\Facades\Route;
// ... resto del código
```

---

## DOCUMENTACIÓN DE MÉTODOS (PHPDoc)

Todos los métodos públicos deben incluir documentación PHPDoc:

```php
/**
 * Display a listing of resources
 *
 * @return View
 */
public function index(): View
{
    // ...
}

/**
 * Store a newly created resource
 *
 * @param StoreRequest $request
 * @return RedirectResponse
 */
public function store(StoreRequest $request): RedirectResponse
{
    // ...
}

/**
 * Update the specified resource
 *
 * @param UpdateRequest $request
 * @param Model $model
 * @return RedirectResponse
 */
public function update(UpdateRequest $request, Model $model): RedirectResponse
{
    // ...
}
```

---

## COMENTARIOS EN EL CÓDIGO

### Comentarios de una línea

```php
// Comentario breve para aclaración
$variable = $value;
```

### Comentarios multilínea

```php
/*
 * Comentario más extenso que explica
 * lógica compleja o secciones importantes
 */
foreach ($items as $item) {
    // ...
}
```

### Secciones en archivos largos

```php
/*
 * ============================================
 * SECCIÓN: Métodos CRUD
 * ============================================
 */

// Métodos index, create, store, etc.

/*
 * ============================================
 * SECCIÓN: Métodos Auxiliares
 * ============================================
 */

// Métodos helper privados
```

---

## USO DE LOS COMPONENTES CREADOS

### Componente Icon

```blade
{{-- Uso básico --}}
<x-icon name="user" />
<x-icon name="edit" />
<x-icon name="delete" />

{{-- Con clases adicionales --}}
<x-icon name="success" class="text-success fs-4" />
<x-icon name="warning" class="text-warning me-2" />

{{-- En botones --}}
<button class="btn btn-primary">
    <x-icon name="save" /> Guardar
</button>

{{-- Iconos disponibles en config/icons.php --}}
```

### Componente Alert

```blade
{{-- Alerta de éxito --}}
<x-cs-alert type="success" message="Operación exitosa" />

{{-- Alerta de error --}}
<x-cs-alert type="error" message="Error al procesar" />

{{-- Alerta de advertencia --}}
<x-cs-alert type="warning" message="Cuidado con esta acción" />

{{-- Alerta de información --}}
<x-cs-alert type="info" message="Información importante" />

{{-- Alerta no dismissible --}}
<x-cs-alert type="success" message="Guardado" :dismissible="false" />

{{-- Usando sesión flash --}}
@if(session('success'))
    <x-cs-alert type="success" :message="session('success')" />
@endif
```

---

## NOTAS IMPORTANTES

1. **Autor**: Reemplaza `[Nombre del Desarrollador]` con tu nombre o "CETAM Dev Team"
2. **Fecha**: Usa la fecha actual en formato YYYY-MM-DD
3. **Descripción**: Debe ser concisa y explicar el propósito del archivo
4. **Versión**: Comienza en 1.0.0 y sigue versionado semántico
5. **Copyright**: Siempre debe ser "CETAM © 2025"

---

## CHECKLIST DE CABECERAS

- [ ] Todos los controladores tienen cabecera institucional
- [ ] Todos los modelos tienen cabecera institucional
- [ ] Todos los requests tienen cabecera institucional
- [ ] Todas las migraciones tienen cabecera institucional
- [ ] Todos los seeders tienen cabecera institucional
- [ ] Todas las vistas blade tienen cabecera institucional
- [ ] Todos los layouts tienen cabecera institucional
- [ ] Todos los componentes tienen cabecera institucional
- [ ] Todos los archivos de configuración tienen cabecera institucional
- [ ] Archivos de rutas tienen cabecera institucional
- [ ] Métodos públicos tienen documentación PHPDoc

---

**Creado por:** CETAM Dev Team
**Fecha:** 2025-11-20
**Versión:** 1.0.0
