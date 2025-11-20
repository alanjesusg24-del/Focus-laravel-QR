# Guía de Migración a Firebase Cloud Messaging API v1

## Resumen

Firebase ha deprecado la API heredada (Legacy) de Cloud Messaging. Este proyecto ha sido actualizado para usar la **FCM HTTP v1 API**.

## Cambios Realizados

### 1. SDK Instalado

```bash
composer require kreait/firebase-php
```

### 2. Archivo de Credenciales JSON

Ya **NO se usa** la variable `FCM_SERVER_KEY` (API Legacy deprecada desde junio 2024).

Ahora usamos un **archivo JSON de credenciales de servicio**.

## Configuración Paso a Paso

### Paso 1: Obtener el archivo de credenciales JSON

1. Ve a [Firebase Console](https://console.firebase.google.com/)
2. Selecciona tu proyecto
3. Click en ⚙️ → **Project Settings**
4. Ve a la pestaña **"Service Accounts"**
5. Click en el botón **"Generate new private key"** (azul)
6. Se descargará un archivo JSON (ejemplo: `your-project-firebase-adminsdk-xxxxx.json`)

### Paso 2: Guardar el archivo en Laravel

Guarda el archivo descargado en:

```
storage/firebase-credentials.json
```

**IMPORTANTE:** El archivo ya está ignorado en `.gitignore` para evitar subirlo a Git.

### Paso 3: Configurar la variable de entorno

En tu archivo `.env`, la variable `FIREBASE_CREDENTIALS_PATH` ya está configurada:

```env
FIREBASE_CREDENTIALS_PATH="${APP_BASE_PATH}/storage/firebase-credentials.json"
```

Si tu archivo tiene otro nombre o ubicación, actualiza esta variable.

### Paso 4: Verificar que funciona

Crea un script de prueba en `test_fcm_v1.php`:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

// Cargar archivo de credenciales
$credentialsPath = __DIR__ . '/storage/firebase-credentials.json';

if (!file_exists($credentialsPath)) {
    die("ERROR: Archivo de credenciales no encontrado en: {$credentialsPath}\n");
}

$factory = (new Factory)->withServiceAccount($credentialsPath);
$messaging = $factory->createMessaging();

// Token FCM de prueba (reemplazar con un token real de tu app)
$fcmToken = 'TOKEN_FCM_DE_TU_DISPOSITIVO';

// Crear notificación
$notification = Notification::create(
    '🎉 Prueba FCM v1',
    'Si ves esta notificación, ¡la migración fue exitosa!'
);

// Crear mensaje
$message = CloudMessage::withTarget('token', $fcmToken)
    ->withNotification($notification)
    ->withData([
        'type' => 'test',
        'timestamp' => time(),
    ]);

// Enviar
try {
    $result = $messaging->send($message);
    echo "✅ Notificación enviada exitosamente!\n";
    echo "Result: {$result}\n";
} catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
    echo "❌ Token FCM no válido o dispositivo no encontrado\n";
    echo "Error: " . $e->getMessage() . "\n";
} catch (\Kreait\Firebase\Exception\MessagingException $e) {
    echo "❌ Error de Firebase Messaging\n";
    echo "Error: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
```

Ejecutar:

```bash
php test_fcm_v1.php
```

### Paso 5: Probar desde Laravel

Puedes probar desde `tinker`:

```bash
php artisan tinker
```

```php
use App\Services\PushNotificationService;
use App\Models\Order;

$order = Order::first();
$mobileUser = $order->mobileUser;

if ($mobileUser && $mobileUser->fcm_token) {
    PushNotificationService::sendOrderStatusChange(
        $mobileUser->fcm_token,
        $order,
        'pending',
        'ready'
    );
}
```

## Diferencias entre API Legacy y API v1

| Aspecto | API Legacy (deprecada) | API v1 (actual) |
|---------|----------------------|----------------|
| **Autenticación** | Server Key simple | Service Account JSON con OAuth 2.0 |
| **Endpoint** | `https://fcm.googleapis.com/fcm/send` | `https://fcm.googleapis.com/v1/projects/{project_id}/messages:send` |
| **Headers** | `Authorization: key=AAAA...` | `Authorization: Bearer {access_token}` |
| **Seguridad** | Menos seguro (clave estática) | Más seguro (tokens que expiran) |
| **Estado** | ❌ Deprecada desde junio 2023 | ✅ Recomendada y soportada |

## Archivos Modificados

- `app/Services/PushNotificationService.php` - Actualizado para usar FCM v1 API
- `.env` - Nueva variable `FIREBASE_CREDENTIALS_PATH`
- `.gitignore` - Agregado `firebase-credentials.json`
- `composer.json` - Agregado `kreait/firebase-php`

## Troubleshooting

### Error: "Firebase credentials file not found"

**Causa:** El archivo JSON no está en la ubicación especificada

**Solución:**
1. Verifica que el archivo esté en `storage/firebase-credentials.json`
2. Verifica que la ruta en `.env` sea correcta
3. Si está en otra ubicación, actualiza `FIREBASE_CREDENTIALS_PATH`

### Error: "Insufficient Permission"

**Causa:** El Service Account no tiene permisos para enviar mensajes

**Solución:**
1. Ve a Firebase Console → Project Settings → Service Accounts
2. Verifica que el rol sea "Firebase Admin SDK Administrator Service Agent"
3. Genera una nueva clave si es necesario

### Error: "Invalid Token"

**Causa:** El token FCM del dispositivo es inválido o expiró

**Solución:**
1. Verifica que el token FCM del usuario esté actualizado
2. Pide al usuario que vuelva a loguear en la app móvil
3. Verifica que el token no tenga espacios o caracteres especiales

### La notificación no llega

**Checklist:**
- [ ] Archivo de credenciales JSON descargado y guardado correctamente
- [ ] Variable `FIREBASE_CREDENTIALS_PATH` configurada en `.env`
- [ ] Token FCM del usuario guardado en la base de datos
- [ ] Token FCM es válido y pertenece al proyecto correcto
- [ ] App móvil tiene permisos de notificaciones habilitados
- [ ] Dispositivo tiene conexión a internet

## Ver Logs

Para depurar problemas, revisa los logs de Laravel:

```bash
tail -f storage/logs/laravel.log
```

Busca mensajes como:
- `📤 Enviando notificación (FCM v1)`
- `✅ Notificación enviada exitosamente (FCM v1)`
- `❌ Error de Firebase Messaging`

## Referencias

- [Firebase Cloud Messaging HTTP v1 API](https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages)
- [Kreait Firebase PHP SDK](https://firebase-php.readthedocs.io/)
- [Migration Guide (Official)](https://firebase.google.com/docs/cloud-messaging/migrate-v1)

## Notas de Seguridad

1. **NUNCA** subas el archivo `firebase-credentials.json` a Git
2. **NUNCA** compartas el archivo JSON en chats o correos
3. Si el archivo se filtra, revócalo inmediatamente y genera uno nuevo
4. Usa variables de entorno en producción para la ruta del archivo

## Soporte

Si tienes problemas con la migración:

1. Revisa los logs de Laravel: `storage/logs/laravel.log`
2. Verifica que el archivo JSON sea válido (JSON bien formado)
3. Prueba con el script de prueba `test_fcm_v1.php`
4. Revisa la consola de Firebase para errores

---

**Migración completada:** ✅ Este proyecto ya usa FCM HTTP v1 API

**Fecha de migración:** 2025-11-06
