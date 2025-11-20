# 📱 Instrucciones para Claude Code - Proyecto Flutter

## 🎯 Objetivo

Modificar la app móvil Flutter para usar una URL fija de ngrok en lugar de una IP local variable.

---

## 🔧 Cambios Necesarios

### 1. Encontrar el archivo de configuración del API

Busca en tu proyecto Flutter el archivo donde está definida la URL base del API. Probablemente esté en uno de estos lugares:

**Ubicaciones comunes:**
- `lib/config/api_config.dart`
- `lib/services/api_service.dart`
- `lib/utils/constants.dart`
- `lib/core/config.dart`
- `lib/config/app_config.dart`

**Buscar por:**
```dart
// Busca líneas como estas:
final String baseUrl = "http://192.168...";
const String API_URL = "http://...";
static const String baseUrl = "http://...";
```

---

### 2. Reemplazar la URL

**ANTES (IP local variable):**
```dart
final String baseUrl = "http://192.168.1.100:8000/api/v1";
// o cualquier IP tipo 192.168.x.x o 10.0.x.x
```

**DESPUÉS (URL fija de ngrok):**
```dart
final String baseUrl = "https://NOMBRE_DEL_DOMINIO.ngrok-free.app/api/v1";
```

**Importante:**
- Reemplaza `NOMBRE_DEL_DOMINIO` con el dominio que obtengas de ngrok
- Usa `https://` (no `http://`)
- Mantén `/api/v1` al final

---

### 3. Verificar todas las referencias

Busca en TODO el proyecto si hay otras referencias a IPs locales:

```bash
# Buscar en todo el proyecto
grep -r "192.168" lib/
grep -r "10.0.0" lib/
grep -r "localhost" lib/
grep -r "http://" lib/
```

Y reemplázalas con la URL de ngrok.

---

### 4. Actualizar archivo de configuración de entorno (si existe)

Si tu proyecto tiene archivos como:
- `.env`
- `assets/config/config.json`
- `lib/config/environment.dart`

Actualiza también ahí la URL.

**Ejemplo si usas .env:**
```env
# ANTES
API_BASE_URL=http://192.168.1.100:8000/api/v1

# DESPUÉS
API_BASE_URL=https://NOMBRE_DEL_DOMINIO.ngrok-free.app/api/v1
```

---

### 5. Verificar permisos de internet en Android

Asegúrate de que `android/app/src/main/AndroidManifest.xml` tenga:

```xml
<manifest xmlns:android="http://schemas.android.com/apk/res/android">
    <!-- Agregar estos permisos si no están -->
    <uses-permission android:name="android.permission.INTERNET"/>
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE"/>

    <!-- Permitir tráfico HTTP en texto claro (para desarrollo) -->
    <application
        android:usesCleartextTraffic="true"
        ...>
        ...
    </application>
</manifest>
```

---

### 6. Para iOS (si aplica)

Actualizar `ios/Runner/Info.plist` si es necesario:

```xml
<key>NSAppTransportSecurity</key>
<dict>
    <key>NSAllowsArbitraryLoads</key>
    <true/>
</dict>
```

---

## 📝 Ejemplo Completo de Cambio

### Archivo: `lib/services/api_service.dart`

**ANTES:**
```dart
class ApiService {
  static const String baseUrl = "http://192.168.1.100:8000/api/v1";

  static Future<Map<String, dynamic>> registerDevice({
    required String deviceId,
    String? fcmToken,
    required String deviceType,
  }) async {
    final url = Uri.parse('$baseUrl/mobile/register');

    final response = await http.post(
      url,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'device_id': deviceId,
        'fcm_token': fcmToken,
        'device_type': deviceType,
      }),
    );

    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> associateOrder({
    required String deviceId,
    required String qrToken,
  }) async {
    final url = Uri.parse('$baseUrl/mobile/orders/associate');
    // ... resto del código
  }
}
```

