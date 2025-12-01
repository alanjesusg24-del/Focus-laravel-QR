# Corrección - Órdenes Desaparecen y Notificaciones No Llegan

## 🔴 Problemas Actuales

### Problema 1: Órdenes desaparecen al deslizar hacia arriba
- Al hacer pull-to-refresh (deslizar hacia arriba) en la app
- Las órdenes desaparecen de la lista
- Probablemente el endpoint devuelve lista vacía

### Problema 2: Notificaciones no llegan
- Cuando se marca una orden como "lista" en Laravel
- La notificación push no llega al celular
- Anteriormente funcionaba correctamente

---

## 🔍 Diagnóstico

### Problema 1: Causas Posibles

1. **El endpoint `/mobile/orders` no devuelve órdenes correctamente**
   - Busca por `device_id` en lugar de `mobile_user_id`
   - No usa autenticación Sanctum
   - Falta relación en el modelo

2. **El token no se envía en el request de Flutter**
   - Headers no incluyen `Authorization`
   - Token expirado o inválido

### Problema 2: Causas Posibles

1. **El FCM token del usuario no se guarda correctamente**
   - Campo `fcm_token` es NULL en `mobile_users`
   - No se actualiza después del login/registro

2. **Laravel no encuentra el usuario al enviar notificación**
   - Busca por `device_id` en lugar de `mobile_user_id`
   - El usuario no existe o está mal relacionado

3. **Configuración de Firebase incorrecta**
   - Credenciales desactualizadas
   - Server key inválido

---

## 🛠️ SOLUCIÓN 1: Corregir Endpoint de Órdenes

### **Archivo:** `app/Http/Controllers/Api/V1/Mobile/OrderController.php`

#### Método `index()` - CORRECTO:

```php
/**
 * Listar órdenes del usuario autenticado
 */
public function index(Request $request)
{
    \Log::info('=== LISTAR ÓRDENES ===');

    // ✅ Obtener usuario autenticado desde token
    $user = $request->user();

    if (!$user) {
        \Log::error('Usuario no autenticado en listar órdenes');

        return response()->json([
            'success' => false,
            'message' => 'No autenticado',
        ], 401);
    }

    \Log::info('Usuario autenticado:', [
        'id' => $user->id,
        'email' => $user->email
    ]);

    try {
        // ✅ Buscar órdenes por mobile_user_id (NO por device_id)
        $orders = Order::where('mobile_user_id', $user->id)
                       ->orderBy('created_at', 'desc')
                       ->get();

        \Log::info('Órdenes encontradas:', ['count' => $orders->count()]);

        return response()->json([
            'success' => true,
            'orders' => $orders,
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

#### Método `show()` - CORRECTO:

```php
/**
 * Ver detalle de una orden específica
 */
public function show(Request $request, $id)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'No autenticado',
        ], 401);
    }

    try {
        $order = Order::where('id', $id)
                      ->where('mobile_user_id', $user->id)
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Orden no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error al obtener orden:', [
            'order_id' => $id,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener la orden',
        ], 500);
    }
}
```

---

## 🛠️ SOLUCIÓN 2: Guardar y Actualizar FCM Token

### **Paso 1: Agregar columna `fcm_token` a `mobile_users`**

#### Migración:

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
            if (!Schema::hasColumn('mobile_users', 'fcm_token')) {
                $table->text('fcm_token')->nullable()->after('device_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }
};
```

Ejecutar:
```bash
php artisan migrate
```

