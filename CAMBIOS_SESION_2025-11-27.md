# 📋 Cambios de Sesión - 27 de Noviembre 2025

## 🎯 Resumen de Cambios

Esta sesión se enfocó en implementar y corregir el sistema de autenticación con Email/Password para la app móvil, resolver errores en endpoints de órdenes y mejorar el sistema de notificaciones push.

---

## 📦 CAMBIO 1: Implementación de Autenticación Email/Password

### **Contexto**
Se implementó un sistema completo de autenticación con email y contraseña para usuarios móviles, separado del sistema de autenticación web.

### **Archivos Modificados**

#### 1.1. Migración: Agregar campos de autenticación a `mobile_users`

**Archivo**: `database/migrations/2025_11_27_140452_add_auth_fields_to_mobile_users_table.php`

**Acción**: Crear archivo nuevo con este contenido:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            // Agregar campos de autenticación
            $table->string('email')->unique()->nullable()->after('id');
            $table->string('password')->nullable()->after('email');
            $table->timestamp('email_verified_at')->nullable()->after('password');

            // Modificar device_id para que no sea único (puede ser null si se registra primero sin dispositivo)
            $table->dropUnique(['device_id']);
            $table->string('device_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            // Eliminar campos agregados
            $table->dropColumn(['email', 'password', 'email_verified_at']);

            // Restaurar device_id como único y requerido
            $table->string('device_id')->unique()->nullable(false)->change();
        });
    }
};
```

**Comando a ejecutar**:
```bash
php artisan migrate
```

---

#### 1.2. Migración: Hacer campos de dispositivo opcionales

**Archivo**: `database/migrations/2025_11_27_140732_make_device_fields_nullable_in_mobile_users_table.php`

**Acción**: Crear archivo nuevo con este contenido:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            // Hacer que los campos de dispositivo sean opcionales
            $table->string('device_type')->nullable()->change();
            $table->string('device_model')->nullable()->change();
            $table->string('os_version')->nullable()->change();
            $table->string('app_version')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            // Restaurar campos como requeridos
            $table->string('device_type')->nullable(false)->change();
            $table->string('device_model')->nullable()->change();
            $table->string('os_version')->nullable()->change();
            $table->string('app_version')->nullable()->change();
        });
    }
};
```

**Comando a ejecutar**:
```bash
php artisan migrate
```

---

#### 1.3. Modelo: `app/Models/MobileUser.php`

**Acción**: REEMPLAZAR TODO EL CONTENIDO del archivo con:

```php
<?php

/**
 * ============================================
 * CETAM - Mobile User Model
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        MobileUser.php
 * @description Modelo de usuarios móviles y dispositivos
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     2.0.0
 * @copyright   CETAM © 2025
 *
 * @table       mobile_users
 * @primaryKey  id
 *
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MobileUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'mobile_users';

    protected $fillable = [
        'email',
        'password',
        'device_id',
        'fcm_token',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relación con órdenes
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'mobile_user_id');
    }

    /**
     * Verificar si el email está verificado
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Marcar email como verificado
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }
}
```

**Cambios clave**:
- ✅ Agregado `fcm_token` a `$fillable`
- ✅ Cambiado de `Model` a `Authenticatable`
- ✅ Agregados traits: `HasApiTokens`, `Notifiable`, `SoftDeletes`
- ✅ Cast de `password` a `hashed`

---

#### 1.4. Controlador: `app/Http/Controllers/Api/V1/Auth/AuthController.php`

**Acción**: Crear archivo nuevo con este contenido:

