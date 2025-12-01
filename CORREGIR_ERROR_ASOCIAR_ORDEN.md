# Corrección - Error al Asociar Orden (500 Internal Server Error)

## 🔴 Problema Actual

Al escanear un código QR para asociar una orden, se produce un error 500 en el servidor Laravel.

### Error en Ngrok:
```
POST /api/v1/mobile/orders/associate 500 Internal Server Error
```

### Error en Flutter:
```
Exception: SQLSTATE[23000]...
```

---

## 🔍 Diagnóstico

### ¿Qué es SQLSTATE[23000]?

Es un error de **violación de integridad de base de datos**. Causas comunes:

1. **Intento de insertar un valor NULL en una columna NOT NULL**
2. **Violación de UNIQUE constraint** (valor duplicado)
3. **Violación de FOREIGN KEY constraint** (referencia inválida)
4. **Campo requerido faltante**

### Causa Probable

El endpoint `/mobile/orders/associate` probablemente está intentando:

1. Asociar una orden a un `mobile_user_id`
2. Pero el campo `mobile_user_id` podría estar **NULL** o no existir
3. O la tabla espera un campo que ya no existe (como `name`)

---

## 🛠️ Soluciones a Implementar en Laravel

### **SOLUCIÓN 1: Verificar el Controlador de Asociación**

**Archivo:** `app/Http/Controllers/Api/V1/Mobile/OrderController.php` (o similar)

#### Código INCORRECTO (probablemente actual):

```php
public function associate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'qr_code' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    // ❌ PROBLEMA: Buscar por device_id pero puede ser null
    $mobileUser = MobileUser::where('device_id', $request->device_id)->first();

    if (!$mobileUser) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario móvil no encontrado',
        ], 404);
    }

    // Resto del código...
}
```

#### Código CORRECTO:

```php
public function associate(Request $request)
{
    \Log::info('=== ASOCIAR ORDEN ===');
    \Log::info('Request data:', $request->all());

    $validator = Validator::make($request->all(), [
        'qr_code' => 'required|string',
    ]);

    if ($validator->fails()) {
        \Log::error('Validación falló:', $validator->errors()->toArray());

        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    // ✅ CORRECCIÓN: Obtener usuario autenticado desde el token
    $user = $request->user(); // Sanctum authentication

    if (!$user) {
        \Log::error('Usuario no autenticado');

        return response()->json([
            'success' => false,
            'message' => 'No autenticado',
        ], 401);
    }

    \Log::info('Usuario autenticado:', ['id' => $user->id, 'email' => $user->email]);

    try {
        // Buscar la orden por QR code
        $order = Order::where('qr_code', $request->qr_code)->first();

        if (!$order) {
            \Log::warning('Orden no encontrada:', ['qr_code' => $request->qr_code]);

            return response()->json([
                'success' => false,
                'message' => 'Código QR no válido',
            ], 404);
        }

        // Verificar si la orden ya está asociada
        if ($order->mobile_user_id) {
            \Log::warning('Orden ya asociada:', [
                'order_id' => $order->id,
                'mobile_user_id' => $order->mobile_user_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Esta orden ya está asociada a un usuario',
            ], 409);
        }

        // ✅ Asociar orden al usuario autenticado
        $order->mobile_user_id = $user->id;
        $order->save();

        \Log::info('Orden asociada exitosamente:', [
            'order_id' => $order->id,
            'mobile_user_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Orden asociada exitosamente',
            'order' => [
                'id' => $order->id,
                'qr_code' => $order->qr_code,
                'status' => $order->status,
                // Agregar más campos según sea necesario
            ],
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error al asociar orden:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al asociar la orden',
            'error' => $e->getMessage(), // Solo en desarrollo
        ], 500);
    }
}
```

---

### **SOLUCIÓN 2: Verificar la Ruta (debe usar auth:sanctum)**

**Archivo:** `routes/api.php`

```php
Route::prefix('v1')->group(function () {

    // ✅ CORRECTO: Ruta protegida con Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('mobile/orders/associate', [OrderController::class, 'associate']);
        Route::get('mobile/orders', [OrderController::class, 'index']);
        Route::get('mobile/orders/{id}', [OrderController::class, 'show']);
    });

});
```

---

### **SOLUCIÓN 3: Verificar la Tabla Orders**

Asegúrate de que la tabla `orders` tenga la columna correcta:

```bash
php artisan tinker

# Verificar estructura
>>> Schema::getColumnListing('orders')

# Debería incluir:
# - id
# - qr_code
# - mobile_user_id (puede ser NULL)
# - status
# - created_at, updated_at
```