### **Paso 2: Actualizar Modelo MobileUser**

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class MobileUser extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'email',
        'password',
        'device_id',
        'fcm_token', // ✅ Agregar
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

    // Relaciones
    public function orders()
    {
        return $this->hasMany(Order::class, 'mobile_user_id');
    }
}
```

### **Paso 3: Endpoint para actualizar FCM Token**

#### Agregar ruta en `routes/api.php`:

```php
Route::prefix('v1')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
        // Actualizar FCM token
        Route::post('mobile/update-token', [MobileController::class, 'updateFcmToken']);

        // Otras rutas...
        Route::post('mobile/orders/associate', [OrderController::class, 'associate']);
        Route::get('mobile/orders', [OrderController::class, 'index']);
        Route::get('mobile/orders/{id}', [OrderController::class, 'show']);
    });

});
```

#### Crear controlador o agregar método:

```php
<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MobileController extends Controller
{
    /**
     * Actualizar FCM token del usuario
     */
    public function updateFcmToken(Request $request)
    {
        \Log::info('=== ACTUALIZAR FCM TOKEN ===');
        \Log::info('Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado',
            ], 401);
        }

        try {
            $user->fcm_token = $request->fcm_token;
            $user->save();

            \Log::info('FCM token actualizado:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => substr($request->fcm_token, 0, 20) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token FCM actualizado exitosamente',
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error al actualizar FCM token:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar token',
            ], 500);
        }
    }
}
```

---

## 🛠️ SOLUCIÓN 3: Enviar Notificaciones Correctamente

### **Archivo:** Servicio de Notificaciones (ej: `app/Services/NotificationService.php`)

```php
<?php

namespace App\Services;