```php
<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario
     */
    public function register(Request $request)
    {
        \Log::info('=== INICIO REGISTRO ===');
        \Log::info('Email recibido: ' . $request->email);
        \Log::info('Device ID recibido: ' . ($request->device_id ?? 'NULL'));

        // Validación - device_id es OPCIONAL
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:mobile_users,email',
            'password' => 'required|string|min:6',
            'device_id' => 'nullable|string', // OPCIONAL - puede ser null
        ]);

        if ($validator->fails()) {
            \Log::error('Validación falló:', $validator->errors()->toArray());

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // NO HAY VALIDACIÓN DE DEVICE_ID ÚNICO
        // Un mismo dispositivo puede tener múltiples cuentas

        // Crear usuario
        try {
            $user = MobileUser::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'device_id' => $request->device_id, // Puede ser null
                'email_verified_at' => now(),
            ]);

            \Log::info('Usuario creado exitosamente: ' . $user->email);

            // Generar token
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'device_id' => $user->device_id,
                    'email_verified' => $user->hasVerifiedEmail(),
                ],
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        \Log::info('=== INICIO LOGIN ===');
        \Log::info('Email recibido: ' . $request->email);
        \Log::info('Device ID recibido: ' . ($request->device_id ?? 'NULL'));

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_id' => 'nullable|string', // OPCIONAL
        ]);

        if ($validator->fails()) {
            \Log::error('Validación de login falló:', $validator->errors()->toArray());

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Buscar usuario por email
        $user = MobileUser::where('email', $request->email)->first();

        // Verificar credenciales
        if (!$user || !Hash::check($request->password, $user->password)) {
            \Log::warning('Credenciales incorrectas para: ' . $request->email);

            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        // NO HAY VALIDACIÓN DE DEVICE_ID
        // El usuario puede loguearse desde cualquier dispositivo

        // Actualizar device_id si se proporciona
        if ($request->device_id) {
            $user->device_id = $request->device_id;
            $user->save();
        }

        // Revocar tokens anteriores (opcional)
        $user->tokens()->delete();

        // Generar nuevo token
        $token = $user->createToken('mobile-app')->plainTextToken;

        \Log::info('Login exitoso para: ' . $user->email);

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'device_id' => $user->device_id,
                'email_verified' => $user->hasVerifiedEmail(),
            ],
        ], 200);
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'device_id' => $user->device_id,
                'email_verified' => $user->hasVerifiedEmail(),
            ],
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        // Revocar token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente',
        ], 200);
    }

    /**
     * Solicitar cambio de dispositivo
     */
    public function requestDeviceChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'new_device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verificar credenciales
        $user = MobileUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        // Actualizar device_id
        $user->device_id = $request->new_device_id;
        $user->save();

        // Revocar todos los tokens anteriores
        $user->tokens()->delete();

        // Generar nuevo token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Dispositivo cambiado exitosamente',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'device_id' => $user->device_id,
                'email_verified' => $user->hasVerifiedEmail(),
            ],
        ], 200);
    }

    /**
     * Verificar cambio de dispositivo
     */
    public function verifyDeviceChange(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Función no implementada - usar requestDeviceChange',
        ], 501);
    }
}
```

---

#### 1.5. Rutas: `routes/api.php`

**Acción**: AGREGAR al inicio del archivo (después de los `use` statements):

```php
use App\Http\Controllers\Api\V1\Auth\AuthController;
```

**Acción**: REEMPLAZAR el grupo de rutas `Route::prefix('auth')` dentro de `Route::prefix('v1')` con:

```php
Route::prefix('auth')->group(function () {
    // Public routes (no authentication required)
    // Usando AuthController para tabla mobile_users con email/password
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Login con Google sigue usando MobileAuthController (tabla users)
    Route::post('/login/google', [MobileAuthController::class, 'loginWithGoogle']);
    Route::post('/password/forgot', [MobileAuthController::class, 'forgotPassword']);
    Route::post('/password/reset', [MobileAuthController::class, 'resetPassword']);

    // Device change routes (Email/Password auth)
    Route::post('/device/change-request', [AuthController::class, 'requestDeviceChange']);
    Route::post('/device/verify-change', [AuthController::class, 'verifyDeviceChange']);

    // DEBUG ROUTE - ELIMINAR EN PRODUCCIÓN
    Route::get('/debug-users', function () {
        return response()->json([
            'tabla_existe' => Schema::hasTable('mobile_users'),
            'total_usuarios' => App\Models\MobileUser::count(),
            'usuarios_con_email' => App\Models\MobileUser::whereNotNull('email')->where('email', '!=', '')->count(),
            'usuarios_sin_email' => App\Models\MobileUser::whereNull('email')->orWhere('email', '')->count(),
            'emails_registrados' => App\Models\MobileUser::whereNotNull('email')->where('email', '!=', '')->pluck('email'),
            'devices_registrados' => App\Models\MobileUser::pluck('device_id'),
        ]);
    });

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Usando AuthController para tabla mobile_users
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Estos endpoints siguen con MobileAuthController
        Route::post('/verify-email', [MobileAuthController::class, 'verifyEmail']);
        Route::post('/resend-verification', [MobileAuthController::class, 'resendVerification']);
        Route::post('/password/change', [MobileAuthController::class, 'changePassword']);
    });
});
```

