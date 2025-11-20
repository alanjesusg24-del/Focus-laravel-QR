# 📱 Configurar App Móvil Flutter para ngrok

## 🎯 Resumen

Tu app móvil Flutter actualmente usa:
```dart
// ANTES (IP fija)
final String baseUrl = "http://192.168.1.100:8000/api/v1";
```

Necesitas cambiarla para usar la URL de ngrok que es dinámica.

---

## 🔧 Solución Recomendada: Configuración Dinámica

### Opción 1: Escanear QR de Configuración (RECOMENDADA) ⭐

Esta es la mejor opción para presentaciones.

#### Paso 1: Agregar campo de configuración en la app

En tu app Flutter, agrega una pantalla de configuración o un campo en `main.dart`:

```dart
// lib/config/api_config.dart
import 'package:shared_preferences/shared_preferences.dart';

class ApiConfig {
  static const String _keyBaseUrl = 'base_url';
  static const String _defaultUrl = 'http://localhost:8000/api/v1';

  // Obtener URL guardada
  static Future<String> getBaseUrl() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_keyBaseUrl) ?? _defaultUrl;
  }

  // Guardar nueva URL
  static Future<void> setBaseUrl(String url) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_keyBaseUrl, url);
  }

  // Verificar si hay URL configurada
  static Future<bool> isConfigured() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.containsKey(_keyBaseUrl);
  }
}
```

#### Paso 2: Pantalla de configuración inicial

```dart
// lib/screens/setup_screen.dart
import 'package:flutter/material.dart';
import 'package:qr_code_scanner/qr_code_scanner.dart';
import '../config/api_config.dart';

class SetupScreen extends StatefulWidget {
  @override
  _SetupScreenState createState() => _SetupScreenState();
}

class _SetupScreenState extends State<SetupScreen> {
  final TextEditingController _urlController = TextEditingController();
  final GlobalKey qrKey = GlobalKey(debugLabel: 'QR');
  QRViewController? controller;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Configurar Servidor'),
      ),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Título
            Text(
              'Configuración del Servidor',
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
            SizedBox(height: 20),

            // Opción 1: Escanear QR
            Card(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  children: [
                    Icon(Icons.qr_code_scanner, size: 64, color: Colors.blue),
                    SizedBox(height: 10),
                    Text('Opción 1: Escanear QR',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    SizedBox(height: 10),
                    ElevatedButton.icon(
                      onPressed: _scanQR,
                      icon: Icon(Icons.qr_code_scanner),
                      label: Text('Escanear Código QR'),
                      style: ElevatedButton.styleFrom(
                        padding: EdgeInsets.symmetric(horizontal: 30, vertical: 15),
                      ),
                    ),
                    SizedBox(height: 5),
                    Text(
                      'Abre /mobile-config en el servidor',
                      style: TextStyle(fontSize: 12, color: Colors.grey),
                    ),
                  ],
                ),
              ),
            ),

            SizedBox(height: 20),

            // Opción 2: Ingresar manualmente
            Card(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  children: [
                    Icon(Icons.edit, size: 64, color: Colors.orange),
                    SizedBox(height: 10),
                    Text('Opción 2: Ingresar Manualmente',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    SizedBox(height: 10),
                    TextField(
                      controller: _urlController,
                      decoration: InputDecoration(
                        labelText: 'URL del Servidor',
                        hintText: 'https://abc123.ngrok.io/api/v1',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.link),
                      ),
                    ),
                    SizedBox(height: 10),
                    ElevatedButton.icon(
                      onPressed: _saveUrl,
                      icon: Icon(Icons.save),
                      label: Text('Guardar Configuración'),
                      style: ElevatedButton.styleFrom(
                        padding: EdgeInsets.symmetric(horizontal: 30, vertical: 15),
                      ),
                    ),
                  ],
                ),
              ),
            ),

            Spacer(),

            // Ayuda
            Text(
              '💡 Ejecuta start-with-ngrok.bat en el servidor\ny abre la URL /mobile-config',
              style: TextStyle(fontSize: 12, color: Colors.grey),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  void _scanQR() async {
    // Navegar a pantalla de scanner QR
    final result = await Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => QRScannerScreen()),
    );

    if (result != null) {
      await _processQRCode(result);
    }
  }

  Future<void> _processQRCode(String qrData) async {
    // El QR contiene la URL del API
    // Ej: "https://abc123.ngrok.io/api/v1"
    if (qrData.contains('/api')) {
      await ApiConfig.setBaseUrl(qrData);
      _showSuccess('Configuración guardada correctamente');
      _navigateToHome();
    } else {
      _showError('Código QR inválido');
    }
  }

  void _saveUrl() async {
    String url = _urlController.text.trim();

    // Validar URL
    if (url.isEmpty) {
      _showError('Por favor ingresa una URL');
      return;
    }

    // Agregar /api/v1 si no lo tiene
    if (!url.contains('/api')) {
      url = url + '/api/v1';
    }

    // Guardar configuración
    await ApiConfig.setBaseUrl(url);
    _showSuccess('Configuración guardada correctamente');
    _navigateToHome();
  }

  void _showSuccess(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.green,
      ),
    );
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.red,
      ),
    );
  }

  void _navigateToHome() {
    Navigator.pushReplacementNamed(context, '/home');
  }
}
```

#### Paso 3: Actualizar tu API client

