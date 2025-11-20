# 📱 Cambio en App Flutter - SUPER SIMPLE

## 🎯 Solo necesitas cambiar UNA cosa

---

## PASO 1: Buscar el archivo de configuración

En tu proyecto Flutter, busca el archivo donde está la URL del API.

**Probablemente está en uno de estos:**
- `lib/services/api_service.dart`
- `lib/config/api_config.dart`
- `lib/utils/constants.dart`
- `lib/config/app_config.dart`

**Busca por la palabra:** `192.168` o `http://`

---

## PASO 2: Hacer el cambio

Busca una línea que se vea así:

```dart
// ANTES (puede variar en tu app)
final String baseUrl = "http://192.168.1.100:8000/api/v1";
// O algo como:
static const String baseUrl = "http://192.168.X.X:8000/api/v1";
// O:
const String API_URL = "http://10.0.0.X:8000/api/v1";
```

**Cámbiala por:**

```dart
// DESPUÉS
final String baseUrl = "https://gerald-ironical-contradictorily.ngrok-free.dev/api/v1";
```

---

## PASO 3: Verificar AndroidManifest.xml

Abre: `android/app/src/main/AndroidManifest.xml`

Asegúrate de que tenga esto (probablemente ya lo tiene):

```xml
<manifest xmlns:android="http://schemas.android.com/apk/res/android">
    <!-- Estos permisos deben estar ANTES de <application> -->
    <uses-permission android:name="android.permission.INTERNET"/>
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE"/>

    <application
        ...>
        ...
    </application>
</manifest>
```

---

## PASO 4: Reconstruir la app

```bash
flutter clean
flutter pub get
flutter run
```

---

## ✅ Eso es TODO

Solo cambias:
```
http://192.168.X.X:8000/api/v1
```

Por:
```
https://gerald-ironical-contradictorily.ngrok-free.dev/api/v1
```

---

## 🔍 Ejemplo Completo

### Si tu archivo `api_service.dart` se ve así:

**ANTES:**
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

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

  // ... más métodos
}
```

**DESPUÉS (solo cambia la línea 5):**
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String baseUrl = "https://gerald-ironical-contradictorily.ngrok-free.dev/api/v1";  // ← ÚNICO CAMBIO

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

  // ... más métodos (sin cambios)
}
```

---

## 💡 Tip: Buscar TODAS las referencias

Para asegurarte de cambiar todas las IPs, busca en VS Code:

1. Presiona `Ctrl + Shift + F` (buscar en todo el proyecto)
2. Busca: `192.168`
3. Reemplaza todas con: `https://gerald-ironical-contradictorily.ngrok-free.dev`

También busca:
- `10.0.`
- `localhost`
- `http://[número]`

---

## ✅ Checklist

- [ ] Encontré el archivo con la URL del API
- [ ] Cambié la URL a: `https://gerald-ironical-contradictorily.ngrok-free.dev/api/v1`
- [ ] Verifiqué AndroidManifest.xml tiene permisos de internet
- [ ] Busqué otras referencias a IPs locales
- [ ] Ejecuté `flutter clean`
- [ ] Ejecuté `flutter pub get`
- [ ] Probé la app con `flutter run`

---

## 🎯 Resumen Ultra Simple

**Buscar:**
```dart
"http://192.168.1.100:8000/api/v1"
```

**Reemplazar por:**
```dart
"https://gerald-ironical-contradictorily.ngrok-free.dev/api/v1"
```

**Listo.** 🚀