---

## 📦 CAMBIO 2: Corrección de Endpoint de Asociar Orden

### **Contexto**
El endpoint `/mobile/orders/associate` tenía un error 500 porque buscaba en la columna `qr_code` que no existe.

### **Archivos Modificados**

#### 2.1. Controlador: `app/Http/Controllers/Api/V1/MobileController.php`

**Acción**: BUSCAR el método `associateOrder` y REEMPLAZARLO completamente con:

```php
/**
 * Asociar una orden con el dispositivo mediante QR
 * POST /api/v1/mobile/orders/associate
 */
public function associateOrder(Request $request)
{
    \Log::info('=== INICIO ASOCIAR ORDEN ===');
    \Log::info('Request data:', $request->all());
    \Log::info('Headers:', $request->headers->all());

    // Validar - aceptar tanto qr_token como qr_code (Flutter puede enviar cualquiera)
    $validated = $request->validate([
        'qr_token' => 'required_without:qr_code|string',
        'qr_code' => 'required_without:qr_token|string',
    ]);

    // Flutter puede enviar "qr_code" pero la tabla solo tiene "qr_token"
    $qrValue = $validated['qr_token'] ?? $validated['qr_code'] ?? null;

    if (!$qrValue) {
        return response()->json([
            'success' => false,
            'message' => 'Se requiere qr_token o qr_code',
        ], 422);
    }

    \Log::info('QR Value:', ['qr' => $qrValue]);

    // Obtener usuario autenticado (MobileUser de la nueva tabla)
    $user = $request->user('sanctum');
    \Log::info('Usuario autenticado:', ['user' => $user ? $user->id : 'NULL', 'email' => $user ? $user->email : 'NULL']);

    // También obtener mobile_user del middleware (si existe)
    $mobileUser = $request->mobile_user ?? null;
    \Log::info('Mobile User del middleware:', ['mobile_user' => $mobileUser ? $mobileUser->id : 'NULL']);

    try {
        // Buscar orden SOLO por qr_token (la tabla NO tiene columna qr_code)
        $order = Order::where('qr_token', $qrValue)->first();

        if (!$order) {
            \Log::warning('Orden no encontrada con QR:', ['qr' => $qrValue]);

            return response()->json([
                'success' => false,
                'message' => 'Código QR no válido',
            ], 404);
        }

        \Log::info('Orden encontrada:', [
            'order_id' => $order->id,
            'order_number' => $order->order_number ?? $order->order_id,
            'current_user_id' => $order->user_id,
            'current_mobile_user_id' => $order->mobile_user_id,
        ]);

        // Verificar si ya está asociada
        if ($order->user_id || $order->mobile_user_id) {
            \Log::warning('Orden ya asociada:', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'mobile_user_id' => $order->mobile_user_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Esta orden ya está asociada a un usuario',
            ], 409);
        }

        // Asociar según el método de autenticación
        if ($user) {
            // Usuario autenticado con el nuevo sistema (tabla mobile_users)
            $order->mobile_user_id = $user->id; // Usar mobile_user_id
            $order->associated_at = now();
            $order->save();

            \Log::info('Orden asociada a usuario autenticado (nueva tabla):', [
                'order_id' => $order->id,
                'mobile_user_id' => $user->id,
                'email' => $user->email,
            ]);

            // Intentar enviar notificación si tiene FCM token
            if ($user->fcm_token) {
                try {
                    PushNotificationService::sendOrderAssociated($user->fcm_token, $order);
                } catch (\Exception $e) {
                    \Log::error('Error enviando notificación:', ['error' => $e->getMessage()]);
                }
            }
        } else if ($mobileUser) {
            // Sin autenticación: asociar a mobile_user del middleware (sistema antiguo)
            $order->mobile_user_id = $mobileUser->id;
            $order->associated_at = now();
            $order->save();

            \Log::info('Orden asociada a dispositivo sin auth:', [
                'order_id' => $order->id,
                'mobile_user_id' => $mobileUser->id,
            ]);

            // Enviar notificación
            if ($mobileUser->fcm_token) {
                try {
                    PushNotificationService::sendOrderAssociated($mobileUser->fcm_token, $order);
                } catch (\Exception $e) {
                    \Log::error('Error enviando notificación:', ['error' => $e->getMessage()]);
                }
            }
        } else {
            \Log::error('No hay usuario autenticado ni mobile_user del middleware');

            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación',
            ], 401);
        }

        // Cargar relaciones
        $order->load('items', 'statusHistory');

        // Asegurar que statusHistory nunca sea null
        if (!$order->statusHistory) {
            $order->setRelation('statusHistory', collect([]));
        }

        \Log::info('Orden asociada exitosamente');

        return response()->json([
            'success' => true,
            'message' => 'Order associated successfully',
            'data' => $order,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error al asociar orden:', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al asociar la orden',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

**Cambios clave**:
- ✅ Removida búsqueda en columna `qr_code` (no existe)
- ✅ Busca solo en `qr_token`
- ✅ Acepta tanto `qr_code` como `qr_token` en el request pero busca en `qr_token`
- ✅ Logs extensivos para debugging

---

## 📦 CAMBIO 3: Corrección de Endpoints de Órdenes

### **Contexto**
Los endpoints `getOrders()` y `getOrderDetail()` buscaban por `user_id` en lugar de `mobile_user_id`, causando que las órdenes desaparecieran.

### **Archivos Modificados**

#### 3.1. Controlador: `app/Http/Controllers/Api/V1/MobileController.php`

**Acción**: BUSCAR el método `getOrders` y REEMPLAZARLO completamente con:

```php
/**
 * Obtener órdenes del dispositivo
 * GET /api/v1/mobile/orders?status=pending&page=1&per_page=20
 */