```dart
// lib/services/api_service.dart
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../config/api_config.dart';

class ApiService {
  static Future<String> get baseUrl async => await ApiConfig.getBaseUrl();

  // Registrar dispositivo
  static Future<Map<String, dynamic>> registerDevice({
    required String deviceId,
    String? fcmToken,
    required String deviceType,
    String? deviceModel,
    String? osVersion,
    String? appVersion,
  }) async {
    final url = Uri.parse('${await baseUrl}/mobile/register');

    final response = await http.post(
      url,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'device_id': deviceId,
        'fcm_token': fcmToken,
        'device_type': deviceType,
        'device_model': deviceModel,
        'os_version': osVersion,
        'app_version': appVersion,
      }),
    );

    return jsonDecode(response.body);
  }

  // Asociar orden
  static Future<Map<String, dynamic>> associateOrder({
    required String deviceId,
    required String qrToken,
  }) async {
    final url = Uri.parse('${await baseUrl}/mobile/orders/associate');

    final response = await http.post(
      url,
      headers: {
        'Content-Type': 'application/json',
        'X-Device-ID': deviceId,
      },
      body: jsonEncode({'qr_token': qrToken}),
    );

    return jsonDecode(response.body);
  }

  // Obtener órdenes
  static Future<Map<String, dynamic>> getOrders({
    required String deviceId,
    String? status,
    int page = 1,
    int perPage = 20,
  }) async {
    String queryString = '?page=$page&per_page=$perPage';
    if (status != null) queryString += '&status=$status';

    final url = Uri.parse('${await baseUrl}/mobile/orders$queryString');

    final response = await http.get(
      url,
      headers: {'X-Device-ID': deviceId},
    );

    return jsonDecode(response.body);
  }
}
```

#### Paso 4: Modificar main.dart

```dart
// lib/main.dart
import 'package:flutter/material.dart';
import 'screens/setup_screen.dart';
import 'screens/home_screen.dart';
import 'config/api_config.dart';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Order QR System',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
      home: SplashScreen(),
      routes: {
        '/setup': (context) => SetupScreen(),
        '/home': (context) => HomeScreen(),
      },
    );
  }
}

class SplashScreen extends StatefulWidget {
  @override
  _SplashScreenState createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkConfiguration();
  }

  Future<void> _checkConfiguration() async {
    await Future.delayed(Duration(seconds: 2)); // Splash delay

    final isConfigured = await ApiConfig.isConfigured();

    if (isConfigured) {
      Navigator.pushReplacementNamed(context, '/home');
    } else {
      Navigator.pushReplacementNamed(context, '/setup');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.qr_code, size: 100, color: Colors.blue),
            SizedBox(height: 20),
            Text(
              'Order QR System',
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            SizedBox(height: 20),
            CircularProgressIndicator(),
          ],
        ),
      ),
    );
  }
}
```

---

## 📦 Dependencias Necesarias

Agrega a tu `pubspec.yaml`:

```yaml
dependencies:
  flutter:
    sdk: flutter
  shared_preferences: ^2.2.2  # Para guardar configuración
  qr_code_scanner: ^1.0.1     # Para escanear QR
  http: ^1.1.0                # Para API calls
```

Luego ejecuta:
```bash
flutter pub get
```

---

## 🚀 Flujo Completo de Uso

### Primera Vez (Configuración):

1. Usuario abre la app → Ve splash screen
2. App detecta que no hay configuración → Muestra SetupScreen
3. Usuario tiene 2 opciones:
   - **Opción A:** Escanear QR desde `/mobile-config`
   - **Opción B:** Ingresar URL manualmente
4. App guarda configuración en SharedPreferences
5. App navega a HomeScreen

### Usos Posteriores:

1. Usuario abre la app → Ve splash screen
2. App detecta configuración existente → Va directo a HomeScreen
3. App usa la URL guardada para todas las peticiones API

### Cambiar Configuración:

Agrega un botón en Settings:

```dart
// En tu settings screen
ElevatedButton(
  onPressed: () {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => SetupScreen()),
    );
  },
  child: Text('Cambiar Servidor'),
)
```

---

## 🎓 Para tu Presentación en Clase

### Preparación:

1. **Backend:**
   ```bash
   start-with-ngrok.bat
   ```
   - Espera la URL de ngrok
   - Abre `/mobile-config` en el navegador

2. **App Móvil:**
   - Abre la app
   - Toca "Escanear QR"
   - Escanea el QR de la pantalla
   - ¡Listo! ✅

### Durante la Presentación:

1. Proyecta el QR en la pantalla
2. Tus compañeros escanean con sus apps
3. Sus apps se configuran automáticamente
4. Todos ven las órdenes en tiempo real

---

## 🔧 Opción 2: URL Fija de ngrok (Cuenta Pro)

Si prefieres no cambiar la app, puedes usar una URL fija de ngrok:

1. Crea cuenta en https://dashboard.ngrok.com/
2. Reserva un dominio: `tu-proyecto.ngrok-free.app`
3. En la app, usa:
   ```dart
   final String baseUrl = "https://tu-proyecto.ngrok-free.app/api/v1";
   ```
4. Siempre inicia con:
   ```bash
   ngrok http 8000 --domain=tu-proyecto.ngrok-free.app
   ```

**Costo:** ~$8 USD/mes para URL fija

---

## ✅ Resumen

**Lo que DEBES hacer en la app móvil:**

1. Agregar `shared_preferences` y `qr_code_scanner` a pubspec.yaml
2. Crear `api_config.dart` para manejar URL dinámica
3. Crear `setup_screen.dart` para configuración inicial
4. Modificar `api_service.dart` para usar URL dinámica
5. Actualizar `main.dart` para mostrar setup si es primera vez

**Una vez hecho esto:**
- La app funcionará con cualquier servidor (local o ngrok)
- Solo necesitas escanear el QR una vez
- La configuración se guarda automáticamente
- Perfecto para demos y presentaciones

---

¿Necesitas ayuda con alguna parte específica del código Flutter?
