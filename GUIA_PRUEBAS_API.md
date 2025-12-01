# 🧪 Guía de Pruebas de API - Sistema de Órdenes Móvil

## 📋 Configuración Inicial

### Variables de Entorno
```bash
# Ngrok URL (cambiar por la URL actual)
NGROK_URL="https://gerald-ironical-contradictorily.ngrok-free.dev"

# Headers comunes
HEADERS=(
  -H "Content-Type: application/json"
  -H "Accept: application/json"
  -H "ngrok-skip-browser-warning: true"
)
```

---

## 🔐 Test 1: Registro de Usuario

### Request
```bash
curl -X POST "${NGROK_URL}/api/v1/auth/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true" \
  -d '{
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "device_id": "test-device-001",
    "device_type": "android",
    "device_model": "Pixel 7",
    "os_version": "Android 14",
    "app_version": "1.0.0"
  }'
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 13,
      "email": "test@example.com",
      "device_id": "test-device-001",
      "is_active": 1
    },
    "token": "50|xxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

### ✅ Verificación
- El email es único
- Se devuelve un token
- El usuario está activo

---

## 🔑 Test 2: Login de Usuario

### Request
```bash
curl -X POST "${NGROK_URL}/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true" \
  -d '{
    "email": "prueba2@outlook.com",
    "password": "123456",
    "device_id": "test-device-002"
  }'
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 12,
      "email": "prueba2@outlook.com",
      "device_id": "938ae020-e6b3-4e13-8f67-b7cc875ef370",
      "is_active": 1
    },
    "token": "49|V1jNlgfihYsgbEC9s0Dz5w7udNLhq3wkyfkz0nXZ142572e0"
  }
}
```

### ❌ Credenciales Incorrectas (401)
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### ✅ Verificación
- Guardar el token para las siguientes pruebas
- El usuario existe en la base de datos

---

## 📦 Test 3: Asociar Orden con QR

### Request
```bash
# Reemplazar {TOKEN} con el token obtenido del login
TOKEN="49|V1jNlgfihYsgbEC9s0Dz5w7udNLhq3wkyfkz0nXZ142572e0"

curl -X POST "${NGROK_URL}/api/v1/mobile/orders/associate" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "X-Device-ID: test-device-002" \
  -H "ngrok-skip-browser-warning: true" \
  -d '{
    "qr_token": "UR9woI7oI2WlcI8KfhkUIaOFqLyNVUCr"
  }'
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "message": "Order associated successfully",
  "data": {
    "order_id": 128,
    "order_number": "ORD-2025-0117",
    "status": "pending",
    "associated_at": "2025-11-27T20:47:31.000000Z",
    "items": [],
    "statusHistory": []
  }
}
```

### ❌ QR Inválido (404)
```json
{
  "success": false,
  "message": "Código QR no válido"
}
```

### ❌ Orden Ya Asociada (409)
```json
{
  "success": false,
  "message": "Esta orden ya está asociada a un usuario"
}
```

### ❌ Sin Autenticación (401)
```json
{
  "success": false,
  "message": "Se requiere autenticación"
}
```

### ✅ Verificación
- La orden se asocia al usuario autenticado
- No se puede asociar dos veces
- Solo usuarios autenticados pueden asociar

---

## 📋 Test 4: Listar Órdenes del Usuario

### Request
```bash
TOKEN="49|V1jNlgfihYsgbEC9s0Dz5w7udNLhq3wkyfkz0nXZ142572e0"

curl -X GET "${NGROK_URL}/api/v1/mobile/orders" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"
```

### Con Filtros
```bash
# Por estado
curl -X GET "${NGROK_URL}/api/v1/mobile/orders?status=pending" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"