Si `mobile_user_id` no existe o tiene un nombre diferente, crea una migración:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Si la columna no existe, agregarla
            if (!Schema::hasColumn('orders', 'mobile_user_id')) {
                $table->unsignedBigInteger('mobile_user_id')->nullable()->after('id');
                $table->foreign('mobile_user_id')
                      ->references('id')
                      ->on('mobile_users')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['mobile_user_id']);
            $table->dropColumn('mobile_user_id');
        });
    }
};
```

Ejecutar:
```bash
php artisan migrate
```

---

### **SOLUCIÓN 4: Verificar el Request desde Flutter**

El request desde Flutter debe incluir el token de autenticación:

**Verificar en `lib/services/api_service.dart`:**

```dart
Future<Map<String, dynamic>> associateOrder(String qrCode) async {
  final token = await _storage.getToken();

  if (token == null) {
    throw Exception('No hay sesión activa');
  }

  final response = await _dio.post(
    ApiConfig.associateOrder,
    data: {'qr_code': qrCode},
    options: Options(
      headers: {
        'Authorization': 'Bearer $token', // ✅ Importante
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ),
  );

  return response.data;
}
```

---

### **SOLUCIÓN 5: Logs de Debugging**

Agrega logs temporales para ver exactamente qué está pasando:

```php
public function associate(Request $request)
{
    // Ver todo lo que llega
    \Log::info('=== INICIO ASOCIAR ORDEN ===');
    \Log::info('Headers:', $request->headers->all());
    \Log::info('Request body:', $request->all());
    \Log::info('User autenticado:', [$request->user()]);

    // Tu código...

    try {
        // ...
    } catch (\Exception $e) {
        \Log::error('ERROR COMPLETO:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        throw $e; // Re-lanzar para ver en respuesta
    }
}
```

Ver logs:
```bash
tail -f storage/logs/laravel.log
```

---

## 🧪 Testing con cURL

### Probar el endpoint manualmente:

```bash
# 1. Primero hacer login para obtener token
curl -X POST http://tu-ngrok-url/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "ngrok-skip-browser-warning: true" \
  -d '{
    "email": "test@test.com",
    "password": "123456"
  }'

# Copiar el token de la respuesta

# 2. Intentar asociar orden
curl -X POST http://tu-ngrok-url/api/v1/mobile/orders/associate \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TU_TOKEN_AQUI" \
  -H "ngrok-skip-browser-warning: true" \
  -d '{
    "qr_code": "ABC123"
  }'
```

---

## 🔍 Verificaciones en Base de Datos

```bash
php artisan tinker

# 1. Ver estructura de orders
>>> \DB::select('DESCRIBE orders');

# 2. Ver una orden de ejemplo
>>> App\Models\Order::first()

# 3. Verificar si mobile_user_id acepta NULL
>>> Schema::getColumnType('orders', 'mobile_user_id')

# 4. Intentar asociar manualmente
>>> $order = App\Models\Order::where('qr_code', 'ABC123')->first();
>>> $order->mobile_user_id = 1;
>>> $order->save(); // ¿Da error?
```

---

## 📋 Checklist de Verificación

- [ ] 1. La ruta `/mobile/orders/associate` usa `auth:sanctum` middleware
- [ ] 2. El controlador obtiene el usuario con `$request->user()`
- [ ] 3. La tabla `orders` tiene la columna `mobile_user_id`
- [ ] 4. La columna `mobile_user_id` es NULLABLE
- [ ] 5. Flutter envía el token en el header `Authorization`
- [ ] 6. El modelo `Order` tiene `fillable` o no usa `$guarded`
- [ ] 7. Los logs muestran el usuario autenticado
- [ ] 8. No hay foreign key constraint roto

---

## 🎯 Respuesta Esperada (Exitosa)

```json
{
  "success": true,
  "message": "Orden asociada exitosamente",
  "order": {
    "id": 123,
    "qr_code": "ABC123",
    "status": "pendiente"
  }
}
```

---

## 🚨 Errores Comunes y Soluciones

### Error: "Column 'mobile_user_id' cannot be null"

**Causa:** La columna no acepta NULL pero no se está enviando un valor.

**Solución:**
```sql
ALTER TABLE orders MODIFY mobile_user_id BIGINT UNSIGNED NULL;
```

### Error: "Field 'name' doesn't have a default value"

**Causa:** El modelo `Order` o alguna tabla relacionada espera un campo `name`.

**Solución:** Hacer el campo nullable o proporcionar un valor por defecto.

### Error: "SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row"

**Causa:** Foreign key constraint - el `mobile_user_id` no existe en la tabla `mobile_users`.

**Solución:** Verificar que el usuario existe:
```php
$user = MobileUser::find($userId);
if (!$user) {
    throw new Exception('Usuario no existe');
}
```

---

## 📝 Código Completo del Controlador (CORRECTO)

```php
<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Asociar una orden a un usuario móvil
     */
    public function associate(Request $request)
    {
        \Log::info('=== ASOCIAR ORDEN ===', $request->all());

        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Obtener usuario autenticado
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado',
            ], 401);
        }

        try {
            // Buscar orden
            $order = Order::where('qr_code', $request->qr_code)->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código QR no válido',
                ], 404);
            }

            // Verificar si ya está asociada
            if ($order->mobile_user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta orden ya está asociada a un usuario',
                ], 409);
            }

            // Asociar
            $order->mobile_user_id = $user->id;
            $order->save();

            \Log::info('Orden asociada exitosamente:', [
                'order_id' => $order->id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Orden asociada exitosamente',
                'order' => $order,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error al asociar orden:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al asociar la orden',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar órdenes del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::where('mobile_user_id', $user->id)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ], 200);
    }

    /**
     * Ver detalle de una orden
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

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
    }
}
```

---

**Versión:** 1.0.0
**Fecha:** 2025-11-27
**Propósito:** Corregir error 500 al asociar órdenes después de cambios en autenticación