public function getOrders(Request $request)
{
    \Log::info('=== LISTAR ÓRDENES ===');

    $user = $request->user('sanctum'); // Usuario autenticado (si existe)
    $mobileUser = $request->mobile_user ?? null; // Dispositivo del middleware

    // IMPORTANTE: Priorizar usuario autenticado sobre mobile_user_id
    if ($user) {
        // Usuario autenticado: filtrar por mobile_user_id (CORREGIDO)
        $query = Order::where('mobile_user_id', $user->id);
        \Log::info('Fetching orders for authenticated user', [
            'mobile_user_id' => $user->id,
            'email' => $user->email
        ]);
    } else if ($mobileUser) {
        // Sin autenticación: filtrar por mobile_user_id (sistema antiguo)
        $query = Order::where('mobile_user_id', $mobileUser->id);
        \Log::info('Fetching orders for device', [
            'mobile_user_id' => $mobileUser->id
        ]);
    } else {
        \Log::error('No hay usuario autenticado ni mobile_user del middleware');

        return response()->json([
            'success' => false,
            'message' => 'Se requiere autenticación o device_id',
        ], 401);
    }

    try {
        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar por más recientes
        $query->orderBy('created_at', 'desc');

        // Paginación
        $perPage = $request->get('per_page', 20);
        $orders = $query->with('items')->paginate($perPage);

        \Log::info('Órdenes encontradas:', ['count' => $orders->count()]);

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'total_pages' => $orders->lastPage(),
                    'total_items' => $orders->total(),
                    'per_page' => $orders->perPage(),
                ],
            ],
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error al listar órdenes:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener órdenes',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

