# 📱 Sistema de Escaneo QR para Entrega de Órdenes

## 🎯 Funcionalidad Implementada

El sistema ahora detecta automáticamente cuando se escanea el código QR del cliente desde cualquier pantalla del dashboard y marca la orden como entregada.

## 🔧 Cómo Funciona

### 1. **Listener Global JavaScript**
- Se ejecuta en todas las páginas del dashboard
- Detecta entrada rápida de teclado (característica de los escáneres QR)
- Ignora texto escrito en inputs/textareas normales
- Procesa automáticamente el token escaneado

### 2. **Validación Backend**
- Endpoint: `POST /api/v1/scanner/validate-delivery`
- Valida el token de pickup (`pickup_token`)
- Verifica que la orden esté en estado `ready`
- Marca la orden como `delivered`
- Envía notificación push al cliente

### 3. **Feedback Visual**
- Notificación de éxito: "Orden {FOLIO} entregada exitosamente"
- Notificación de error: Muestra el motivo del error
- Sonido de éxito/error
- Recarga automática si estás en la vista de órdenes

## 📋 Instrucciones de Prueba

### Opción 1: Con Escáner QR Físico

1. **Preparar la orden:**
   - Crear una orden desde el dashboard
   - Marcarla como "Lista para recoger"
   - El cliente debe escanear el QR y asociar la orden a su celular

2. **Escanear el QR del cliente:**
   - Estar en cualquier pantalla del dashboard (no importa cuál)
   - El cliente muestra el QR en su celular
   - Escanear el QR con el escáner USB/Bluetooth
   - El sistema automáticamente detectará el escaneo y marcará la orden como entregada

3. **Verificar:**
   - Aparecerá una notificación de éxito
   - La orden cambiará a estado "delivered"
   - El cliente recibirá una notificación push

### Opción 2: Prueba Manual (Sin escáner)

1. **Obtener el pickup_token:**
   - Ir a la base de datos
   - Seleccionar una orden en estado `ready`
   - Copiar el valor de `pickup_token`

2. **Simular escaneo:**
   - Abrir cualquier página del dashboard
   - Hacer clic en un área vacía (no en un input)
   - Pegar el token
   - Presionar Enter

3. **Usar curl/Postman:**
```bash
curl -X POST http://192.168.1.66:8000/api/v1/scanner/validate-delivery \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"pickup_token":"EL_TOKEN_AQUI"}'
```

## 🔍 Formato del Pickup Token

El QR del cliente contiene el `pickup_token` que se genera cuando:
1. La orden es marcada como "lista" (`ready`)
2. El token tiene el formato: `letras-numeros-guiones` (ej: `abc123-def456-ghi789`)

## ✅ Validaciones del Sistema

El sistema valida:
- ✓ Token válido y existente
- ✓ Orden en estado `ready` (lista para recoger)
- ✓ Formato correcto del token (alfanumérico con guiones)
- ✓ Longitud mínima del token (10 caracteres)

## ⚠️ Casos de Error

| Error | Causa | Solución |
|-------|-------|----------|
| "Token de pickup inválido" | El token no existe en la BD | Verificar que el QR sea correcto |
| "La orden no está lista para ser entregada" | Orden no está en estado `ready` | Marcar la orden como lista primero |
| No detecta el escaneo | Cursor en un input | Hacer clic fuera de inputs |

## 🎨 Características

- ✓ Detección automática sin botones
- ✓ Funciona en cualquier pantalla del dashboard
- ✓ No interfiere con inputs normales
- ✓ Notificaciones visuales y sonoras
- ✓ Recarga automática de la página de órdenes
- ✓ Notificación push al cliente
- ✓ Registro en historial de estados

## 🔐 Seguridad

- Token único por orden
- Validación de estado antes de marcar como entregada
- No requiere autenticación adicional (el token es la prueba)
- CSRF protection incluida

## 📱 Flujo Completo

```
1. Negocio crea orden → QR generado con qr_token
2. Cliente escanea QR → Orden asociada a su dispositivo
3. Negocio marca orden como "lista" → pickup_token generado + notificación al cliente
4. Cliente llega a recoger → Muestra QR en su celular
5. Negocio escanea QR del cliente → Sistema detecta pickup_token
6. Orden marcada como entregada → Notificación al cliente
```

## 🛠️ Archivos Modificados

- `resources/views/layouts/base.blade.php` - Listener global de JavaScript
- `app/Http/Controllers/Api/V1/MobileController.php` - Método `validateDelivery()`
- `app/Services/PushNotificationService.php` - Método `sendOrderDelivered()`
- `routes/api.php` - Ruta `/api/v1/scanner/validate-delivery`

## 📝 Notas Técnicas

- **Timeout de escaneo:** 100ms entre caracteres
- **Longitud mínima:** 10 caracteres
- **Detección:** KeyPress event listener
- **Notificaciones:** Librería Notyf (ya incluida en Volt Dashboard)
- **Sonidos:** Base64 encoded WAV files (compatibilidad universal)

## 🚀 Próximos Pasos

- [ ] Probar con escáner físico
- [ ] Ajustar timeout si es necesario según el escáner
- [ ] Considerar agregar logs de auditoría para escaneos
