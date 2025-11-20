# Solución: Órdenes Duplicadas Entre Usuarios

## Problema Identificado

Actualmente, cuando inicias sesión con diferentes usuarios (correo electrónico o Google), **ves las mismas órdenes en ambas cuentas** aunque no hayas asociado nada.

### Causa Raíz

El sistema tiene dos métodos de identificación que están en conflicto:

1. **Sistema antiguo (sin autenticación)**: Usa `device_id` para identificar al dispositivo
2. **Sistema nuevo (con autenticación)**: Usa `user_id` para identificar al usuario

El problema es que el endpoint `/api/v1/mobile/orders` está filtrando por `device_id` en lugar de por `user_id` cuando hay un usuario autenticado.

## Solución Implementada en Flutter

### Cambios realizados en el código Flutter:

#### 1. ApiService (`lib/services/api_service.dart`)

Se agregó soporte para tokens de autenticación:

```dart
static String? _authToken;

/// Configurar token de autenticación
static void setAuthToken(String? token) {
  _authToken = token;
  if (token != null) {
    _dio.options.headers['Authorization'] = 'Bearer $token';
  } else {
    _dio.options.headers.remove('Authorization');
  }
}

/// Verificar si el usuario está autenticado
static bool get isAuthenticated => _authToken != null;
```

#### 2. AuthProvider (`lib/providers/auth_provider.dart`)

Se configuró el token en `ApiService` cuando el usuario inicia sesión:

```dart
if (response.success) {
  // Configurar token en ApiService
  if (response.token != null) {
    ApiService.setAuthToken(response.token);
  }

  _user = response.user;
  _status = AuthStatus.authenticated;
  return true;
}
```

Y se limpia cuando cierra sesión:

```dart
Future<void> logout() async {
  await _authService.logout();
  _user = null;
  _status = AuthStatus.unauthenticated;

  // Limpiar token de ApiService
  ApiService.setAuthToken(null);
}
```

## Solución Requerida en el Backend Laravel

### IMPORTANTE: Debes actualizar el endpoint de órdenes en tu backend

El endpoint `/api/v1/mobile/orders` debe filtrar las órdenes de manera diferente dependiendo de si el usuario está autenticado o no.

### Código a actualizar en Laravel:

