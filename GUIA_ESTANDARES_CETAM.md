# GUÍA DE ESTÁNDARES CETAM - IMPLEMENTADOS

**Proyecto:** Centro de Servicios (CS) - Order QR System
**Versión:** 1.0.0
**Fecha:** 2025-11-20
**Autor:** CETAM Dev Team

---

## 📋 TABLA DE CONTENIDOS

1. [Introducción](#introducción)
2. [Archivos Creados](#archivos-creados)
3. [Configuraciones CETAM](#configuraciones-cetam)
4. [Sistema de Componentes](#sistema-de-componentes)
5. [Variables de Entorno](#variables-de-entorno)
6. [Convenciones de Código](#convenciones-de-código)
7. [Próximos Pasos](#próximos-pasos)

---

## 🎯 INTRODUCCIÓN

Este documento describe los estándares CETAM que han sido implementados en el proyecto Order QR System. El objetivo es mantener un código consistente, profesional y fácil de mantener siguiendo las directrices institucionales.

### Versiones Requeridas

- ✅ PHP: 8.2.x
- ✅ Laravel: 12.x (actual: 12.36.1)
- ⚠️ Node.js: 22.x (verificar versión actual)
- ✅ Bootstrap: 5.3.x
- ✅ Plantilla Volt integrada

---

## 📁 ARCHIVOS CREADOS

### 1. Archivos de Configuración

#### `config/cetam.cs.php`
Configuración centralizada del proyecto CS con:
- Identificación del proyecto (código, slug, nombre)
- Features habilitados (chat, QR scanner, payments, etc.)
- Configuración de base de datos
- Paginación
- Rutas
- Mobile app settings
- Configuración de pagos (MercadoPago)

**Uso:**
```php
// Obtener configuración
$perPage = config('cetam.cs.pagination.per_page'); // 15
$projectName = config('cetam.cs.name'); // "Centro de Servicios - Order QR System"
$isChatEnabled = config('cetam.cs.features.chat'); // true
```

#### `config/icons.php`
Catálogo estandarizado de iconos Font Awesome Classic Solid con aliases institucionales.

**Iconos disponibles:**
- Usuarios: `user`, `userCircle`, `userAdd`, `userRemove`, `userGroup`, `userTie`
- Acciones: `add`, `edit`, `delete`, `view`, `save`, `cancel`, `send`, `download`, `upload`, `search`, `refresh`
- Estados: `success`, `error`, `warning`, `info`, `pending`, `notification`
- Navegación: `home`, `dashboard`, `menu`, `back`, `forward`, `close`
- QR/Scanner: `qrcode`, `barcode`, `scanner`
- Órdenes: `business`, `store`, `order`, `package`, `truck`, `mapMarker`
- Estados de orden: `orderPending`, `orderProcessing`, `orderReady`, `orderDelivered`, `orderCancelled`

### 2. Sistema de Componentes Blade

#### Componente Icon

**Archivos:**
- `app/View/Components/Icon.php` (clase)
- `resources/views/components/icon.blade.php` (vista)

**Uso:**
```blade
{{-- Básico --}}
<x-icon name="user" />
<x-icon name="qrcode" />

{{-- Con clases CSS adicionales --}}
<x-icon name="success" class="text-success fs-4" />
<x-icon name="warning" class="text-warning me-2" />

{{-- En botones --}}
<button class="btn btn-primary">
    <x-icon name="save" /> Guardar
</button>

<a href="#" class="btn btn-danger">
    <x-icon name="delete" /> Eliminar
</a>

{{-- En badges --}}
<span class="badge bg-success">
    <x-icon name="success" /> Activo
</span>
```

#### Componente Alert

**Archivos:**
- `app/View/Components/CS/Alert.php` (clase)
- `resources/views/components/cs/alert.blade.php` (vista)

**Uso:**
```blade
{{-- Alerta de éxito --}}
<x-cs-alert type="success" message="Usuario creado correctamente" />

{{-- Alerta de error --}}
<x-cs-alert type="error" message="Error al procesar la solicitud" />

{{-- Alerta de advertencia --}}
<x-cs-alert type="warning" message="Esta acción no se puede deshacer" />

{{-- Alerta de información --}}
<x-cs-alert type="info" message="Recuerda completar todos los campos" />

{{-- Alerta no dismissible (sin botón cerrar) --}}
<x-cs-alert type="success" message="Guardado" :dismissible="false" />

{{-- Con sesión flash --}}
@if(session('success'))
    <x-cs-alert type="success" :message="session('success')" />
@endif

@if(session('error'))
    <x-cs-alert type="error" :message="session('error')" />
@endif
```

### 3. Documentación

- `CABECERAS_CETAM.md` - Plantillas de cabeceras para todos los tipos de archivos
- `GUIA_ESTANDARES_CETAM.md` - Este documento

---

## ⚙️ CONFIGURACIONES CETAM

### Variables de Entorno Actualizadas

El archivo `.env` ha sido actualizado con prefijos institucionales CETAM_CS_:

```env
# Identificación del Proyecto
CETAM_CS_PROJECT_CODE=CS
CETAM_CS_PROJECT_SLUG=cs
CETAM_CS_PROJECT_NAME="Centro de Servicios - Order QR System"

# Base de Datos con prefijo institucional
CETAM_CS_DB_CONNECTION=mysql
CETAM_CS_DB_HOST=127.0.0.1
CETAM_CS_DB_PORT=3306
CETAM_CS_DB_DATABASE=volt_dashboard
CETAM_CS_DB_USERNAME=root
CETAM_CS_DB_PASSWORD=

# Features
CETAM_CS_FEATURE_INVOICING=false
CETAM_CS_FEATURE_REPORTING=true
CETAM_CS_FEATURE_CHAT=true
CETAM_CS_FEATURE_QR_SCANNER=true
CETAM_CS_FEATURE_PAYMENTS=true
```

---

## 🎨 SISTEMA DE COMPONENTES

### Ventajas del Sistema de Componentes

1. **Consistencia visual**: Todos los iconos y alertas se ven iguales en toda la aplicación
2. **Mantenibilidad**: Un solo lugar para cambiar el diseño de todos los iconos/alertas
3. **Estandarización**: Uso de aliases institucionales en lugar de clases directas
4. **Facilidad de uso**: Sintaxis simple y clara

### Iconos Personalizados del Proyecto

Además de los iconos estándar CETAM, se han agregado iconos específicos para el sistema de órdenes:

```blade
{{-- QR y Scanner --}}
<x-icon name="qrcode" />      {{-- fa-solid fa-qrcode --}}
<x-icon name="barcode" />     {{-- fa-solid fa-barcode --}}
<x-icon name="scanner" />     {{-- fa-solid fa-camera --}}

{{-- Negocios y Órdenes --}}
<x-icon name="business" />    {{-- fa-solid fa-building --}}
<x-icon name="store" />       {{-- fa-solid fa-store --}}
<x-icon name="order" />       {{-- fa-solid fa-receipt --}}
<x-icon name="package" />     {{-- fa-solid fa-box --}}
<x-icon name="truck" />       {{-- fa-solid fa-truck --}}

{{-- Estados de Orden --}}
<x-icon name="orderPending" />    {{-- fa-solid fa-clock --}}
<x-icon name="orderProcessing" /> {{-- fa-solid fa-spinner --}}
<x-icon name="orderReady" />      {{-- fa-solid fa-check-circle --}}
<x-icon name="orderDelivered" />  {{-- fa-solid fa-check-double --}}
<x-icon name="orderCancelled" />  {{-- fa-solid fa-ban --}}
```

---

## 📝 CONVENCIONES DE CÓDIGO

### Nomenclatura

**Clases y Modelos:**
- PascalCase, singular
- Ejemplos: `User`, `Order`, `Business`

**Controladores:**
- PascalCase + sufijo `Controller`
- Ejemplos: `UserController`, `OrderController`, `DashboardController`

**Métodos y Funciones:**
- camelCase
- Ejemplos: `getUserList()`, `calculateTotal()`, `processOrder()`

**Variables:**
- camelCase
- Booleanos con prefijos: `is`, `has`, `can`, `should`
- Colecciones en plural
- Ejemplos: `$userName`, `$isActive`, `$orders`

**Archivos Blade:**
- kebab-case
- Ejemplos: `user-profile.blade.php`, `order-list.blade.php`

### Cabeceras Obligatorias

**Todos** los archivos PHP deben incluir una cabecera institucional. Ver `CABECERAS_CETAM.md` para plantillas completas.

Ejemplo para controlador:
```php
<?php

/**
 * ============================================
 * CETAM - Order Controller
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        OrderController.php
 * @description Controlador CRUD de órdenes
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */
```

Ejemplo para vista Blade:
```blade
{{--
============================================
CETAM - Listado de Órdenes
============================================

@project     Centro de Servicios (CS)
@file        index.blade.php
@description Vista principal del módulo de órdenes
@created     2025-11-20

============================================
--}}
```

### Documentación de Métodos (PHPDoc)

Todos los métodos públicos deben documentarse:

```php
/**
 * Display a listing of orders
 *
 * @return View
 */
public function index(): View
{
    $orders = Order::latest()->paginate(
        config('cetam.cs.pagination.per_page')
    );

    return view('orders.index', compact('orders'));
}

/**
 * Store a newly created order
 *
 * @param StoreOrderRequest $request
 * @return RedirectResponse
 */
public function store(StoreOrderRequest $request): RedirectResponse
{
    $order = Order::create($request->validated());

    return redirect()
        ->route('cs.orders.index')
        ->with('success', 'Orden creada correctamente');
}
```

---

## 🚀 PRÓXIMOS PASOS

### Tareas Pendientes para Completar Estándares

1. **Reorganizar Controladores**
   - [ ] Crear carpeta `app/Http/Controllers/CS/`
   - [ ] Mover controladores existentes a estructura CS
   - [ ] Actualizar namespaces

2. **Actualizar Rutas**
   - [ ] Modificar `routes/web.php` para usar prefijo `/p/cs/`
   - [ ] Actualizar nombres de rutas con prefijo `cs.`
   - [ ] Actualizar rutas API en `routes/api.php`

3. **Agregar Cabeceras**
   - [ ] Agregar cabeceras a todos los controladores
   - [ ] Agregar cabeceras a todos los modelos
   - [ ] Agregar cabeceras a todas las vistas Blade
   - [ ] Agregar cabeceras a migraciones y seeders

4. **Actualizar Vistas**
   - [ ] Reemplazar iconos directos por componente `<x-icon>`
   - [ ] Reemplazar alertas por componente `<x-cs-alert>`
   - [ ] Estandarizar layouts según plantilla Volt

5. **Form Requests**
   - [ ] Crear carpeta `app/Http/Requests/CS/`
   - [ ] Crear Form Requests para validaciones
   - [ ] Mover lógica de validación a Request classes

6. **Optimización**
   - [ ] Ejecutar `composer dump-autoload`
   - [ ] Ejecutar `php artisan config:cache`
   - [ ] Ejecutar `php artisan route:cache`
   - [ ] Compilar assets con `npm run build`

### Comandos Útiles

```bash
# Verificar configuración
php artisan config:show cetam.cs

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Desarrollo
php artisan serve
npm run dev

# Producción
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📚 REFERENCIAS

- Manual de Estándares CETAM: `INSTRUCCIONES_IMPLEMENTACION_LARAVEL_CETAM.md`
- Plantillas de Cabeceras: `CABECERAS_CETAM.md`
- Configuración del Proyecto: `config/cetam.cs.php`
- Catálogo de Iconos: `config/icons.php`

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Fase Completada ✅

- [x] Verificar versión de Laravel (12.36.1)
- [x] Crear `config/cetam.cs.php`
- [x] Crear `config/icons.php`
- [x] Actualizar `.env` con prefijos CETAM
- [x] Crear componente Icon
- [x] Crear componente Alert
- [x] Crear documentación de cabeceras
- [x] Crear guía de estándares

### Fase Pendiente ⚠️

- [ ] Reorganizar estructura de controladores
- [ ] Actualizar rutas con prefijos estándar
- [ ] Agregar cabeceras a archivos existentes
- [ ] Crear Form Requests
- [ ] Actualizar vistas con componentes
- [ ] Optimizar y cachear configuraciones

---

## 💡 TIPS Y MEJORES PRÁCTICAS

1. **Uso de Configuración**
   ```php
   // En lugar de hardcodear valores
   $perPage = 15;

   // Usar configuración
   $perPage = config('cetam.cs.pagination.per_page');
   ```

2. **Mensajes Flash con Componentes**
   ```php
   // En controlador
   return redirect()->back()->with('success', 'Operación exitosa');

   // En vista
   @if(session('success'))
       <x-cs-alert type="success" :message="session('success')" />
   @endif
   ```

3. **Iconos Consistentes**
   ```blade
   {{-- ❌ No hacer --}}
   <i class="fas fa-user"></i>

   {{-- ✅ Hacer --}}
   <x-icon name="user" />
   ```

4. **Nombres de Rutas**
   ```php
   // Seguir convención cs.modulo.accion
   route('cs.orders.index')
   route('cs.orders.create')
   route('cs.orders.store')
   route('cs.orders.show', $order)
   route('cs.orders.edit', $order)
   ```

---

**Documento creado:** 2025-11-20
**Última actualización:** 2025-11-20
**Versión:** 1.0.0
**Autor:** CETAM Dev Team

---

Para cualquier duda o sugerencia sobre los estándares CETAM, consultar el manual completo en `INSTRUCCIONES_IMPLEMENTACION_LARAVEL_CETAM.md`.