# Con paginación
curl -X GET "${NGROK_URL}/api/v1/mobile/orders?page=1&per_page=10" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "data": {
    "orders": [
      {
        "order_id": 128,
        "order_number": "ORD-2025-0117",
        "status": "pending",
        "created_at": "2025-11-25T10:30:00.000000Z",
        "items": [
          {
            "id": 1,
            "name": "Producto 1",
            "quantity": 2,
            "price": "150.00"
          }
        ]
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 1,
      "total_items": 1,
      "per_page": 20
    }
  }
}
```

### ✅ Verificación
- Solo se muestran las órdenes del usuario autenticado
- La paginación funciona correctamente
- Los filtros aplican correctamente

---

## 📄 Test 5: Detalle de Orden

### Request
```bash
TOKEN="49|V1jNlgfihYsgbEC9s0Dz5w7udNLhq3wkyfkz0nXZ142572e0"
ORDER_ID="128"

curl -X GET "${NGROK_URL}/api/v1/mobile/orders/${ORDER_ID}" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "data": {
    "order_id": 128,
    "order_number": "ORD-2025-0117",
    "status": "pending",
    "qr_token": "UR9woI7oI2WlcI8KfhkUIaOFqLyNVUCr",
    "pickup_token": "abc123xyz",
    "created_at": "2025-11-25T10:30:00.000000Z",
    "associated_at": "2025-11-27T20:47:31.000000Z",
    "items": [
      {
        "id": 1,
        "name": "Producto 1",
        "quantity": 2,
        "price": "150.00",
        "subtotal": "300.00"
      }
    ],
    "statusHistory": [
      {
        "old_status": null,
        "new_status": "pending",
        "changed_at": "2025-11-25T10:30:00.000000Z"
      }
    ]
  }
}
```

### ❌ Orden No Encontrada (404)
```json
{
  "success": false,
  "message": "Order not found"
}
```

### ✅ Verificación
- Solo el propietario puede ver el detalle
- Se incluyen items y historial de estados

---

## 🔔 Test 6: Actualizar Token FCM

### Request
```bash
TOKEN="49|V1jNlgfihYsgbEC9s0Dz5w7udNLhq3wkyfkz0nXZ142572e0"

curl -X PUT "${NGROK_URL}/api/v1/mobile/update-token" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true" \
  -d '{
    "fcm_token": "fcm_token_example_xxxxxxxxxxxxx"
  }'
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "message": "FCM token updated successfully"
}
```

### ✅ Verificación
- El token se guarda en la base de datos
- Se puede usar para enviar notificaciones

---

## 🏢 Test 7: Listar Negocios

### Request
```bash
curl -X GET "${NGROK_URL}/api/v1/businesses" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"
```

### Con Paginación
```bash
curl -X GET "${NGROK_URL}/api/v1/businesses?page=1&per_page=10" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "message": "Businesses retrieved successfully",
  "data": {
    "businesses": [
      {
        "id": 1,
        "name": "Cafetería CETAM",
        "description": "Café y snacks",
        "address": "Campus CETAM",
        "is_active": 1
      }
    ],
    "pagination": {
      "total": 4,
      "count": 4,
      "per_page": 50,
      "current_page": 1,
      "total_pages": 1
    }
  }
}
```

### ✅ Verificación
- No requiere autenticación
- Devuelve todos los negocios activos

---

## 📍 Test 8: Negocios Cercanos

### Request
```bash
curl -X GET "${NGROK_URL}/api/v1/businesses/nearby?latitude=20.6597&longitude=-103.3496&radius=5000" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "message": "Nearby businesses found",
  "data": {
    "businesses": [
      {
        "id": 1,
        "name": "Cafetería CETAM",
        "latitude": 20.6597,
        "longitude": -103.3496,
        "distance_km": 0.5
      }
    ],
    "search_params": {
      "latitude": 20.6597,
      "longitude": -103.3496,
      "radius_km": 5
    }
  }
}
```

### ✅ Verificación
- Devuelve negocios dentro del radio especificado
- Incluye la distancia calculada

---

## 📱 Test 9: Scanner QR - Validar Entrega

### Request (Desde Dashboard)
```bash
curl -X POST "${NGROK_URL}/api/v1/scanner/validate-delivery" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true" \
  -d '{
    "pickup_token": "abc123xyz"
  }'
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "message": "Orden entregada exitosamente",
  "data": {
    "order_id": 128,
    "folio_number": "F-001",
    "status": "delivered",
    "delivered_at": "2025-11-27T21:30:00.000000Z"
  }
}
```

### ❌ Token Inválido (404)
```json
{
  "success": false,
  "message": "Token inválido o no encontrado"
}
```

### ❌ Orden No Lista (400)
```json
{
  "success": false,
  "message": "La orden no está lista para ser entregada. Estado actual: pending",
  "current_status": "pending"
}
```

### ✅ Verificación
- Solo funciona si la orden está en estado "ready"
- Cambia el estado a "delivered"
- Envía notificación al cliente

---

## 👤 Test 10: Obtener Usuario Autenticado

### Request
```bash
TOKEN="49|V1jNlgfihYsgbEC9s0Dz5w7udNLhq3wkyfkz0nXZ142572e0"

