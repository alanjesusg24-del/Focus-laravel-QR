# Guía de Reestructuración de Carpetas - Focus QR System
## Cumplimiento del Estándar CETAM - Estructura de Archivos

**Proyecto:** Focus QR System  
**Código:** FQR  
**Objetivo:** Reorganizar la estructura de carpetas para cumplir con el estándar del Manual de Programación CETAM v4.0

---

## ⚠️ IMPORTANTE - LEE ANTES DE COMENZAR

Esta guía te ayudará a **mover archivos** de tu estructura actual a la estructura estándar requerida por CETAM.

**ANTES DE HACER CUALQUIER CAMBIO:**
1. ✅ Haz un **backup completo** de tu proyecto
2. ✅ Confirma que tienes control de versiones (Git)
3. ✅ Crea una **rama nueva** para estos cambios: `git checkout -b restructure-folders`
4. ✅ Lee toda esta guía primero, luego ejecuta los cambios

---

## 📋 ÍNDICE

1. [Estado Actual vs Estado Requerido](#estado-actual-vs-estado-requerido)
2. [Reestructuración de Controllers](#reestructuración-de-controllers)
3. [Creación de Services y Repositories](#creación-de-services-y-repositories)
4. [Reestructuración de Vistas (Views)](#reestructuración-de-vistas-views)
5. [Reestructuración de Modelos](#reestructuración-de-modelos)
6. [Actualización de Namespaces](#actualización-de-namespaces)
7. [Actualización de Rutas](#actualización-de-rutas)
8. [Actualización de Referencias en Código](#actualización-de-referencias-en-código)
9. [Script de Migración Automatizada](#script-de-migración-automatizada)
10. [Checklist de Verificación](#checklist-de-verificación)

---

## 1. ESTADO ACTUAL VS ESTADO REQUERIDO

### 📊 Comparación Visual

```
ESTRUCTURA ACTUAL (❌ No cumple estándar)
└── app/
    └── Http/
        └── Controllers/
            ├── OrderController.php
            ├── QrController.php
            ├── DeviceController.php
            └── NotificationController.php

ESTRUCTURA REQUERIDA (✅ Cumple estándar)
└── app/
    └── Http/
        └── Controllers/
            ├── Orders/
            │   └── OrderController.php
            ├── QrCodes/
            │   └── QrCodeController.php
            ├── Devices/
            │   └── DeviceController.php
            ├── Notifications/
            │   └── NotificationController.php
            └── Deliveries/
                └── DeliveryController.php
```

---

## 2. REESTRUCTURACIÓN DE CONTROLLERS

### 📂 Crear Subcarpetas por Módulo

**PASO 1:** Crear la estructura de carpetas

```bash
# Ejecutar desde la raíz del proyecto
mkdir -p app/Http/Controllers/Orders
mkdir -p app/Http/Controllers/QrCodes
mkdir -p app/Http/Controllers/Devices
mkdir -p app/Http/Controllers/Notifications
mkdir -p app/Http/Controllers/Deliveries
```

### 📦 Mover Controllers a sus Carpetas

#### Controller de Órdenes

**ANTES:**
```
app/Http/Controllers/OrderController.php
```

**DESPUÉS:**
```
app/Http/Controllers/Orders/OrderController.php
```

**Comando:**
```bash
mv app/Http/Controllers/OrderController.php app/Http/Controllers/Orders/OrderController.php
```

**Actualizar namespace en el archivo:**
```php
<?php

// ❌ ANTES
namespace App\Http\Controllers;

// ✅ DESPUÉS
namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;  // ← Agregar este use
```

---

#### Controller de Códigos QR

**ANTES:**
```
app/Http/Controllers/QrController.php
o
app/Http/Controllers/QRController.php
```

**DESPUÉS:**
```
app/Http/Controllers/QrCodes/QrCodeController.php
```

**Comandos:**
```bash
# Si tu archivo se llama QrController.php
mv app/Http/Controllers/QrController.php app/Http/Controllers/QrCodes/QrCodeController.php

# Si tu archivo se llama QRController.php
mv app/Http/Controllers/QRController.php app/Http/Controllers/QrCodes/QrCodeController.php
```

**Actualizar namespace y nombre de clase:**
```php
<?php

// ❌ ANTES
namespace App\Http\Controllers;

class QrController extends Controller  // o QRController
{
    // ...
}

// ✅ DESPUÉS
namespace App\Http\Controllers\QrCodes;

use App\Http\Controllers\Controller;

class QrCodeController extends Controller
{
    // ...
}
```

---

#### Controller de Dispositivos

**ANTES:**
```
app/Http/Controllers/DeviceController.php
```

**DESPUÉS:**
```
app/Http/Controllers/Devices/DeviceController.php
```

**Comando:**
```bash
mv app/Http/Controllers/DeviceController.php app/Http/Controllers/Devices/DeviceController.php
```

**Actualizar namespace:**
```php
<?php

// ❌ ANTES
namespace App\Http\Controllers;

// ✅ DESPUÉS
namespace App\Http\Controllers\Devices;

use App\Http\Controllers\Controller;
```

---

#### Controller de Notificaciones

**ANTES:**
```
app/Http/Controllers/NotificationController.php
```

**DESPUÉS:**
```
app/Http/Controllers/Notifications/NotificationController.php
```

**Comando:**
```bash
mv app/Http/Controllers/NotificationController.php app/Http/Controllers/Notifications/NotificationController.php
```

**Actualizar namespace:**
```php
<?php

// ❌ ANTES
namespace App\Http\Controllers;

// ✅ DESPUÉS
namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
```

---

#### Controller de Entregas (si existe)

**ANTES:**
```
app/Http/Controllers/DeliveryController.php
```

**DESPUÉS:**
```
app/Http/Controllers/Deliveries/DeliveryController.php
```

**Comando:**
```bash
# Solo si existe este controller
if [ -f app/Http/Controllers/DeliveryController.php ]; then
    mv app/Http/Controllers/DeliveryController.php app/Http/Controllers/Deliveries/DeliveryController.php
fi
```

**Actualizar namespace:**
```php
<?php

// ❌ ANTES
namespace App\Http\Controllers;

// ✅ DESPUÉS
namespace App\Http\Controllers\Deliveries;

use App\Http\Controllers\Controller;
```

---

### 📝 Tabla Resumen de Movimientos

| Archivo Actual | Ubicación Nueva | Namespace Nuevo |
|---------------|-----------------|-----------------|
| `OrderController.php` | `Orders/OrderController.php` | `App\Http\Controllers\Orders` |
| `QrController.php` | `QrCodes/QrCodeController.php` | `App\Http\Controllers\QrCodes` |
| `DeviceController.php` | `Devices/DeviceController.php` | `App\Http\Controllers\Devices` |
| `NotificationController.php` | `Notifications/NotificationController.php` | `App\Http\Controllers\Notifications` |
| `DeliveryController.php` | `Deliveries/DeliveryController.php` | `App\Http\Controllers\Deliveries` |

---

## 3. CREACIÓN DE SERVICES Y REPOSITORIES

Según el estándar CETAM, la **lógica de negocio** NO debe estar en los Controllers.

### 📂 Crear Carpetas

```bash
mkdir -p app/Services
mkdir -p app/Repositories
```

### 🏗️ Crear Services

#### OrderService.php

**Crear archivo:** `app/Services/OrderService.php`

```php
<?php
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Archivo: OrderService.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Servicio para lógica de negocio de órdenes
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;

class OrderService
{
    private OrderRepository $orderRepository;
    
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    
    /**
     * Obtiene todas las órdenes
     */
    public function getOrders()
    {
        return $this->orderRepository->findAll();
    }
    
    /**
     * Crea una nueva orden
     */
    public function createOrder(array $data): Order
    {
        return $this->orderRepository->create($data);
    }
    
    /**
     * Encuentra una orden por ID
     */
    public function findOrder(int $id): Order
    {
        return $this->orderRepository->findOrFail($id);
    }
    
    /**
     * Marca una orden como lista
     */
    public function markAsReady(int $id): Order
    {
        $order = $this->findOrder($id);
        
        return $this->orderRepository->update($order->id, [
            'status' => Order::STATUS_READY,
            'ready_at' => now(),
        ]);
    }
    
    /**
     * Marca una orden como entregada
     */
    public function markAsDelivered(int $id): Order
    {
        $order = $this->findOrder($id);
        
        return $this->orderRepository->update($order->id, [
            'status' => Order::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
    }
}
```

#### QrCodeService.php

**Crear archivo:** `app/Services/QrCodeService.php`

```php
<?php
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Archivo: QrCodeService.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Servicio para generación y gestión de códigos QR
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */

namespace App\Services;

use App\Models\Order;
use App\Models\QrCode;

class QrCodeService
{
    /**
     * Genera código QR para una orden
     */
    public function generateForOrder(Order $order): QrCode
    {
        // Mover aquí la lógica de generación de QR que tengas en el controller
        // Ejemplo básico:
        
        $code = $this->generateUniqueCode();
        
        return QrCode::create([
            'order_id' => $order->id,
            'code' => $code,
            'type' => 'order',
        ]);
    }
    
    /**
     * Genera código único
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = 'FQR-' . strtoupper(\Str::random(10));
        } while (QrCode::where('code', $code)->exists());
        
        return $code;
    }
    
    /**
     * Encuentra QR por ID
     */
    public function findById(int $id): QrCode
    {
        return QrCode::findOrFail($id);
    }
}
```

#### NotificationService.php

**Crear archivo:** `app/Services/NotificationService.php`

```php
<?php
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Archivo: NotificationService.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Servicio para envío de notificaciones push
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */

namespace App\Services;

use App\Models\Order;

class NotificationService
{
    /**
     * Envía notificación cuando la orden está lista
     */
    public function notifyOrderReady(Order $order): void
    {
        // Mover aquí la lógica de notificaciones que tengas
        
        if (!$order->device || !$order->device->fcm_token) {
            return;
        }
        
        // Lógica de envío de notificación push
        // Usar Firebase Cloud Messaging, etc.
    }
}
```

### 🗄️ Crear Repositories

#### OrderRepository.php

**Crear archivo:** `app/Repositories/OrderRepository.php`

```php
<?php
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Archivo: OrderRepository.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Repositorio para acceso a datos de órdenes
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    /**
     * Obtiene todas las órdenes
     */
    public function findAll(): Collection
    {
        return Order::with(['qrCode', 'device'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Encuentra una orden por ID
     */
    public function findOrFail(int $id): Order
    {
        return Order::with(['qrCode', 'device', 'notifications'])
            ->findOrFail($id);
    }
    
    /**
     * Crea una orden
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }
    
    /**
     * Actualiza una orden
     */
    public function update(int $id, array $data): Order
    {
        $order = $this->findOrFail($id);
        $order->update($data);
        return $order->fresh();
    }
    
    /**
     * Elimina una orden
     */
    public function delete(int $id): bool
    {
        $order = $this->findOrFail($id);
        return $order->delete();
    }
    
    /**
     * Obtiene órdenes por estado
     */
    public function findByStatus(string $status): Collection
    {
        return Order::where('status', $status)
            ->with(['qrCode', 'device'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
```

#### DeviceRepository.php

**Crear archivo:** `app/Repositories/DeviceRepository.php`

```php
<?php
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Archivo: DeviceRepository.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Repositorio para acceso a datos de dispositivos
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */

namespace App\Repositories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Collection;

class DeviceRepository
{
    /**
     * Encuentra dispositivo por device_id
     */
    public function findByDeviceId(string $deviceId): ?Device
    {
        return Device::where('device_id', $deviceId)->first();
    }
    
    /**
     * Registra o actualiza un dispositivo
     */
    public function upsert(array $data): Device
    {
        return Device::updateOrCreate(
            ['device_id' => $data['device_id']],
            $data
        );
    }
    
    /**
     * Actualiza token FCM
     */
    public function updateFcmToken(string $deviceId, string $fcmToken): Device
    {
        $device = $this->findByDeviceId($deviceId);
        $device->update(['fcm_token' => $fcmToken]);
        return $device->fresh();
    }
}
```

---

## 4. REESTRUCTURACIÓN DE VISTAS (VIEWS)

### 📂 Crear Estructura de Vistas

```bash
# Crear carpeta principal del proyecto
mkdir -p resources/views/focus-qr

# Crear subcarpetas obligatorias
mkdir -p resources/views/focus-qr/layouts
mkdir -p resources/views/focus-qr/components
mkdir -p resources/views/focus-qr/partials
mkdir -p resources/views/focus-qr/modules

# Crear carpetas por módulo
mkdir -p resources/views/focus-qr/modules/orders
mkdir -p resources/views/focus-qr/modules/qr-codes
mkdir -p resources/views/focus-qr/modules/devices
mkdir -p resources/views/focus-qr/modules/notifications
mkdir -p resources/views/focus-qr/modules/deliveries
```

### 📦 Mover Vistas

#### Layouts

**ANTES:**
```
resources/views/
├── layout.blade.php
├── master.blade.php
└── app.blade.php
```

**DESPUÉS:**
```
resources/views/focus-qr/layouts/
└── app.blade.php
```

**Comando:**
```bash
# Mover tu layout principal (ajusta el nombre según tu archivo)
cp resources/views/layout.blade.php resources/views/focus-qr/layouts/app.blade.php
# o
cp resources/views/app.blade.php resources/views/focus-qr/layouts/app.blade.php
```

**Actualizar contenido del layout:**
```blade
{{--
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Vista: app.blade.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Layout maestro principal
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Focus QR System</title>
    
    {{-- CSS --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>
    {{-- Header --}}
    @include('focus-qr.partials.header')
    
    {{-- Sidebar --}}
    @include('focus-qr.partials.sidebar')
    
    {{-- Contenido Principal --}}
    <main class="container py-4">
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('focus-qr.partials.footer')
    
    {{-- JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
    
    @yield('scripts')
</body>
</html>
```

---

#### Partials (Header, Footer, Sidebar)

**ANTES:**
```
resources/views/
├── header.blade.php
├── footer.blade.php
└── sidebar.blade.php
```

**DESPUÉS:**
```
resources/views/focus-qr/partials/
├── header.blade.php
├── footer.blade.php
└── sidebar.blade.php
```

**Comandos:**
```bash
# Mover partials (si existen)
[ -f resources/views/header.blade.php ] && mv resources/views/header.blade.php resources/views/focus-qr/partials/header.blade.php
[ -f resources/views/footer.blade.php ] && mv resources/views/footer.blade.php resources/views/focus-qr/partials/footer.blade.php
[ -f resources/views/sidebar.blade.php ] && mv resources/views/sidebar.blade.php resources/views/focus-qr/partials/sidebar.blade.php
```

**Agregar cabecera en cada partial:**
```blade
{{--
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Vista: header.blade.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Encabezado de la aplicación
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */
--}}

{{-- Tu código del header aquí --}}
```

---

#### Vistas de Órdenes

**ANTES:**
```
resources/views/orders/
├── index.blade.php
├── create.blade.php
├── show.blade.php
└── edit.blade.php
```

**DESPUÉS:**
```
resources/views/focus-qr/modules/orders/
├── index.blade.php
├── create.blade.php
├── show.blade.php
└── edit.blade.php
```

**Comandos:**
```bash
# Si tienes carpeta orders
if [ -d resources/views/orders ]; then
    mv resources/views/orders/* resources/views/focus-qr/modules/orders/
    rmdir resources/views/orders
fi
```

**Actualizar cada vista:**
```blade
{{--
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Vista: index.blade.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Listado de órdenes
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */
--}}

{{-- ❌ ANTES --}}
@extends('layout')

{{-- ✅ DESPUÉS --}}
@extends('focus-qr.layouts.app')

@section('title', 'Órdenes')

@section('content')
    {{-- Tu contenido aquí --}}
@endsection
```

---

#### Vistas de Códigos QR

**ANTES:**
```
resources/views/qr/
├── index.blade.php
└── show.blade.php
```

**DESPUÉS:**
```
resources/views/focus-qr/modules/qr-codes/
├── index.blade.php
└── show.blade.php
```

**Comandos:**
```bash
# Si tienes carpeta qr o qrcodes
if [ -d resources/views/qr ]; then
    mv resources/views/qr/* resources/views/focus-qr/modules/qr-codes/
    rmdir resources/views/qr
fi

if [ -d resources/views/qrcodes ]; then
    mv resources/views/qrcodes/* resources/views/focus-qr/modules/qr-codes/
    rmdir resources/views/qrcodes
fi
```

---

#### Vistas de Dispositivos

**ANTES:**
```
resources/views/devices/
└── index.blade.php
```

**DESPUÉS:**
```
resources/views/focus-qr/modules/devices/
└── index.blade.php
```

**Comandos:**
```bash
if [ -d resources/views/devices ]; then
    mv resources/views/devices/* resources/views/focus-qr/modules/devices/
    rmdir resources/views/devices
fi
```

---

#### Vistas de Entregas

**ANTES:**
```
resources/views/deliveries/
├── index.blade.php
└── scanner.blade.php
```

**DESPUÉS:**
```
resources/views/focus-qr/modules/deliveries/
├── index.blade.php
└── scanner.blade.php
```

**Comandos:**
```bash
if [ -d resources/views/deliveries ]; then
    mv resources/views/deliveries/* resources/views/focus-qr/modules/deliveries/
    rmdir resources/views/deliveries
fi
```

---

### 📝 Tabla Resumen de Movimientos de Vistas

| Ubicación Actual | Ubicación Nueva |
|-----------------|-----------------|
| `resources/views/layout.blade.php` | `resources/views/focus-qr/layouts/app.blade.php` |
| `resources/views/header.blade.php` | `resources/views/focus-qr/partials/header.blade.php` |
| `resources/views/footer.blade.php` | `resources/views/focus-qr/partials/footer.blade.php` |
| `resources/views/sidebar.blade.php` | `resources/views/focus-qr/partials/sidebar.blade.php` |
| `resources/views/orders/*` | `resources/views/focus-qr/modules/orders/*` |
| `resources/views/qr/*` | `resources/views/focus-qr/modules/qr-codes/*` |
| `resources/views/devices/*` | `resources/views/focus-qr/modules/devices/*` |
| `resources/views/deliveries/*` | `resources/views/focus-qr/modules/deliveries/*` |

---

## 5. REESTRUCTURACIÓN DE MODELOS

Los modelos **NO necesitan moverse**, pero SÍ necesitan actualizaciones.

### ✅ Ubicación Correcta

```
app/Models/
├── Order.php
├── QrCode.php
├── Device.php
├── Notification.php
└── Delivery.php
```

### 📝 Actualizar Cada Modelo

**Agregar cabecera CETAM:**

```php
<?php
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Archivo: Order.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Modelo de órdenes
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    
    // Constantes de estados
    const STATUS_PENDING = 'pending';
    const STATUS_READY = 'ready';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    
    /**
     * Atributos asignables masivamente
     */
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'description',
        'status',
        'ready_at',
        'delivered_at',
    ];
    
    /**
     * Casting de atributos
     */
    protected $casts = [
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];
    
    /**
     * Relación: Orden tiene un código QR
     */
    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }
    
    /**
     * Relación: Orden pertenece a un dispositivo
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
    
    /**
     * Scope: Órdenes pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
    
    /**
     * Scope: Órdenes listas
     */
    public function scopeReady($query)
    {
        return $query->where('status', self::STATUS_READY);
    }
}
```

---

## 6. ACTUALIZACIÓN DE NAMESPACES

### 🔄 Buscar y Reemplazar en Controllers

Después de mover los controllers, necesitas actualizar los `use` statements.

**Ejemplo en OrderController:**

```php
<?php
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Archivo: OrderController.php
 * Proyecto: Focus QR System (FQR)
 * Descripción: Controlador de órdenes
 * Autor: [Tu Nombre]
 * Fecha de creación: 2025-12-03
 * Versión: 1.0.0
 */

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;  // ← Importante agregar esto
use App\Services\OrderService;
use App\Services\QrCodeService;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;
    private QrCodeService $qrCodeService;
    
    public function __construct(
        OrderService $orderService,
        QrCodeService $qrCodeService
    ) {
        $this->orderService = $orderService;
        $this->qrCodeService = $qrCodeService;
    }
    
    /**
     * Muestra listado de órdenes
     */
    public function index()
    {
        $orders = $this->orderService->getOrders();
        return view('focus-qr.modules.orders.index', compact('orders'));
    }
    
    // ... resto de métodos
}
```

---

## 7. ACTUALIZACIÓN DE RUTAS

### 📝 Actualizar routes/web.php

**ANTES:**
```php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QrController;

Route::get('/orders', [OrderController::class, 'index']);
Route::get('/qr/{id}', [QrController::class, 'show']);
```

**DESPUÉS:**
```php
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\QrCodes\QrCodeController;
use App\Http\Controllers\Devices\DeviceController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Deliveries\DeliveryController;
use Illuminate\Support\Facades\Route;

Route::prefix('p/focus-qr')->name('focus-qr.')->group(function () {
    
    // Rutas de órdenes
    Route::resource('orders', OrderController::class);
    Route::post('orders/{id}/mark-ready', [OrderController::class, 'markAsReady'])
        ->name('orders.mark-ready');
    Route::post('orders/{id}/mark-delivered', [OrderController::class, 'markAsDelivered'])
        ->name('orders.mark-delivered');
    
    // Rutas de códigos QR
    Route::get('qr-codes', [QrCodeController::class, 'index'])
        ->name('qr-codes.index');
    Route::get('qr-codes/{id}', [QrCodeController::class, 'show'])
        ->name('qr-codes.show');
    
    // Rutas de dispositivos
    Route::resource('devices', DeviceController::class)
        ->only(['index', 'show']);
    
    // Rutas de entregas
    Route::get('deliveries', [DeliveryController::class, 'index'])
        ->name('deliveries.index');
    Route::get('deliveries/scanner', [DeliveryController::class, 'scanner'])
        ->name('deliveries.scanner');
    Route::post('deliveries/scan', [DeliveryController::class, 'processScan'])
        ->name('deliveries.scan');
});
```

### 📝 Actualizar routes/api.php (para Flutter)

```php
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\Devices\DeviceController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/focus-qr')->name('api.v1.focus-qr.')->group(function () {
    
    // Asociar orden con dispositivo
    Route::post('orders/associate', [OrderController::class, 'associateWithDevice']);
    
    // Verificar estado de orden
    Route::get('orders/{orderNumber}/status', [OrderController::class, 'checkStatus']);
    
    // Registrar token FCM
    Route::post('devices/register-token', [DeviceController::class, 'registerToken']);
    
    // Obtener QR del cliente
    Route::get('orders/{orderNumber}/client-qr', [OrderController::class, 'getClientQr']);
});
```

---

## 8. ACTUALIZACIÓN DE REFERENCIAS EN CÓDIGO

### 🔍 Buscar y Reemplazar

Necesitas actualizar todas las referencias a:
- Rutas
- Vistas
- Controllers

#### Actualizar Referencias a Vistas

**En todos los Controllers:**

```php
// ❌ ANTES
return view('orders.index', $data);
return view('qr.show', $data);

// ✅ DESPUÉS
return view('focus-qr.modules.orders.index', $data);
return view('focus-qr.modules.qr-codes.show', $data);
```

**Comando para buscar todas las referencias:**
```bash
# Buscar todas las llamadas a view()
grep -r "return view(" app/Http/Controllers/
```

#### Actualizar Referencias a Rutas

**En todas las vistas Blade:**

```blade
{{-- ❌ ANTES --}}
<a href="{{ route('orders.index') }}">Órdenes</a>
<form action="{{ route('orders.store') }}">

{{-- ✅ DESPUÉS --}}
<a href="{{ route('focus-qr.orders.index') }}">Órdenes</a>
<form action="{{ route('focus-qr.orders.store') }}">
```

**Comando para buscar referencias:**
```bash
# Buscar route() en vistas
grep -r "route(" resources/views/
```

#### Actualizar Redirects en Controllers

```php
// ❌ ANTES
return redirect()->route('orders.index');

// ✅ DESPUÉS
return redirect()->route('focus-qr.orders.index');
```

---

## 9. SCRIPT DE MIGRACIÓN AUTOMATIZADA

### 🤖 Script Bash para Automatizar

**Crear archivo:** `restructure.sh` en la raíz del proyecto

```bash
#!/bin/bash

echo "========================================"
echo "Reestructuración de Focus QR System"
echo "Cumplimiento Estándar CETAM"
echo "========================================"
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para crear directorio
create_dir() {
    if [ ! -d "$1" ]; then
        mkdir -p "$1"
        echo -e "${GREEN}✓${NC} Creado: $1"
    else
        echo -e "${YELLOW}⚠${NC} Ya existe: $1"
    fi
}

# Función para mover archivo
move_file() {
    if [ -f "$1" ]; then
        mv "$1" "$2"
        echo -e "${GREEN}✓${NC} Movido: $1 → $2"
    else
        echo -e "${YELLOW}⚠${NC} No existe: $1"
    fi
}

echo "Paso 1: Creando estructura de Controllers..."
create_dir "app/Http/Controllers/Orders"
create_dir "app/Http/Controllers/QrCodes"
create_dir "app/Http/Controllers/Devices"
create_dir "app/Http/Controllers/Notifications"
create_dir "app/Http/Controllers/Deliveries"

echo ""
echo "Paso 2: Creando Services y Repositories..."
create_dir "app/Services"
create_dir "app/Repositories"

echo ""
echo "Paso 3: Creando estructura de Vistas..."
create_dir "resources/views/focus-qr"
create_dir "resources/views/focus-qr/layouts"
create_dir "resources/views/focus-qr/components"
create_dir "resources/views/focus-qr/partials"
create_dir "resources/views/focus-qr/modules"
create_dir "resources/views/focus-qr/modules/orders"
create_dir "resources/views/focus-qr/modules/qr-codes"
create_dir "resources/views/focus-qr/modules/devices"
create_dir "resources/views/focus-qr/modules/notifications"
create_dir "resources/views/focus-qr/modules/deliveries"

echo ""
echo "Paso 4: Moviendo Controllers..."
move_file "app/Http/Controllers/OrderController.php" "app/Http/Controllers/Orders/OrderController.php"
move_file "app/Http/Controllers/QrController.php" "app/Http/Controllers/QrCodes/QrCodeController.php"
move_file "app/Http/Controllers/QRController.php" "app/Http/Controllers/QrCodes/QrCodeController.php"
move_file "app/Http/Controllers/DeviceController.php" "app/Http/Controllers/Devices/DeviceController.php"
move_file "app/Http/Controllers/NotificationController.php" "app/Http/Controllers/Notifications/NotificationController.php"
move_file "app/Http/Controllers/DeliveryController.php" "app/Http/Controllers/Deliveries/DeliveryController.php"

echo ""
echo "Paso 5: Moviendo Vistas (si existen)..."

# Mover vistas de orders
if [ -d "resources/views/orders" ]; then
    for file in resources/views/orders/*; do
        if [ -f "$file" ]; then
            filename=$(basename "$file")
            move_file "$file" "resources/views/focus-qr/modules/orders/$filename"
        fi
    done
    rmdir resources/views/orders 2>/dev/null
fi

# Mover vistas de qr
if [ -d "resources/views/qr" ]; then
    for file in resources/views/qr/*; do
        if [ -f "$file" ]; then
            filename=$(basename "$file")
            move_file "$file" "resources/views/focus-qr/modules/qr-codes/$filename"
        fi
    done
    rmdir resources/views/qr 2>/dev/null
fi

# Mover vistas de devices
if [ -d "resources/views/devices" ]; then
    for file in resources/views/devices/*; do
        if [ -f "$file" ]; then
            filename=$(basename "$file")
            move_file "$file" "resources/views/focus-qr/modules/devices/$filename"
        fi
    done
    rmdir resources/views/devices 2>/dev/null
fi

# Mover vistas de deliveries
if [ -d "resources/views/deliveries" ]; then
    for file in resources/views/deliveries/*; do
        if [ -f "$file" ]; then
            filename=$(basename "$file")
            move_file "$file" "resources/views/focus-qr/modules/deliveries/$filename"
        fi
    done
    rmdir resources/views/deliveries 2>/dev/null
fi

echo ""
echo "========================================"
echo -e "${GREEN}✓ Reestructuración completada${NC}"
echo "========================================"
echo ""
echo "SIGUIENTES PASOS MANUALES:"
echo "1. Actualizar namespaces en Controllers movidos"
echo "2. Actualizar referencias a vistas en Controllers"
echo "3. Actualizar rutas en routes/web.php"
echo "4. Actualizar route() en vistas Blade"
echo "5. Crear Services y Repositories"
echo "6. Agregar cabeceras CETAM a todos los archivos"
echo "7. Ejecutar: php artisan route:clear"
echo "8. Ejecutar: php artisan view:clear"
echo "9. Ejecutar: php artisan config:clear"
echo "10. Probar la aplicación"
echo ""
```

**Ejecutar el script:**

```bash
# Dar permisos de ejecución
chmod +x restructure.sh

# Ejecutar
./restructure.sh
```

---

## 10. CHECKLIST DE VERIFICACIÓN

### ✅ Verificación de Estructura

```bash
# Verificar estructura de controllers
tree app/Http/Controllers/

# Debe mostrar:
# app/Http/Controllers/
# ├── Orders/
# │   └── OrderController.php
# ├── QrCodes/
# │   └── QrCodeController.php
# ├── Devices/
# │   └── DeviceController.php
# ├── Notifications/
# │   └── NotificationController.php
# └── Deliveries/
#     └── DeliveryController.php

# Verificar estructura de vistas
tree resources/views/focus-qr/

# Debe mostrar:
# resources/views/focus-qr/
# ├── layouts/
# │   └── app.blade.php
# ├── components/
# ├── partials/
# │   ├── header.blade.php
# │   ├── footer.blade.php
# │   └── sidebar.blade.php
# └── modules/
#     ├── orders/
#     ├── qr-codes/
#     ├── devices/
#     ├── notifications/
#     └── deliveries/

# Verificar Services y Repositories
ls -la app/Services/
ls -la app/Repositories/
```

### ✅ Checklist Completo

- [ ] **Controllers movidos a subcarpetas**
  - [ ] OrderController en `Orders/`
  - [ ] QrCodeController en `QrCodes/`
  - [ ] DeviceController en `Devices/`
  - [ ] NotificationController en `Notifications/`
  - [ ] DeliveryController en `Deliveries/`

- [ ] **Namespaces actualizados en Controllers**
  - [ ] `namespace App\Http\Controllers\[Modulo]`
  - [ ] `use App\Http\Controllers\Controller` agregado

- [ ] **Services creados**
  - [ ] OrderService.php
  - [ ] QrCodeService.php
  - [ ] NotificationService.php

- [ ] **Repositories creados**
  - [ ] OrderRepository.php
  - [ ] DeviceRepository.php

- [ ] **Estructura de vistas creada**
  - [ ] Carpeta `focus-qr/` principal
  - [ ] Subcarpeta `layouts/`
  - [ ] Subcarpeta `components/`
  - [ ] Subcarpeta `partials/`
  - [ ] Subcarpeta `modules/`

- [ ] **Vistas movidas correctamente**
  - [ ] Órdenes en `focus-qr/modules/orders/`
  - [ ] QR en `focus-qr/modules/qr-codes/`
  - [ ] Dispositivos en `focus-qr/modules/devices/`
  - [ ] Entregas en `focus-qr/modules/deliveries/`

- [ ] **Cabeceras CETAM agregadas**
  - [ ] En todos los Controllers
  - [ ] En todos los Services
  - [ ] En todos los Repositories
  - [ ] En todos los Modelos
  - [ ] En todas las vistas Blade

- [ ] **Rutas actualizadas**
  - [ ] Prefijo `p/focus-qr` agregado
  - [ ] Nombres de ruta con `focus-qr.`
  - [ ] Use statements actualizados

- [ ] **Referencias actualizadas**
  - [ ] `return view()` con rutas correctas
  - [ ] `route()` en vistas con prefijo correcto
  - [ ] `redirect()->route()` actualizado

- [ ] **Caché limpiado**
  - [ ] `php artisan route:clear`
  - [ ] `php artisan view:clear`
  - [ ] `php artisan config:clear`
  - [ ] `php artisan cache:clear`

- [ ] **Pruebas funcionales**
  - [ ] Listar órdenes funciona
  - [ ] Crear orden funciona
  - [ ] Ver QR funciona
  - [ ] Marcar orden como lista funciona
  - [ ] Escaneo de entrega funciona
  - [ ] Notificaciones funcionan
  - [ ] API para Flutter funciona

---

## 🚀 PASOS FINALES

### 1. Limpiar Caché

```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

### 2. Probar Rutas

```bash
php artisan route:list | grep focus-qr
```

Deberías ver todas tus rutas con el prefijo `focus-qr.`

### 3. Probar Aplicación

```bash
php artisan serve
```

Visita: `http://localhost:8000/p/focus-qr/orders`

### 4. Commit de Cambios

```bash
git add .
git commit -m "Reestructuración según estándar CETAM - Focus QR System"
git push origin restructure-folders
```

### 5. Crear Pull Request

Crea un PR de la rama `restructure-folders` hacia `main` y revisa todos los cambios antes de hacer merge.

---

## ⚠️ PROBLEMAS COMUNES Y SOLUCIONES

### Problema 1: "Class not found"

**Error:**
```
Class 'App\Http\Controllers\OrderController' not found
```

**Solución:**
```bash
# Limpiar autoload de Composer
composer dump-autoload

# Verificar namespace en el archivo
# Debe ser: namespace App\Http\Controllers\Orders;
```

### Problema 2: "View not found"

**Error:**
```
View [orders.index] not found
```

**Solución:**
```php
// Actualizar en el controller:
return view('focus-qr.modules.orders.index', $data);

// Limpiar caché de vistas
php artisan view:clear
```

### Problema 3: "Route not found"

**Error:**
```
Route [orders.index] not defined
```

**Solución:**
```blade
{{-- Actualizar en vistas: --}}
<a href="{{ route('focus-qr.orders.index') }}">

{{-- Limpiar caché de rutas --}}
php artisan route:clear
```

### Problema 4: "Target class does not exist"

**Error:**
```
Target class [App\Http\Controllers\QrController] does not exist
```

**Solución:**
```php
// Actualizar use en routes/web.php:
use App\Http\Controllers\QrCodes\QrCodeController;

// NO:
use App\Http\Controllers\QrController;
```

---

## 📞 SOPORTE

Si encuentras problemas durante la reestructuración:

1. Revisa el checklist de verificación
2. Consulta la sección de problemas comunes
3. Verifica que todos los namespaces estén correctos
4. Asegúrate de haber limpiado todos los cachés

---

## ✅ CONCLUSIÓN

Una vez completada esta reestructuración, tu proyecto **Focus QR System** cumplirá con el estándar de estructura de carpetas establecido por CETAM, manteniendo toda la funcionalidad existente.

**Recuerda:**
- ✅ Haz backup antes de empezar
- ✅ Trabaja en una rama separada
- ✅ Prueba cada módulo después de moverlo
- ✅ Limpia cachés después de cada cambio importante
- ✅ Documenta cualquier problema que encuentres

**¡Éxito en la reestructuración!** 🚀
