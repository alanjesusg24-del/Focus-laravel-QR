# Corrección: Validación de Chat en Backend

## Problema Actual

Al intentar acceder al chat de una orden, el backend responde:
```
Exception: Orden no encontrada o no pertenece a este dispositivo
```

## Causa Raíz

Los endpoints de chat están validando que la orden pertenezca al `device_id`, pero el sistema de autenticación ha migrado a **autenticación por usuario** (email/password o Google Sign-In) con tokens Bearer.

## Endpoints Afectados

1. **GET** `/mobile/orders/{orderId}/messages` - Obtener mensajes
2. **POST** `/mobile/orders/{orderId}/messages` - Enviar mensaje
3. **PUT** `/mobile/orders/{orderId}/messages/mark-read` - Marcar como leído

## Solución Requerida

### Cambiar la validación de acceso

**ANTES** (validación por device_id):
```php
// Verificar que la orden pertenece al dispositivo
$order = Order::where('id', $orderId)
    ->where('device_id', $request->header('X-Device-ID'))
    ->firstOrFail();
```

**DESPUÉS** (validación por usuario autenticado):
```php
// Obtener el usuario autenticado del token Bearer
$user = auth()->user(); // o Auth::user()

// Verificar que la orden pertenece al usuario autenticado
$order = Order::where('id', $orderId)
    ->where('mobile_user_id', $user->id)
    ->firstOrFail();
```

### Middleware de Autenticación

Asegurarse de que estos endpoints usen el middleware de autenticación:

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/mobile/orders/{orderId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/mobile/orders/{orderId}/messages', [ChatController::class, 'sendMessage']);
    Route::put('/mobile/orders/{orderId}/messages/mark-read', [ChatController::class, 'markAsRead']);
});
```

## Contexto del Sistema

### Flujo de Autenticación Actual

1. Usuario se registra/inicia sesión con email/password o Google
2. Backend genera token Sanctum
3. Flutter guarda el token en SecureStorage
4. Todas las peticiones incluyen: `Authorization: Bearer {token}`
5. Backend valida el token y obtiene el usuario autenticado

### Estructura de Tablas

```
mobile_users
├── id
├── email
├── name
├── google_id (nullable)
└── ...

orders
├── id
├── mobile_user_id (FK a mobile_users.id)
├── business_id
├── device_id (DEPRECATED - ya no se usa para validación)
└── ...

chat_messages
├── id
├── order_id (FK a orders.id)
├── sender_type (mobile|business)
├── message
├── read_at
└── ...
```

## Headers que Flutter Envía

```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json
Accept: application/json
X-Device-ID: 938ae020-e6b3-4e13-8f67-b7cc875ef370
```

**Nota**: El `X-Device-ID` sigue enviándose para compatibilidad, pero **NO debe usarse** para validación de seguridad. Usar `auth()->user()` en su lugar.

## Verificación de la Corrección

Después de implementar los cambios, verificar:

1. Usuario puede acceder solo a chats de sus propias órdenes
2. Usuario NO puede acceder a chats de órdenes de otros usuarios
3. Token Bearer se valida correctamente
4. Mensajes se marcan como leídos correctamente

## Prioridad

🔴 **ALTA** - Bloquea funcionalidad de chat en la aplicación móvil

## Archivos Backend a Modificar

1. `app/Http/Controllers/Mobile/ChatController.php` (o similar)
2. `routes/api.php` - Verificar middleware de autenticación
3. Cualquier middleware personalizado de validación de dispositivo

## Ejemplo Completo del Controller

```php
<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Obtener mensajes de una orden
     */
    public function getMessages(Request $request, $orderId)
    {
        $user = Auth::user();

        // Validar que la orden pertenece al usuario autenticado
        $order = Order::where('id', $orderId)
            ->where('mobile_user_id', $user->id)
            ->firstOrFail();

        $messages = ChatMessage::where('order_id', $orderId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages,
            ],
        ]);
    }

    /**
     * Enviar un mensaje
     */
    public function sendMessage(Request $request, $orderId)
    {
        $user = Auth::user();

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Validar que la orden pertenece al usuario autenticado
        $order = Order::where('id', $orderId)
            ->where('mobile_user_id', $user->id)
            ->firstOrFail();

        $message = ChatMessage::create([
            'order_id' => $orderId,
            'sender_type' => 'mobile',
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'data' => $message,
            'message' => 'Mensaje enviado',
        ]);
    }

    /**
     * Marcar mensajes como leídos
     */
    public function markAsRead(Request $request, $orderId)
    {
        $user = Auth::user();

        // Validar que la orden pertenece al usuario autenticado
        $order = Order::where('id', $orderId)
            ->where('mobile_user_id', $user->id)
            ->firstOrFail();

        $markedCount = ChatMessage::where('order_id', $orderId)
            ->where('sender_type', 'business')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => [
                'messages_marked' => $markedCount,
            ],
        ]);
    }
}
```

## Resumen

- ❌ **NO usar**: `device_id` para validación de acceso
- ✅ **SÍ usar**: `auth()->user()` y validar `mobile_user_id`
- ✅ **Aplicar**: middleware `auth:sanctum` en rutas de chat
- ✅ **Validar**: Que la orden pertenezca al usuario autenticado antes de cualquier operación