use App\Models\MobileUser;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class NotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Enviar notificación a un usuario móvil
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        \Log::info('=== ENVIAR NOTIFICACIÓN ===', [
            'user_id' => $userId,
            'title' => $title,
            'body' => $body
        ]);

        try {
            // ✅ Buscar usuario por ID (NO por device_id)
            $user = MobileUser::find($userId);

            if (!$user) {
                \Log::error('Usuario no encontrado:', ['user_id' => $userId]);
                return false;
            }

            if (!$user->fcm_token) {
                \Log::warning('Usuario sin FCM token:', [
                    'user_id' => $userId,
                    'email' => $user->email
                ]);
                return false;
            }

            \Log::info('Enviando a FCM token:', [
                'token' => substr($user->fcm_token, 0, 20) . '...'
            ]);

            $notification = FirebaseNotification::create($title, $body);

            $message = CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification($notification)
                ->withData($data);

            $result = $this->messaging->send($message);

            \Log::info('Notificación enviada exitosamente:', [
                'result' => $result
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Error al enviar notificación:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Enviar notificación cuando una orden está lista
     */
    public function notifyOrderReady($orderId)
    {
        try {
            $order = \App\Models\Order::find($orderId);

            if (!$order || !$order->mobile_user_id) {
                \Log::warning('Orden no tiene usuario asociado:', ['order_id' => $orderId]);
                return false;
            }

            return $this->sendToUser(
                $order->mobile_user_id,
                'Tu orden está lista',
                'Tu orden #' . $order->id . ' está lista para recoger',
                [
                    'type' => 'order_ready',
                    'order_id' => (string) $order->id,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]
            );

        } catch (\Exception $e) {
            \Log::error('Error en notifyOrderReady:', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
```

### **Uso en el Controlador de Órdenes:**

```php
use App\Services\NotificationService;

class OrderController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Actualizar estado de orden (desde panel admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        \Log::info('Estado de orden actualizado:', [
            'order_id' => $id,
            'old_status' => $oldStatus,
            'new_status' => $request->status
        ]);

        // ✅ Enviar notificación si el estado es "lista"
        if ($request->status === 'lista' || $request->status === 'ready') {
            $this->notificationService->notifyOrderReady($order->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado',
            'order' => $order
        ]);
    }
}
```

---

## 🛠️ SOLUCIÓN 4: Actualizar Flutter

### **Verificar que Flutter envíe el token al backend**

#### En `lib/services/notification_service.dart`:

```dart
import 'package:firebase_messaging/firebase_messaging.dart';
import 'api_service.dart';

class NotificationService {
  final FirebaseMessaging _messaging = FirebaseMessaging.instance;
  final ApiService _apiService = ApiService();

  /// Inicializar notificaciones
  Future<void> initialize() async {
    // Pedir permisos
    await _messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
    );

    // Obtener token FCM
    String? token = await _messaging.getToken();

    if (token != null) {
      print('[FCM] Token obtenido: ${token.substring(0, 20)}...');

      // ✅ IMPORTANTE: Enviar token al backend
      await _updateTokenInBackend(token);
    }

    // Escuchar cambios de token
    _messaging.onTokenRefresh.listen((newToken) {
      print('[FCM] Token actualizado: ${newToken.substring(0, 20)}...');
      _updateTokenInBackend(newToken);
    });

    // Manejar notificaciones
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);
    FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationClick);
  }

  /// Enviar token al backend
  Future<void> _updateTokenInBackend(String token) async {
    try {
      await _apiService.updateFcmToken(token);
      print('[FCM] Token enviado al backend exitosamente');
    } catch (e) {
      print('[FCM] Error al enviar token al backend: $e');
    }
  }

  // Resto de métodos...
}
```

#### En `lib/services/api_service.dart`:

```dart
/// Actualizar FCM token en el backend
Future<void> updateFcmToken(String fcmToken) async {
  final token = await _storage.getToken();

  if (token == null) {
    throw Exception('No hay sesión activa');
  }

  try {
    await _dio.post(
      ApiConfig.updateFcmToken,
      data: {'fcm_token': fcmToken},
      options: Options(
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      ),
    );

    print('[API] FCM token actualizado en backend');
  } catch (e) {
    print('[API] Error al actualizar FCM token: $e');
    rethrow;
  }
}
```

#### Llamar en `lib/providers/auth_provider.dart`:

```dart
/// Login con email y contraseña
Future<bool> login({
  required String email,
  required String password,
  String? deviceId,
}) async {
  // ... código de login existente ...

  if (response.success) {
    _user = response.user;
    _status = AuthStatus.authenticated;

    // ✅ Actualizar FCM token después de login exitoso
    try {
      final notificationService = NotificationService();
      await notificationService.initialize();
    } catch (e) {
      print('[WARN] Error al inicializar notificaciones: $e');
    }

    return true;
  }

  // ...
}
```

---

## 📋 Checklist de Verificación

### Backend Laravel

- [ ] Tabla `mobile_users` tiene columna `fcm_token`
- [ ] Endpoint `/mobile/orders` usa `$request->user()`
- [ ] Endpoint `/mobile/orders` busca por `mobile_user_id`
- [ ] Endpoint `/mobile/update-token` existe y funciona
- [ ] NotificationService busca usuario por `mobile_user_id`
- [ ] NotificationService verifica que `fcm_token` no sea NULL
- [ ] Al cambiar estado a "lista" se envía notificación
- [ ] Logs muestran: usuario encontrado, token FCM presente

### Flutter

- [ ] `NotificationService.initialize()` se llama después del login
- [ ] `updateFcmToken()` se llama con el token FCM
- [ ] Request incluye header `Authorization: Bearer TOKEN`
- [ ] `OrdersProvider.fetchOrders()` envía el token
- [ ] Pull-to-refresh llama a `fetchOrders()`

---

## 🧪 Testing

### 1. Verificar que el token FCM se guarda:

```bash
php artisan tinker

# Ver usuario y su token
>>> $user = App\Models\MobileUser::where('email', 'test@test.com')->first();
>>> $user->fcm_token;
// Debería mostrar un token largo, no NULL
```

### 2. Verificar que las órdenes se obtienen:

```bash
# Con cURL
curl -X GET http://tu-ngrok-url/api/v1/mobile/orders \
  -H "Authorization: Bearer TU_TOKEN_AQUI" \
  -H "ngrok-skip-browser-warning: true"

# Debería devolver las órdenes del usuario
```

### 3. Probar notificación manualmente:

```bash
php artisan tinker

>>> $service = new App\Services\NotificationService();
>>> $service->sendToUser(1, 'Test', 'Mensaje de prueba');
// Debería enviar notificación al celular
```

---

## 🔍 Ver Logs en Tiempo Real

```bash
# Terminal 1: Ver logs de Laravel
tail -f storage/logs/laravel.log

# Terminal 2: Probar endpoints
curl ...
```

---

**Versión:** 1.0.0
**Fecha:** 2025-11-27
**Propósito:** Corregir órdenes que desaparecen y notificaciones que no llegan
