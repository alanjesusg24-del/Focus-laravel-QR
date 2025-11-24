# PLANTILLAS DE CABECERAS CETAM

**Proyecto:** Centro de Servicios (CS) - Order QR System
**Versión:** 1.0.0
**Fecha:** 2025-11-24
**Autor:** CETAM Dev Team

---

## 📋 ÍNDICE

1. [Controladores PHP](#controladores-php)
2. [Modelos PHP](#modelos-php)
3. [Form Requests PHP](#form-requests-php)
4. [Middlewares PHP](#middlewares-php)
5. [Services PHP](#services-php)
6. [Vistas Blade](#vistas-blade)
7. [Archivos de Rutas](#archivos-de-rutas)
8. [Archivos de Configuración](#archivos-de-configuración)
9. [Migraciones](#migraciones)
10. [Seeders](#seeders)

---

## CONTROLADORES PHP

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Controlador]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreControlador].php
 * @description [Descripción breve del propósito del controlador]
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
// ... resto de imports
```

---

## MODELOS PHP

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Modelo]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreModelo].php
 * @description [Descripción de la entidad que representa]
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * @table       [nombre_tabla]
 * @primaryKey  [id]
 *
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// ... resto de imports
```

---

## FORM REQUESTS PHP

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Request]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreRequest].php
 * @description [Descripción de las validaciones]
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;
```

---

## MIDDLEWARES PHP

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Middleware]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreMiddleware].php
 * @description [Descripción del middleware]
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Middleware;

use Closure;
```

---

## SERVICES PHP

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Servicio]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [NombreService].php
 * @description [Descripción del servicio y su responsabilidad]
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Services;
```

---

## VISTAS BLADE

```blade
{{--
============================================
CETAM - [Nombre de la Vista]
============================================

@project     Centro de Servicios (CS)
@file        [nombre-vista].blade.php
@description [Descripción de qué muestra/hace la vista]
@created     2025-11-24
@version     1.0.0

============================================
--}}

@extends('[layout]')

@section('content')
    {{-- Contenido de la vista --}}
@endsection
```

---

## ARCHIVOS DE RUTAS

```php
<?php

/**
 * ============================================
 * CETAM - [Tipo de Rutas]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [archivo].php
 * @description [Descripción de las rutas contenidas]
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

use Illuminate\Support\Facades\Route;
```

---

## ARCHIVOS DE CONFIGURACIÓN

```php
<?php

/**
 * ============================================
 * CETAM - Configuración [Nombre]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [config].php
 * @description [Descripción de la configuración]
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

return [
    // ... configuraciones
];
```

---

## MIGRACIONES

```php
<?php

/**
 * ============================================
 * CETAM - Migración: [Descripción]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [timestamp]_[nombre].php
 * @description [Descripción de la migración]
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * @table       [nombre_tabla]
 *
 * ============================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
```

---

## SEEDERS

```php
<?php

/**
 * ============================================
 * CETAM - Seeder: [Nombre]
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        [Nombre]Seeder.php
 * @description [Descripción de los datos a insertar]
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
```

---

## NOTAS IMPORTANTES

1. **Fecha de creación**: Usar la fecha actual en formato YYYY-MM-DD
2. **Descripción**: Debe ser clara y concisa, explicando el propósito del archivo
3. **Versión**: Iniciar en 1.0.0 y seguir semver (semantic versioning)
4. **Copyright**: Siempre usar "CETAM © 2025"
5. **Consistencia**: Mantener el mismo estilo en todos los archivos

---

**Documento creado:** 2025-11-24
**Autor:** CETAM Dev Team
**Versión:** 1.0.0