#### Archivo: `app/Http/Controllers/MobileController.php` (o similar)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class MobileController extends Controller
{
    /**
     * Obtener órdenes del usuario/dispositivo
     */
    public function getOrders(Request $request)
    {
        try {
            $user = $request->user('sanctum'); // Usuario autenticado (si existe)
            $deviceId = $request->header('X-Device-ID'); // Device ID del header

            // IMPORTANTE: Priorizar usuario autenticado sobre device_id
            if ($user) {
                // Usuario autenticado: filtrar por user_id
                $orders = Order::where('user_id', $user->id)
                    ->with(['business', 'items']) // Cargar relaciones necesarias
                    ->orderBy('created_at', 'desc')
                    ->get();

                \Log::info('Órdenes obtenidas para usuario autenticado', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'orders_count' => $orders->count()
                ]);
            } else if ($deviceId) {
                // Sin autenticación: filtrar por device_id (sistema antiguo)
                $orders = Order::where('device_id', $deviceId)
                    ->with(['business', 'items'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                \Log::info('Órdenes obtenidas para dispositivo sin auth', [
                    'device_id' => $deviceId,
                    'orders_count' => $orders->count()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requiere autenticación o device_id',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'total' => $orders->count(),
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener órdenes: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener órdenes',
            ], 500);
        }
    }

    /**
     * Asociar orden con usuario/dispositivo
     */
    public function associateOrder(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        try {
            $user = $request->user('sanctum');
            $deviceId = $request->header('X-Device-ID');
            $qrToken = $request->qr_token;

            // Buscar la orden por QR token
            $order = Order::where('qr_token', $qrToken)
                ->whereNull('user_id') // Solo órdenes no asociadas
                ->whereNull('device_id')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden no encontrada o ya fue asociada',
                ], 404);
            }

            // Asociar según el método de autenticación
            if ($user) {
                // Usuario autenticado: asociar a user_id
                $order->user_id = $user->id;
                $order->device_id = $deviceId; // Guardar también device_id como backup
                $order->save();

                \Log::info('Orden asociada a usuario autenticado', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            } else if ($deviceId) {
                // Sin autenticación: asociar solo a device_id
                $order->device_id = $deviceId;
                $order->save();

                \Log::info('Orden asociada a dispositivo sin auth', [
                    'order_id' => $order->id,
                    'device_id' => $deviceId,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requiere autenticación o device_id',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Orden asociada exitosamente',
                'data' => $order->load(['business', 'items']),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al asociar orden: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al asociar orden',
            ], 500);
        }
    }

    /**
     * Obtener detalle de una orden
     */
    public function getOrderDetail(Request $request, $orderId)
    {
        try {
            $user = $request->user('sanctum');
            $deviceId = $request->header('X-Device-ID');

            // Buscar la orden con verificación de propiedad
            $query = Order::where('id', $orderId);

            if ($user) {
                // Usuario autenticado: verificar que sea del usuario
                $query->where('user_id', $user->id);
            } else if ($deviceId) {
                // Sin autenticación: verificar que sea del dispositivo
                $query->where('device_id', $deviceId);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requiere autenticación o device_id',
                ], 401);
            }

            $order = $query->with(['business', 'items', 'statusHistory'])->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden no encontrada',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $order,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener detalle de orden: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener orden',
            ], 500);
        }
    }
}
```

### Actualizar la tabla de órdenes

Si tu tabla `orders` no tiene el campo `user_id`, créalo:

```php
// database/migrations/xxxx_add_user_id_to_orders_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Índices para mejorar performance
            $table->index('user_id');
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
```

Ejecutar la migración:
```bash
php artisan migrate
```

### Actualizar las rutas en `routes/api.php`

Asegúrate de que las rutas de órdenes acepten tanto autenticación como device_id:

```php
Route::prefix('v1')->group(function () {
    Route::prefix('mobile')->group(function () {
        // Rutas que aceptan autenticación opcional
        Route::middleware(['auth:sanctum,optional'])->group(function () {
            Route::get('/orders', [MobileController::class, 'getOrders']);
            Route::get('/orders/{id}', [MobileController::class, 'getOrderDetail']);
            Route::post('/orders/associate', [MobileController::class, 'associateOrder']);
        });

        // Otras rutas sin autenticación requerida
        Route::post('/register', [MobileController::class, 'register']);
        Route::post('/update-token', [MobileController::class, 'updateToken']);
    });
});
```

### Crear middleware opcional de Sanctum

Si no existe, crea un middleware que permita autenticación opcional:

```php
// app/Http/Middleware/OptionalSanctumAuth.php

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class OptionalSanctumAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Intentar autenticar pero no fallar si no hay token
        if ($request->bearerToken()) {
            $request->setUserResolver(function () use ($request) {
                return \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            });
        }

        return $next($request);
    }
}
```

Registrar el middleware en `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ...
    'auth:sanctum,optional' => \App\Http\Middleware\OptionalSanctumAuth::class,
];
```

## Verificación

### 1. Probar con usuario autenticado:

```bash
curl -X GET http://localhost:8000/api/v1/mobile/orders \
  -H "Authorization: Bearer {token}" \
  -H "X-Device-ID: test-device-123"
```

Debe retornar solo las órdenes del usuario autenticado.

### 2. Probar sin autenticación:

```bash
curl -X GET http://localhost:8000/api/v1/mobile/orders \
  -H "X-Device-ID: test-device-123"
```

Debe retornar solo las órdenes del dispositivo.

### 3. Probar desde la app:

1. Inicia sesión con usuario A
2. Asocia una orden
3. Cierra sesión
4. Inicia sesión con usuario B
5. **Verifica que NO veas las órdenes del usuario A**

## Resumen

### Cambios en Flutter ✅
- [x] Agregado soporte para tokens en ApiService
- [x] Configurado token automáticamente al login
- [x] Limpiado token al logout

### Cambios en Laravel ⚠️ PENDIENTE
- [ ] Actualizar `getOrders()` para filtrar por user_id si está autenticado
- [ ] Actualizar `associateOrder()` para guardar user_id
- [ ] Actualizar `getOrderDetail()` para verificar propiedad
- [ ] Agregar campo `user_id` a tabla orders
- [ ] Crear middleware de autenticación opcional
- [ ] Actualizar rutas

Una vez implementados estos cambios en el backend, cada usuario verá solo sus propias órdenes.