**DESPUÉS:**
```dart
class ApiService {
  // ✅ ÚNICO CAMBIO: Reemplazar la URL
  static const String baseUrl = "https://tu-proyecto.ngrok-free.app/api/v1";

  static Future<Map<String, dynamic>> registerDevice({
    required String deviceId,
    String? fcmToken,
    required String deviceType,
  }) async {
    final url = Uri.parse('$baseUrl/mobile/register');

    final response = await http.post(
      url,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'device_id': deviceId,
        'fcm_token': fcmToken,
        'device_type': deviceType,
      }),
    );

    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> associateOrder({
    required String deviceId,
    required String qrToken,
  }) async {
    final url = Uri.parse('$baseUrl/mobile/orders/associate');
    // ... resto del código (sin cambios)
  }
}
```

---

## ✅ Checklist de Verificación

Después de hacer los cambios, verifica:

- [ ] La URL base fue cambiada de IP local a ngrok
- [ ] Todas las referencias a IPs locales fueron actualizadas
- [ ] La URL usa `https://` (no `http://`)
- [ ] La URL termina en `/api/v1`
- [ ] Los permisos de internet están en AndroidManifest.xml
- [ ] No hay otras IPs hardcodeadas en el proyecto

---

## 🧪 Probar los Cambios

### 1. Limpiar y reconstruir

```bash
flutter clean
flutter pub get
flutter run
```

### 2. Verificar la conexión

Agregar un log temporal para confirmar que usa la URL correcta:

```dart
print('🌐 API Base URL: $baseUrl');
```

### 3. Probar registro de dispositivo

La app debe poder:
- Registrar el dispositivo
- Escanear QR de órdenes
- Ver lista de órdenes
- Recibir notificaciones

---

## 🚨 Problemas Comunes

### Error: "Failed to connect"
**Solución:** Verifica que el servidor ngrok esté corriendo:
```bash
# En el proyecto Laravel
start-with-ngrok.bat
```

### Error: "Certificate verification failed"
**Solución:** ngrok usa HTTPS válido, pero si hay problemas:
```dart
// Solo para desarrollo (NO en producción)
import 'dart:io';

class MyHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)
      ..badCertificateCallback = (X509Certificate cert, String host, int port) => true;
  }
}

void main() {
  HttpOverrides.global = MyHttpOverrides();
  runApp(MyApp());
}
```

### Error: "ngrok-free.app not found"
**Solución:** Asegúrate de que:
1. El dominio está bien escrito
2. El servidor ngrok está corriendo con ese dominio
3. Usas `https://` no `http://`

---

## 📋 Resumen de Pasos

1. **Buscar** el archivo donde está la URL del API (ej: `api_service.dart`)
2. **Reemplazar** la IP local por la URL de ngrok
3. **Verificar** que no haya otras IPs en el código
4. **Actualizar** permisos en AndroidManifest.xml si es necesario
5. **Limpiar y reconstruir** con `flutter clean && flutter pub get`
6. **Probar** que la app se conecta correctamente

---

## 🎯 Lo Más Importante

**SOLO NECESITAS CAMBIAR UNA LÍNEA:**

```dart
// DE:
static const String baseUrl = "http://192.168.X.X:8000/api/v1";

// A:
static const String baseUrl = "https://TU-DOMINIO.ngrok-free.app/api/v1";
```

---

## 💡 Ejemplo de Prompt para Claude Code

Copia esto y pégalo en Claude Code de tu proyecto Flutter:

```
Necesito cambiar la URL del API en mi app Flutter.

Actualmente usa una IP local como:
http://192.168.1.100:8000/api/v1

Necesito cambiarla a una URL fija de ngrok:
https://MI-DOMINIO.ngrok-free.app/api/v1

Por favor:
1. Busca donde está definida la baseUrl o API_URL en el proyecto
2. Reemplázala con: https://MI-DOMINIO.ngrok-free.app/api/v1
3. Busca si hay otras referencias a IPs locales (192.168.x.x) en el código
4. Verifica que AndroidManifest.xml tenga los permisos de internet
5. Muéstrame qué archivos modificaste

El dominio específico que debo usar es: [PEGA AQUÍ TU DOMINIO DE NGROK]
```

---

**¡Eso es todo!** Con este cambio, tu app Flutter funcionará con el servidor ngrok sin importar en qué red estés.