**Cambio clave**:
- ❌ ANTES: `Order::where('user_id', $user->id)`
- ✅ AHORA: `Order::where('mobile_user_id', $user->id)`

---

**Acción**: BUSCAR el método `getOrderDetail` y REEMPLAZARLO completamente con:

```php
/**
 * Obtener detalle de una orden
 * GET /api/v1/mobile/orders/{orderId}
 */
public function getOrderDetail(Request $request, $orderId)
{
    \Log::info('=== DETALLE DE ORDEN ===', ['order_id' => $orderId]);

    $user = $request->user('sanctum');
    $mobileUser = $request->mobile_user ?? null;

    if (!$user && !$mobileUser) {
        \Log::error('No hay usuario autenticado ni mobile_user');

        return response()->json([
            'success' => false,
            'message' => 'Se requiere autenticación o device_id',
        ], 401);
    }

    try {
        // Buscar la orden con verificación de propiedad
        $query = Order::where('order_id', $orderId);

        if ($user) {
            // Usuario autenticado: verificar que sea del usuario (CORREGIDO: mobile_user_id)
            $query->where('mobile_user_id', $user->id);
            \Log::info('Buscando orden del usuario autenticado', ['mobile_user_id' => $user->id]);
        } else if ($mobileUser) {
            // Sin autenticación: verificar que sea del dispositivo
            $query->where('mobile_user_id', $mobileUser->id);
            \Log::info('Buscando orden del dispositivo', ['mobile_user_id' => $mobileUser->id]);
        }

        $order = $query->with(['items', 'statusHistory'])->first();

        if (!$order) {
            \Log::warning('Orden no encontrada', ['order_id' => $orderId]);

            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        // Asegurar que statusHistory nunca sea null
        if (!$order->statusHistory) {
            $order->setRelation('statusHistory', collect([]));
        }

        \Log::info('Orden encontrada exitosamente');

        return response()->json([
            'success' => true,
            'data' => $order,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error al obtener detalle de orden:', [
            'order_id' => $orderId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener la orden',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

**Cambio clave**:
- ❌ ANTES: `->where('user_id', $user->id)`
- ✅ AHORA: `->where('mobile_user_id', $user->id)`

---

**Acción**: BUSCAR el método `updateFcmToken` y REEMPLAZARLO completamente con:

```php
/**
 * Actualizar token FCM del dispositivo
 * PUT /api/v1/mobile/update-token
 */
