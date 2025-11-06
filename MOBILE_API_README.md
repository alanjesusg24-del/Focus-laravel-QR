# API Móvil - Order QR System

## Configuración Completada ✓

Se ha implementado exitosamente el backend para la aplicación móvil de Order QR System.

## Base URL

```
http://[TU_IP]:8000/api/v1
```

## Endpoints Disponibles

### 1. Registrar Dispositivo
**POST** `/mobile/register`

No requiere autenticación.

**Body:**
```json
{
  "device_id": "unique-device-uuid",
  "fcm_token": "firebase-token-optional",
  "device_type": "android",
  "device_model": "Samsung Galaxy S21",
  "os_version": "Android 13",
  "app_version": "1.0.0"
}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Device registered successfully",
  "data": {
    "id": 1,
    "device_id": "unique-device-uuid",
    "device_type": "android",
    ...
  }
}
```

### 2. Asociar Orden con QR
**POST** `/mobile/orders/associate`

Requiere header: `X-Device-ID: your-device-id`

**Body:**
```json
{
  "qr_token": "8hNhwLErGSjuv2bOBaaYQT1D7vnNKvIM"
}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Order associated successfully",
  "data": {
    "order": {
      "order_id": 2,
      "order_number": "ORD-2025-001",
      "customer_name": "Juan Pérez",
      "status": "pending",
      "total_amount": "85.00",
      "items": [...],
      "statusHistory": [...]
    }
  }
}
```

### 3. Obtener Órdenes del Dispositivo
**GET** `/mobile/orders?status=pending&page=1&per_page=20`

Requiere header: `X-Device-ID: your-device-id`

**Parámetros opcionales:**
- `status`: pending, ready, delivered, cancelled
- `page`: número de página
- `per_page`: órdenes por página (default: 20)

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "orders": [...],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 100,
      "per_page": 20
    }
  }
}
```

### 4. Detalle de una Orden
**GET** `/mobile/orders/{orderId}`

Requiere header: `X-Device-ID: your-device-id`

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "order": {
      "order_id": 2,
      "order_number": "ORD-2025-001",
      "customer_name": "Juan Pérez",
      "customer_phone": "+52 555 1234567",
      "status": "pending",
      "total_amount": "85.00",
      "description": "Café americano grande + croissant",
      "items": [
        {
          "id": 1,
          "item_name": "Producto de ejemplo",
          "quantity": 2,
          "unit_price": "42.50",
          "total_price": "85.00"
        }
      ],
      "statusHistory": [...]
    }
  }
}
```

### 5. Actualizar Token FCM
**PUT** `/mobile/update-token`

Requiere header: `X-Device-ID: your-device-id`

**Body:**
```json
{
  "fcm_token": "new-firebase-token"
}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "FCM token updated successfully"
}
```

## QR Tokens de Prueba

Para probar la app, usa estos tokens QR:

**Orden 1:**
- Token: `8hNhwLErGSjuv2bOBaaYQT1D7vnNKvIM`
- Cliente: Juan Pérez
- Estado: pending

**Orden 2:**
- Token: `vVfqJlD0c39OVm6bUGoCYasZKcQulkZt`
- Cliente: María García
- Estado: ready

## Ejemplo de Uso Completo

### 1. Registrar dispositivo
```bash
curl -X POST http://localhost:8000/api/v1/mobile/register \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": "test-device-123",
    "device_type": "android",
    "device_model": "Samsung Galaxy S21",
    "os_version": "Android 13",
    "app_version": "1.0.0"
  }'
```

### 2. Asociar orden escaneando QR
```bash
curl -X POST http://localhost:8000/api/v1/mobile/orders/associate \
  -H "Content-Type: application/json" \
  -H "X-Device-ID: test-device-123" \
  -d '{
    "qr_token": "8hNhwLErGSjuv2bOBaaYQT1D7vnNKvIM"
  }'
```

### 3. Obtener mis órdenes
```bash
curl -X GET "http://localhost:8000/api/v1/mobile/orders?status=pending" \
  -H "X-Device-ID: test-device-123"
```

### 4. Ver detalle de orden
```bash
curl -X GET http://localhost:8000/api/v1/mobile/orders/2 \
  -H "X-Device-ID: test-device-123"
```

## Estructura de la Base de Datos

### Tablas Creadas:
1. **mobile_users** - Dispositivos móviles registrados
2. **orders** - Órdenes del sistema (actualizada con campos móviles)
3. **order_items** - Items de cada orden
4. **order_status_history** - Historial de cambios de estado

## Archivos Importantes

- **Controller**: `app/Http/Controllers/Api/V1/MobileController.php`
- **Models**:
  - `app/Models/MobileUser.php`
  - `app/Models/Order.php`
  - `app/Models/OrderItem.php`
  - `app/Models/OrderStatusHistory.php`
- **Middleware**: `app/Http/Middleware/MobileDeviceMiddleware.php`
- **Routes**: `routes/api.php`
- **Migraciones**: `database/migrations/`
- **Seeder**: `database/seeders/MobileAppSeeder.php`

## Comandos Útiles

### Obtener tokens QR disponibles:
```bash
php get_qr_tokens.php
```

### Ejecutar seeders de prueba:
```bash
php artisan db:seed --class=MobileAppSeeder
```

### Ver rutas API:
```bash
php artisan route:list --path=api/v1/mobile
```

### Iniciar servidor:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Estados de Órdenes

- **pending**: Orden creada, esperando confirmación
- **ready**: Orden lista para recoger
- **delivered**: Orden entregada
- **cancelled**: Orden cancelada

## Formato de Respuestas

### Éxito:
```json
{
  "success": true,
  "message": "Mensaje descriptivo",
  "data": { ... }
}
```

### Error:
```json
{
  "success": false,
  "message": "Descripción del error"
}
```

## Códigos HTTP

- **200**: Operación exitosa
- **401**: No autorizado (falta X-Device-ID)
- **404**: Recurso no encontrado
- **409**: Conflicto (ej: orden ya asociada a otro dispositivo)

## Seguridad

- CORS está habilitado para todas las peticiones API
- El middleware `mobile.device` valida el header `X-Device-ID`
- Los tokens QR son únicos y aleatorios (32 caracteres)
- Se actualiza automáticamente `last_seen_at` en cada petición

## Próximos Pasos (Opcional)

1. Implementar notificaciones push con Firebase
2. Agregar Observer para cambios de estado
3. Implementar rate limiting
4. Agregar autenticación con Sanctum (si se requiere)

---

**Versión**: 1.0.0
**Fecha**: 2025-11-06
**Compatible con**: Order QR Mobile App v1.0.0