curl -X GET "${NGROK_URL}/api/v1/auth/user" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "data": {
    "id": 12,
    "email": "prueba2@outlook.com",
    "device_id": "938ae020-e6b3-4e13-8f67-b7cc875ef370",
    "device_type": "android",
    "is_active": 1,
    "created_at": "2025-11-20T11:17:40.000000Z"
  }
}
```

### ❌ Sin Autenticación (401)
```json
{
  "message": "Unauthenticated."
}
```

---

## 🚪 Test 11: Logout

### Request
```bash
TOKEN="49|V1jNlgfihYsgbEC9s0Dz5w7udNLhq3wkyfkz0nXZ142572e0"

curl -X POST "${NGROK_URL}/api/v1/auth/logout" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"
```

### Respuesta Esperada (200)
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### ✅ Verificación
- El token se invalida
- Ya no se puede usar para peticiones

---

## 🧪 Checklist de Pruebas

### Funcionalidades Básicas
- [ ] Registro de usuario nuevo
- [ ] Login con credenciales válidas
- [ ] Login con credenciales inválidas (debe fallar)
- [ ] Obtener perfil de usuario autenticado
- [ ] Logout de usuario

### Gestión de Órdenes
- [ ] Asociar orden con QR válido
- [ ] Intentar asociar orden con QR inválido (debe fallar)
- [ ] Intentar asociar orden ya asociada (debe fallar)
- [ ] Listar órdenes del usuario
- [ ] Ver detalle de orden propia
- [ ] Intentar ver orden de otro usuario (debe fallar)

### Negocios
- [ ] Listar todos los negocios
- [ ] Buscar negocios cercanos con geolocalización
- [ ] Paginación de negocios

### Notificaciones
- [ ] Actualizar token FCM
- [ ] Recibir notificación al asociar orden
- [ ] Recibir notificación al cambiar estado

### Scanner
- [ ] Validar entrega con pickup_token
- [ ] Validar que solo funciona si orden está "ready"

---

## 📊 Códigos de Estado HTTP

### Éxito
- `200 OK` - Petición exitosa
- `201 Created` - Recurso creado exitosamente

### Errores del Cliente
- `400 Bad Request` - Datos inválidos
- `401 Unauthorized` - No autenticado
- `404 Not Found` - Recurso no encontrado
- `409 Conflict` - Conflicto (ej: orden ya asociada)
- `422 Unprocessable Entity` - Validación fallida

### Errores del Servidor
- `500 Internal Server Error` - Error del servidor

---

## 🔍 Debugging

### Ver Logs en Tiempo Real
```bash
tail -f storage/logs/laravel.log
```

### Limpiar Logs
```bash
echo "" > storage/logs/laravel.log
```

### Ver Últimas 50 Líneas
```bash
tail -50 storage/logs/laravel.log
```

---

**Versión**: 1.0.0
**Fecha**: 28 de Noviembre 2025
**Generado por**: Claude Code