public function updateFcmToken(Request $request)
{
    \Log::info('=== ACTUALIZAR FCM TOKEN ===');
    \Log::info('Request:', $request->all());

    $validated = $request->validate([
        'fcm_token' => 'required|string',
    ]);

    $user = $request->user('sanctum');
    $mobileUser = $request->mobile_user ?? null;

    try {
        if ($user) {
            // Usuario autenticado con Sanctum
            $user->fcm_token = $validated['fcm_token'];
            $user->save();

            \Log::info('FCM token actualizado para usuario autenticado:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => substr($validated['fcm_token'], 0, 20) . '...'
            ]);
        } else if ($mobileUser) {
            // Sin autenticación, usando middleware
            $mobileUser->update([
                'fcm_token' => $validated['fcm_token'],
            ]);

            \Log::info('FCM token actualizado para dispositivo:', [
                'mobile_user_id' => $mobileUser->id,
                'token' => substr($validated['fcm_token'], 0, 20) . '...'
            ]);
        } else {
            \Log::error('No hay usuario autenticado ni mobile_user');

            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully',
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error al actualizar FCM token:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar token',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

---

## 🚀 INSTRUCCIONES DE IMPLEMENTACIÓN

### Paso 1: Ejecutar Migraciones

```bash
# Ejecutar las dos nuevas migraciones
php artisan migrate

# Si da error, verificar que las columnas no existan ya:
php artisan tinker
>>> Schema::getColumnListing('mobile_users');
```

### Paso 2: Actualizar Archivos

1. **Modelo `MobileUser.php`**: Reemplazar todo el contenido
2. **Crear `AuthController.php`**: Crear archivo nuevo
3. **Actualizar `routes/api.php`**: Agregar `use` y reemplazar grupo de auth
4. **Actualizar `MobileController.php`**:
   - Reemplazar método `associateOrder()`
   - Reemplazar método `getOrders()`
   - Reemplazar método `getOrderDetail()`
   - Reemplazar método `updateFcmToken()`

### Paso 3: Limpiar Cachés

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Paso 4: Verificar que Funciona

```bash
# Probar endpoint de debug
curl http://localhost/api/v1/auth/debug-users

# Probar registro
curl -X POST http://localhost/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"123456"}'

# Debería devolver un token
```

---

## 📊 RESUMEN DE ENDPOINTS NUEVOS/MODIFICADOS

### Endpoints de Autenticación (NUEVOS)

```
POST   /api/v1/auth/register              - Registro con email/password
POST   /api/v1/auth/login                 - Login con email/password
GET    /api/v1/auth/me                    - Obtener usuario actual
POST   /api/v1/auth/logout                - Cerrar sesión
POST   /api/v1/auth/device/change-request - Cambiar dispositivo
GET    /api/v1/auth/debug-users           - Debug (ELIMINAR EN PROD)
```

### Endpoints de Órdenes (CORREGIDOS)

```
POST   /api/v1/mobile/orders/associate    - Asociar orden (CORREGIDO)
GET    /api/v1/mobile/orders               - Listar órdenes (CORREGIDO)
GET    /api/v1/mobile/orders/{id}          - Detalle orden (CORREGIDO)
PUT    /api/v1/mobile/update-token         - Actualizar FCM token (MEJORADO)
```

---

## ⚠️ NOTAS IMPORTANTES

### 1. Columna `qr_code` NO EXISTE
La tabla `orders` solo tiene `qr_token`, NO tiene `qr_code`. Flutter puede enviar cualquiera de los dos, pero Laravel solo busca en `qr_token`.

### 2. Campo `device_id` es OPCIONAL
Un usuario puede registrarse sin `device_id`. No hay validación de dispositivo único.

### 3. Búsqueda por `mobile_user_id`
TODOS los endpoints de órdenes deben buscar por `mobile_user_id`, NO por `user_id`.

### 4. FCM Token
El campo `fcm_token` debe estar en `$fillable` del modelo `MobileUser` para poder guardarse.

### 5. Logs Extensivos
Todos los métodos tienen logs. Revisar `storage/logs/laravel.log` para debugging.

---

## 🔍 VERIFICACIÓN POST-IMPLEMENTACIÓN

Después de implementar los cambios, verificar:

```bash
# 1. Ver estructura de mobile_users
php artisan tinker
>>> Schema::getColumnListing('mobile_users');
# Debe incluir: email, password, email_verified_at, fcm_token

# 2. Verificar que AuthController existe
ls app/Http/Controllers/Api/V1/Auth/AuthController.php

# 3. Probar registro
curl -X POST http://localhost/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"123456"}'

# 4. Ver logs
tail -f storage/logs/laravel.log
```

---

## 📝 CHANGELOG

**Versión**: 2.0.0
**Fecha**: 2025-11-27
**Autor**: CETAM Dev Team

### Agregado
- ✅ Sistema de autenticación Email/Password
- ✅ Campos de auth en tabla `mobile_users`
- ✅ `AuthController` completo con registro, login, logout, me
- ✅ Logs extensivos en todos los métodos
- ✅ Soporte para `fcm_token` en `MobileUser`

### Corregido
- ✅ Error 500 en `/mobile/orders/associate` (columna `qr_code`)
- ✅ Órdenes desaparecen (búsqueda por `user_id` en lugar de `mobile_user_id`)
- ✅ FCM token no se guardaba (`fcm_token` no estaba en `$fillable`)

### Cambiado
- ✅ `device_id` ahora es opcional y no único
- ✅ `MobileUser` ahora extiende `Authenticatable` en lugar de `Model`
- ✅ Todos los endpoints de órdenes buscan por `mobile_user_id`

---

**FIN DEL DOCUMENTO**
