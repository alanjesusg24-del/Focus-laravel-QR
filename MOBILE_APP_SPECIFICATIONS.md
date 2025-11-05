# Especificaciones Técnicas - App Móvil Order QR System (Flutter)

## 1. Descripción General del Sistema

Order QR System es una plataforma para gestión de órdenes con códigos QR que conecta negocios con sus clientes mediante una aplicación web (Laravel) y una aplicación móvil (Flutter).

---

## 2. Flujo Completo del Sistema

### Fase 1: Creación de Orden (Web - Laravel)
1. El negocio crea una orden desde la página web
2. El sistema genera automáticamente:
   - **Folio number**: Número de orden único (ej: ORD-001)
   - **QR Token**: Token único para asociar la orden con el cliente (32 caracteres alfanuméricos)
   - **Pickup Token**: Token de 16 caracteres que el cliente muestra para recoger
   - **QR Code URL**: Imagen del código QR almacenada en storage/qr_codes/
3. La orden se crea en estado `pending`

### Fase 2: Asociación con Cliente (Móvil - Flutter)
1. El cliente abre la app móvil Flutter
2. El cliente escanea el código QR desde su dispositivo móvil
3. El QR contiene una URL en formato: `https://app.example.com/order/scan/{qr_token}`
4. La app móvil hace una petición a la API para asociar la orden con el cliente
5. La orden queda vinculada al `mobile_user_id` del cliente
6. El cliente puede ver el estado de su orden en tiempo real desde la app

### Fase 3: Orden Lista (Web - Laravel)
1. Cuando el pedido está listo, el negocio marca la orden como "Lista" desde la web
2. El sistema actualiza:
   - `status` → `ready`
   - `ready_at` → timestamp actual
3. Se envía notificación push al dispositivo móvil del cliente
4. El cliente recibe alerta de que puede recoger su orden

### Fase 4: Entrega (Web - Laravel + Lector QR)
1. El cliente llega al negocio para recoger
2. El cliente abre su app y muestra el código QR en pantalla
3. El negocio tiene un lector QR USB/Bluetooth conectado a la computadora
4. El negocio escanea el QR del cliente con el lector
5. El sistema automáticamente:
   - Valida que el `qr_token` escaneado corresponda a la orden
   - Marca la orden como `delivered`
   - Actualiza `delivered_at` con timestamp
   - Muestra confirmación visual (modal verde con check)
   - Reproduce sonido de éxito
6. El proceso es instantáneo y sin clics adicionales

---

## 3. Estructura de la Base de Datos

### Tabla: `orders`

```sql
CREATE TABLE orders (
    order_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    business_id BIGINT NOT NULL,
    folio_number VARCHAR(50) NOT NULL,
    description TEXT,
    qr_code_url VARCHAR(255),
    qr_token VARCHAR(64) UNIQUE NOT NULL,
    pickup_token VARCHAR(16) NOT NULL,
    status ENUM('pending', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
    mobile_user_id BIGINT NULL,
    ready_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (business_id) REFERENCES businesses(business_id),
    FOREIGN KEY (mobile_user_id) REFERENCES mobile_users(mobile_user_id)
);
```

### Tabla: `mobile_users` (a implementar en móvil)

```sql
CREATE TABLE mobile_users (
    mobile_user_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    device_id VARCHAR(255) UNIQUE NOT NULL,
    fcm_token VARCHAR(255) NULL,
    device_type ENUM('android', 'ios') NOT NULL,
    device_model VARCHAR(100) NULL,
    os_version VARCHAR(50) NULL,
    app_version VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_active_at TIMESTAMP NULL
);
```

### Tabla: `businesses`

```sql
CREATE TABLE businesses (
    business_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    business_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    has_chat_module BOOLEAN DEFAULT FALSE,
    theme VARCHAR(50) DEFAULT 'light',
    data_retention_months INT DEFAULT 1,
    monthly_price DECIMAL(10,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 4. APIs Necesarias (Backend Laravel)

### Base URL
```
https://api.orderqrsystem.com/api/v1
```

### 4.1 Registro/Identificación de Dispositivo Móvil

**Endpoint:** `POST /mobile/register`

**Headers:**
```json
{
    "Content-Type": "application/json",
    "Accept": "application/json"
}
```

**Request Body:**
```json
{
    "device_id": "unique-device-id-12345",
    "fcm_token": "firebase-cloud-messaging-token",
    "device_type": "android",
    "device_model": "Samsung Galaxy S21",
    "os_version": "Android 13",
    "app_version": "1.0.0"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "mobile_user_id": 1,
        "device_id": "unique-device-id-12345",
        "created_at": "2025-11-05T10:30:00Z"
    },
    "message": "Dispositivo registrado exitosamente"
}
```

**Response Error (400):**
```json
{
    "success": false,
    "message": "Error en los datos proporcionados",
    "errors": {
        "device_id": ["El device_id es requerido"]
    }
}
```

---

### 4.2 Asociar Orden con Cliente (Escaneo Inicial)

**Endpoint:** `POST /mobile/orders/associate`

**Headers:**
```json
{
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-Device-ID": "unique-device-id-12345"
}
```

**Request Body:**
```json
{
    "qr_token": "JbcWbYvulKWxRbR3jaVfsoAmxPsljmWJ"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "order": {
            "order_id": 123,
            "folio_number": "ORD-001",
            "description": "2 Tacos al pastor, 1 refresco",
            "status": "pending",
            "pickup_token": "1KXRh3nniAX9Losg",
            "qr_token": "JbcWbYvulKWxRbR3jaVfsoAmxPsljmWJ",
            "qr_code_url": "https://app.example.com/order/scan/JbcWbYvulKWxRbR3jaVfsoAmxPsljmWJ",
            "business": {
                "business_id": 1,
                "business_name": "Tacos El Güero",
                "phone": "555-1234",
                "address": "Calle Principal 123"
            },
            "created_at": "2025-11-05T10:00:00Z",
            "ready_at": null,
            "delivered_at": null
        }
    },
    "message": "Orden asociada exitosamente"
}
```

**Response Error (404):**
```json
{
    "success": false,
    "message": "Orden no encontrada o QR inválido"
}
```

**Response Error (409):**
```json
{
    "success": false,
    "message": "Esta orden ya está asociada a otro dispositivo"
}
```

---

### 4.3 Obtener Órdenes del Cliente

**Endpoint:** `GET /mobile/orders`

**Headers:**
```json
{
    "Accept": "application/json",
    "X-Device-ID": "unique-device-id-12345"
}
```

**Query Parameters:**
- `status` (opcional): `pending`, `ready`, `delivered`, `cancelled`
- `page` (opcional): número de página (default: 1)
- `per_page` (opcional): resultados por página (default: 20)

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "orders": [
            {
                "order_id": 123,
                "folio_number": "ORD-001",
                "description": "2 Tacos al pastor, 1 refresco",
                "status": "ready",
                "pickup_token": "1KXRh3nniAX9Losg",
                "qr_code_url": "https://app.example.com/order/scan/JbcWbYvulKWxRbR3jaVfsoAmxPsljmWJ",
                "business": {
                    "business_id": 1,
                    "business_name": "Tacos El Güero",
                    "phone": "555-1234"
                },
                "created_at": "2025-11-05T10:00:00Z",
                "ready_at": "2025-11-05T10:15:00Z",
                "delivered_at": null
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 3,
            "total_items": 45,
            "per_page": 20
        }
    }
}
```

---

### 4.4 Obtener Detalle de Orden

**Endpoint:** `GET /mobile/orders/{order_id}`

**Headers:**
```json
{
    "Accept": "application/json",
    "X-Device-ID": "unique-device-id-12345"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "order": {
            "order_id": 123,
            "folio_number": "ORD-001",
            "description": "2 Tacos al pastor, 1 refresco",
            "status": "ready",
            "pickup_token": "1KXRh3nniAX9Losg",
            "qr_token": "JbcWbYvulKWxRbR3jaVfsoAmxPsljmWJ",
            "qr_code_url": "https://app.example.com/order/scan/JbcWbYvulKWxRbR3jaVfsoAmxPsljmWJ",
            "business": {
                "business_id": 1,
                "business_name": "Tacos El Güero",
                "phone": "555-1234",
                "address": "Calle Principal 123"
            },
            "timeline": [
                {
                    "status": "pending",
                    "timestamp": "2025-11-05T10:00:00Z",
                    "label": "Orden Creada"
                },
                {
                    "status": "ready",
                    "timestamp": "2025-11-05T10:15:00Z",
                    "label": "Orden Lista para Recoger"
                }
            ],
            "created_at": "2025-11-05T10:00:00Z",
            "ready_at": "2025-11-05T10:15:00Z",
            "delivered_at": null,
            "cancelled_at": null
        }
    }
}
```

**Response Error (404):**
```json
{
    "success": false,
    "message": "Orden no encontrada o no pertenece a este dispositivo"
}
```

---

### 4.5 Actualizar FCM Token

**Endpoint:** `PUT /mobile/update-token`

**Headers:**
```json
{
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-Device-ID": "unique-device-id-12345"
}
```

**Request Body:**
```json
{
    "fcm_token": "new-firebase-token-xyz"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Token actualizado exitosamente"
}
```

---

## 5. Notificaciones Push (Firebase Cloud Messaging)

### 5.1 Cuando la orden está lista

**Payload FCM:**
```json
{
    "to": "fcm-token-del-dispositivo",
    "notification": {
        "title": "¡Tu orden está lista! 🎉",
        "body": "Tu orden ORD-001 está lista para recoger en Tacos El Güero",
        "sound": "default",
        "badge": "1"
    },
    "data": {
        "type": "order_ready",
        "order_id": "123",
        "folio_number": "ORD-001",
        "business_name": "Tacos El Güero",
        "pickup_token": "1KXRh3nniAX9Losg",
        "timestamp": "2025-11-05T10:15:00Z"
    },
    "priority": "high",
    "android": {
        "notification": {
            "channel_id": "order_updates",
            "color": "#28a745",
            "icon": "ic_notification"
        }
    },
    "apns": {
        "payload": {
            "aps": {
                "sound": "success.mp3",
                "badge": 1
            }
        }
    }
}
```

### 5.2 Cuando la orden es cancelada

**Payload FCM:**
```json
{
    "to": "fcm-token-del-dispositivo",
    "notification": {
        "title": "Orden Cancelada",
        "body": "Tu orden ORD-001 ha sido cancelada: Stock agotado",
        "sound": "default",
        "badge": "1"
    },
    "data": {
        "type": "order_cancelled",
        "order_id": "123",
        "folio_number": "ORD-001",
        "business_name": "Tacos El Güero",
        "cancellation_reason": "Stock agotado",
        "timestamp": "2025-11-05T10:20:00Z"
    },
    "priority": "high"
}
```

---

## 6. Especificaciones de la App Móvil (Flutter)

### 6.1 Pantallas Principales

#### A) Splash Screen
- Logo de la aplicación
- Verificación de conexión
- Registro/actualización del dispositivo

#### B) Home / Lista de Órdenes
- Lista de órdenes del cliente (activas primero)
- Filtros por estado: Todas, Pendientes, Listas, Entregadas
- Pull to refresh
- Badge con número de órdenes activas
- Floating Action Button para escanear nuevo QR

#### C) Escanear QR (Primera vez)
- Cámara para escanear QR del negocio
- Instrucciones claras: "Escanea el código QR de tu orden"
- Feedback visual al escanear
- Asociación automática con el dispositivo

#### D) Detalle de Orden
- Información completa de la orden
- Timeline visual del estado
- QR grande para mostrar al negocio (para recoger)
- Botón destacado con pickup_token
- Información del negocio (nombre, teléfono, dirección)
- Botón para llamar al negocio

#### E) Configuración
- Notificaciones activadas/desactivadas
- Historial de órdenes
- Información de la app
- Cerrar sesión / limpiar datos

### 6.2 Componentes Clave

#### QR Display Widget
```dart
// Mostrar QR grande para que el negocio lo escanee
Widget buildQRDisplay(String qrToken) {
  return Column(
    children: [
      QrImageView(
        data: 'https://app.example.com/order/scan/$qrToken',
        version: QrVersions.auto,
        size: 280.0,
        backgroundColor: Colors.white,
      ),
      SizedBox(height: 16),
      Text(
        'Muestra este código al negocio',
        style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
      ),
    ],
  );
}
```

#### Status Badge Widget
```dart
Widget buildStatusBadge(String status) {
  final Map<String, Map<String, dynamic>> statusConfig = {
    'pending': {
      'label': 'Pendiente',
      'color': Colors.orange,
      'icon': Icons.schedule,
    },
    'ready': {
      'label': 'Listo para Recoger',
      'color': Colors.green,
      'icon': Icons.check_circle,
    },
    'delivered': {
      'label': 'Entregado',
      'color': Colors.blue,
      'icon': Icons.done_all,
    },
    'cancelled': {
      'label': 'Cancelado',
      'color': Colors.red,
      'icon': Icons.cancel,
    },
  };

  final config = statusConfig[status]!;

  return Chip(
    avatar: Icon(config['icon'], color: Colors.white, size: 18),
    label: Text(config['label'], style: TextStyle(color: Colors.white)),
    backgroundColor: config['color'],
  );
}
```

### 6.3 Gestión de Notificaciones

#### Configuración Firebase
```dart
class NotificationService {
  final FirebaseMessaging _fcm = FirebaseMessaging.instance;

  Future<void> initialize() async {
    // Solicitar permisos
    NotificationSettings settings = await _fcm.requestPermission(
      alert: true,
      badge: true,
      sound: true,
    );

    if (settings.authorizationStatus == AuthorizationStatus.authorized) {
      // Obtener token
      String? token = await _fcm.getToken();

      // Enviar token al backend
      await ApiService.updateFCMToken(token);

      // Escuchar mensajes en foreground
      FirebaseMessaging.onMessage.listen(_handleForegroundMessage);

      // Manejar tap en notificación
      FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationTap);
    }
  }

  void _handleForegroundMessage(RemoteMessage message) {
    // Mostrar notificación local
    if (message.data['type'] == 'order_ready') {
      _showLocalNotification(
        title: message.notification?.title ?? 'Orden Lista',
        body: message.notification?.body ?? '',
        payload: message.data,
      );

      // Reproducir sonido
      _playSuccessSound();

      // Actualizar UI
      _refreshOrdersList();
    }
  }

  void _handleNotificationTap(RemoteMessage message) {
    // Navegar al detalle de la orden
    final orderId = message.data['order_id'];
    Navigator.pushNamed(
      context,
      '/order-detail',
      arguments: {'orderId': orderId},
    );
  }
}
```

### 6.4 Gestión de Estado (Provider/Riverpod/Bloc)

#### Orders Provider Example
```dart
class OrdersProvider extends ChangeNotifier {
  List<Order> _orders = [];
  bool _isLoading = false;
  String? _error;

  List<Order> get orders => _orders;
  bool get isLoading => _isLoading;
  String? get error => _error;

  List<Order> get activeOrders =>
    _orders.where((o) => ['pending', 'ready'].contains(o.status)).toList();

  Future<void> fetchOrders() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await ApiService.getOrders();
      _orders = response.orders;
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> associateOrder(String qrToken) async {
    try {
      final order = await ApiService.associateOrder(qrToken);
      _orders.insert(0, order);
      notifyListeners();

      // Mostrar confirmación
      _showSuccessDialog('Orden asociada exitosamente');
    } catch (e) {
      throw Exception('Error al asociar orden: $e');
    }
  }
}
```

### 6.5 Persistencia Local (SQLite/Hive)

Guardar órdenes localmente para modo offline:

```dart
class LocalDatabase {
  static final _databaseName = "OrderQR.db";
  static final _databaseVersion = 1;

  static final table = 'orders';

  static final columnOrderId = 'order_id';
  static final columnFolioNumber = 'folio_number';
  static final columnDescription = 'description';
  static final columnStatus = 'status';
  static final columnQrToken = 'qr_token';
  static final columnPickupToken = 'pickup_token';
  static final columnBusinessName = 'business_name';
  static final columnCreatedAt = 'created_at';
  static final columnReadyAt = 'ready_at';
  static final columnDeliveredAt = 'delivered_at';

  // Singleton
  LocalDatabase._privateConstructor();
  static final LocalDatabase instance = LocalDatabase._privateConstructor();

  static Database? _database;

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDatabase();
    return _database!;
  }

  Future<void> insertOrder(Order order) async {
    final db = await database;
    await db.insert(table, order.toMap(),
      conflictAlgorithm: ConflictAlgorithm.replace);
  }

  Future<List<Order>> getAllOrders() async {
    final db = await database;
    final List<Map<String, dynamic>> maps = await db.query(table,
      orderBy: '$columnCreatedAt DESC');
    return List.generate(maps.length, (i) => Order.fromMap(maps[i]));
  }
}
```

---

## 7. Flujo de Datos y Sincronización

### Sincronización en Tiempo Real

1. **Al abrir la app:**
   - Verificar conexión a internet
   - Sincronizar órdenes del servidor
   - Actualizar base de datos local
   - Mostrar órdenes activas primero

2. **Cuando llega notificación push:**
   - Actualizar estado de la orden en tiempo real
   - Mostrar badge en la app
   - Reproducir sonido/vibración
   - Actualizar UI inmediatamente

3. **Pull to Refresh:**
   - Hacer request a `/mobile/orders`
   - Actualizar lista completa
   - Mostrar indicador de carga

4. **Modo Offline:**
   - Mostrar órdenes desde base de datos local
   - Indicar que no hay conexión
   - Al recuperar conexión, sincronizar automáticamente

---

## 8. Consideraciones de Seguridad

### 8.1 Autenticación
- No hay login tradicional de usuario
- El dispositivo se identifica por `device_id` único (UUID)
- El `device_id` se genera al instalar la app y persiste
- Usar SSL/TLS para todas las comunicaciones

### 8.2 Validación
- Validar que el QR escaneado sea válido antes de enviar al backend
- El formato debe ser: `https://app.example.com/order/scan/{token}`
- El token debe tener 32 caracteres alfanuméricos

### 8.3 Headers de Seguridad
- Incluir `X-Device-ID` en todas las peticiones
- Opcional: Implementar firma HMAC para verificar integridad
- Rate limiting: máximo 60 requests por minuto por dispositivo

---

## 9. Manejo de Errores

### Códigos de Error Comunes

| Código | Mensaje | Acción |
|--------|---------|--------|
| 404 | Orden no encontrada | Mostrar mensaje y permitir escanear otro QR |
| 409 | Orden ya asociada | Informar que la orden ya tiene dueño |
| 422 | QR inválido | Solicitar escanear nuevamente |
| 429 | Demasiadas peticiones | Esperar 60 segundos |
| 500 | Error del servidor | Reintentar en 5 segundos |
| 503 | Servicio no disponible | Modo offline, usar datos locales |

---

## 10. Testing y QA

### 10.1 Casos de Prueba Principales

1. **Escaneo de QR Primera Vez**
   - ✅ Escanear QR válido → Orden se asocia
   - ✅ Escanear QR inválido → Mostrar error
   - ✅ Escanear QR ya asociado → Mostrar mensaje

2. **Notificaciones**
   - ✅ Recibir notificación cuando orden está lista
   - ✅ Tap en notificación → Navegar a detalle
   - ✅ Sonido y vibración funcionan

3. **Estados de Orden**
   - ✅ Orden pending → Se muestra correctamente
   - ✅ Orden ready → Se actualiza en tiempo real
   - ✅ Orden delivered → Se marca como completada
   - ✅ Orden cancelled → Se muestra motivo

4. **Modo Offline**
   - ✅ Ver órdenes guardadas localmente
   - ✅ Sincronizar al recuperar conexión
   - ✅ Indicador de "Sin conexión"

5. **QR para Entrega**
   - ✅ QR se muestra grande y claro
   - ✅ Pickup token visible
   - ✅ Negocio puede escanear correctamente

### 10.2 Dispositivos de Prueba
- Android 9+ (API 28+)
- iOS 12+
- Diferentes tamaños de pantalla
- Tablets

---

## 11. Dependencias Flutter Recomendadas

```yaml
dependencies:
  flutter:
    sdk: flutter

  # HTTP
  http: ^1.1.0
  dio: ^5.3.3

  # QR
  qr_code_scanner: ^1.0.1
  qr_flutter: ^4.1.0

  # Notificaciones
  firebase_core: ^2.24.0
  firebase_messaging: ^14.7.4
  flutter_local_notifications: ^16.2.0

  # Estado
  provider: ^6.1.1
  # O riverpod: ^2.4.9

  # Base de datos local
  sqflite: ^2.3.0
  # O hive: ^2.2.3

  # Device Info
  device_info_plus: ^9.1.1
  uuid: ^4.2.1

  # Permisos
  permission_handler: ^11.1.0

  # UI
  shimmer: ^3.0.0
  flutter_svg: ^2.0.9
  cached_network_image: ^3.3.0

  # Utilidades
  intl: ^0.18.1
  timeago: ^3.6.0
  url_launcher: ^6.2.2
```

---

## 12. Estructura de Carpetas Sugerida

```
lib/
├── main.dart
├── app.dart
├── config/
│   ├── api_config.dart
│   ├── firebase_config.dart
│   └── theme.dart
├── models/
│   ├── order.dart
│   ├── business.dart
│   └── mobile_user.dart
├── services/
│   ├── api_service.dart
│   ├── notification_service.dart
│   ├── database_service.dart
│   └── qr_service.dart
├── providers/
│   ├── orders_provider.dart
│   └── device_provider.dart
├── screens/
│   ├── splash_screen.dart
│   ├── home_screen.dart
│   ├── scan_qr_screen.dart
│   ├── order_detail_screen.dart
│   └── settings_screen.dart
├── widgets/
│   ├── order_card.dart
│   ├── status_badge.dart
│   ├── qr_display.dart
│   └── timeline_widget.dart
└── utils/
    ├── constants.dart
    ├── helpers.dart
    └── validators.dart
```

---

## 13. Información de Contacto y Recursos

### URLs del Sistema
- **Web App**: https://web.orderqrsystem.com
- **API Base**: https://api.orderqrsystem.com/api/v1
- **Documentación API**: https://docs.orderqrsystem.com

### Versiones
- **Laravel Backend**: 10.x
- **Flutter App**: Target SDK 2.19+
- **Min Android SDK**: 24 (Android 7.0)
- **Min iOS**: 12.0

---

## 14. Guía de Diseño Visual - Coherencia con Volt Dashboard

### 14.1 Paleta de Colores

Para mantener coherencia con Volt Dashboard, usar los siguientes colores en Flutter:

```dart
class AppColors {
  // Primary Colors (Bootstrap 5 / Volt)
  static const Color primary = Color(0xFF262B40);        // Azul oscuro principal
  static const Color secondary = Color(0xFF6C757D);      // Gris
  static const Color success = Color(0xFF28A745);        // Verde
  static const Color danger = Color(0xFFDC3545);         // Rojo
  static const Color warning = Color(0xFFFFC107);        // Amarillo/Naranja
  static const Color info = Color(0xFF17A2B8);           // Azul claro

  // Gray Scale
  static const Color gray100 = Color(0xFFF8F9FA);
  static const Color gray200 = Color(0xFFE9ECEF);
  static const Color gray300 = Color(0xFFDEE2E6);
  static const Color gray400 = Color(0xFFCED4DA);
  static const Color gray500 = Color(0xFFADB5BD);
  static const Color gray600 = Color(0xFF6C757D);
  static const Color gray700 = Color(0xFF495057);
  static const Color gray800 = Color(0xFF343A40);
  static const Color gray900 = Color(0xFF212529);

  // Background
  static const Color background = Color(0xFFF5F5F5);
  static const Color cardBackground = Color(0xFFFFFFFF);

  // Text Colors
  static const Color textPrimary = Color(0xFF212529);
  static const Color textSecondary = Color(0xFF6C757D);
  static const Color textMuted = Color(0xFF8898AA);
}
```

### 14.2 Tipografía

Usar la misma fuente que Volt Dashboard:

```dart
class AppTextStyles {
  // Font Family: Nunito Sans (similar a Volt)
  static const String fontFamily = 'Nunito Sans';

  // Headings
  static const TextStyle h1 = TextStyle(
    fontSize: 32,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    fontFamily: fontFamily,
  );

  static const TextStyle h2 = TextStyle(
    fontSize: 28,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    fontFamily: fontFamily,
  );

  static const TextStyle h3 = TextStyle(
    fontSize: 24,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    fontFamily: fontFamily,
  );

  static const TextStyle h4 = TextStyle(
    fontSize: 20,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    fontFamily: fontFamily,
  );

  static const TextStyle h5 = TextStyle(
    fontSize: 18,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    fontFamily: fontFamily,
  );

  static const TextStyle h6 = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    fontFamily: fontFamily,
  );

  // Body Text
  static const TextStyle body1 = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.normal,
    color: AppColors.textPrimary,
    fontFamily: fontFamily,
  );

  static const TextStyle body2 = TextStyle(
    fontSize: 14,
    fontWeight: FontWeight.normal,
    color: AppColors.textPrimary,
    fontFamily: fontFamily,
  );

  static const TextStyle caption = TextStyle(
    fontSize: 12,
    fontWeight: FontWeight.normal,
    color: AppColors.textSecondary,
    fontFamily: fontFamily,
  );

  // Buttons
  static const TextStyle button = TextStyle(
    fontSize: 14,
    fontWeight: FontWeight.w600,
    fontFamily: fontFamily,
  );
}
```

### 14.3 Componentes de Diseño

#### A) Cards (Similar a Volt)

```dart
class VoltCard extends StatelessWidget {
  final Widget child;
  final EdgeInsets? padding;
  final double? elevation;

  const VoltCard({
    Key? key,
    required this.child,
    this.padding,
    this.elevation,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.cardBackground,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: Offset(0, 2),
          ),
        ],
      ),
      padding: padding ?? EdgeInsets.all(16),
      child: child,
    );
  }
}
```

#### B) Buttons (Estilo Bootstrap/Volt)

```dart
class VoltButton extends StatelessWidget {
  final String text;
  final VoidCallback onPressed;
  final Color? backgroundColor;
  final Color? textColor;
  final IconData? icon;
  final bool isOutlined;
  final bool isLoading;

  const VoltButton({
    Key? key,
    required this.text,
    required this.onPressed,
    this.backgroundColor,
    this.textColor,
    this.icon,
    this.isOutlined = false,
    this.isLoading = false,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final bgColor = backgroundColor ?? AppColors.primary;
    final txtColor = textColor ?? Colors.white;

    return ElevatedButton(
      onPressed: isLoading ? null : onPressed,
      style: ElevatedButton.styleFrom(
        backgroundColor: isOutlined ? Colors.transparent : bgColor,
        foregroundColor: isOutlined ? bgColor : txtColor,
        padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
          side: isOutlined
              ? BorderSide(color: bgColor, width: 1.5)
              : BorderSide.none,
        ),
        elevation: isOutlined ? 0 : 2,
      ),
      child: isLoading
          ? SizedBox(
              height: 20,
              width: 20,
              child: CircularProgressIndicator(
                strokeWidth: 2,
                valueColor: AlwaysStoppedAnimation<Color>(txtColor),
              ),
            )
          : Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                if (icon != null) ...[
                  Icon(icon, size: 18),
                  SizedBox(width: 8),
                ],
                Text(text, style: AppTextStyles.button),
              ],
            ),
    );
  }
}

// Uso:
VoltButton(
  text: 'Confirmar',
  icon: Icons.check,
  backgroundColor: AppColors.success,
  onPressed: () {},
)

VoltButton(
  text: 'Cancelar',
  isOutlined: true,
  backgroundColor: AppColors.secondary,
  onPressed: () {},
)
```

#### C) Status Badges (Similar a Volt)

```dart
class VoltBadge extends StatelessWidget {
  final String text;
  final Color backgroundColor;
  final IconData? icon;

  const VoltBadge({
    Key? key,
    required this.text,
    required this.backgroundColor,
    this.icon,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: backgroundColor.withOpacity(0.1),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: backgroundColor.withOpacity(0.3),
          width: 1,
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (icon != null) ...[
            Icon(icon, size: 14, color: backgroundColor),
            SizedBox(width: 6),
          ],
          Text(
            text,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: backgroundColor,
            ),
          ),
        ],
      ),
    );
  }
}

// Uso para estados de orden:
VoltBadge(
  text: 'Pendiente',
  backgroundColor: AppColors.warning,
  icon: Icons.schedule,
)

VoltBadge(
  text: 'Listo',
  backgroundColor: AppColors.success,
  icon: Icons.check_circle,
)
```

#### D) Order Card (Diseño similar a la web)

```dart
class OrderCard extends StatelessWidget {
  final Order order;
  final VoidCallback onTap;

  const OrderCard({
    Key? key,
    required this.order,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return VoltCard(
      padding: EdgeInsets.all(16),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header: Folio + Status
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  order.folioNumber,
                  style: AppTextStyles.h5,
                ),
                VoltBadge(
                  text: _getStatusLabel(order.status),
                  backgroundColor: _getStatusColor(order.status),
                  icon: _getStatusIcon(order.status),
                ),
              ],
            ),
            SizedBox(height: 12),

            // Descripción
            Text(
              order.description,
              style: AppTextStyles.body2.copyWith(
                color: AppColors.textSecondary,
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
            SizedBox(height: 12),

            // Footer: Business + Time
            Row(
              children: [
                Icon(Icons.store, size: 16, color: AppColors.textMuted),
                SizedBox(width: 6),
                Expanded(
                  child: Text(
                    order.businessName,
                    style: AppTextStyles.caption,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                SizedBox(width: 12),
                Icon(Icons.access_time, size: 16, color: AppColors.textMuted),
                SizedBox(width: 6),
                Text(
                  _formatTime(order.createdAt),
                  style: AppTextStyles.caption,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'pending':
        return AppColors.warning;
      case 'ready':
        return AppColors.success;
      case 'delivered':
        return AppColors.info;
      case 'cancelled':
        return AppColors.danger;
      default:
        return AppColors.secondary;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status) {
      case 'pending':
        return Icons.schedule;
      case 'ready':
        return Icons.check_circle;
      case 'delivered':
        return Icons.done_all;
      case 'cancelled':
        return Icons.cancel;
      default:
        return Icons.info;
    }
  }

  String _getStatusLabel(String status) {
    switch (status) {
      case 'pending':
        return 'Pendiente';
      case 'ready':
        return 'Listo';
      case 'delivered':
        return 'Entregado';
      case 'cancelled':
        return 'Cancelado';
      default:
        return status;
    }
  }

  String _formatTime(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    if (difference.inMinutes < 60) {
      return 'Hace ${difference.inMinutes}m';
    } else if (difference.inHours < 24) {
      return 'Hace ${difference.inHours}h';
    } else {
      return '${dateTime.day}/${dateTime.month}/${dateTime.year}';
    }
  }
}
```

#### E) Timeline Component (Como en la web)

```dart
class OrderTimeline extends StatelessWidget {
  final Order order;

  const OrderTimeline({Key? key, required this.order}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Historial de Estados', style: AppTextStyles.h6),
        SizedBox(height: 16),
        _buildTimelineItem(
          'Orden Creada',
          order.createdAt,
          AppColors.success,
          true,
        ),
        if (order.readyAt != null)
          _buildTimelineItem(
            'Orden Lista',
            order.readyAt!,
            AppColors.success,
            true,
          ),
        if (order.deliveredAt != null)
          _buildTimelineItem(
            'Orden Entregada',
            order.deliveredAt!,
            AppColors.info,
            true,
          ),
        if (order.cancelledAt != null)
          _buildTimelineItem(
            'Orden Cancelada',
            order.cancelledAt!,
            AppColors.danger,
            true,
          ),
      ],
    );
  }

  Widget _buildTimelineItem(
    String title,
    DateTime timestamp,
    Color color,
    bool isCompleted,
  ) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Column(
          children: [
            Container(
              width: 32,
              height: 32,
              decoration: BoxDecoration(
                color: isCompleted ? color : AppColors.gray300,
                shape: BoxShape.circle,
              ),
              child: Icon(
                Icons.check,
                size: 18,
                color: Colors.white,
              ),
            ),
            if (title != 'Orden Entregada' && title != 'Orden Cancelada')
              Container(
                width: 2,
                height: 40,
                color: AppColors.gray300,
              ),
          ],
        ),
        SizedBox(width: 16),
        Expanded(
          child: Padding(
            padding: EdgeInsets.only(top: 4),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: AppTextStyles.body1),
                SizedBox(height: 4),
                Text(
                  _formatDateTime(timestamp),
                  style: AppTextStyles.caption,
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  String _formatDateTime(DateTime dateTime) {
    return '${dateTime.day}/${dateTime.month}/${dateTime.year} ${dateTime.hour}:${dateTime.minute.toString().padLeft(2, '0')}';
  }
}
```

### 14.4 Espaciado y Dimensiones (Volt Style)

```dart
class AppSpacing {
  static const double xs = 4.0;
  static const double sm = 8.0;
  static const double md = 16.0;
  static const double lg = 24.0;
  static const double xl = 32.0;
  static const double xxl = 48.0;

  // Border Radius
  static const double radiusSmall = 6.0;
  static const double radiusMedium = 8.0;
  static const double radiusLarge = 12.0;
  static const double radiusRound = 50.0;

  // Shadows (similar a Volt)
  static List<BoxShadow> cardShadow = [
    BoxShadow(
      color: Colors.black.withOpacity(0.05),
      blurRadius: 10,
      offset: Offset(0, 2),
    ),
  ];

  static List<BoxShadow> elevatedShadow = [
    BoxShadow(
      color: Colors.black.withOpacity(0.1),
      blurRadius: 20,
      offset: Offset(0, 4),
    ),
  ];
}
```

### 14.5 Iconografía

Usar iconos consistentes con Volt Dashboard:

```dart
class AppIcons {
  // Estados de orden
  static const IconData pending = Icons.schedule;
  static const IconData ready = Icons.check_circle;
  static const IconData delivered = Icons.done_all;
  static const IconData cancelled = Icons.cancel;

  // Navegación
  static const IconData home = Icons.home_outlined;
  static const IconData orders = Icons.receipt_long_outlined;
  static const IconData qrScan = Icons.qr_code_scanner;
  static const IconData settings = Icons.settings_outlined;

  // Acciones
  static const IconData phone = Icons.phone;
  static const IconData location = Icons.location_on_outlined;
  static const IconData info = Icons.info_outlined;
  static const IconData close = Icons.close;
  static const IconData back = Icons.arrow_back;
}
```

### 14.6 Theme Configuration (Flutter)

```dart
ThemeData voltTheme() {
  return ThemeData(
    primaryColor: AppColors.primary,
    scaffoldBackgroundColor: AppColors.background,
    fontFamily: AppTextStyles.fontFamily,

    colorScheme: ColorScheme.light(
      primary: AppColors.primary,
      secondary: AppColors.secondary,
      error: AppColors.danger,
      background: AppColors.background,
      surface: AppColors.cardBackground,
    ),

    appBarTheme: AppBarTheme(
      backgroundColor: AppColors.primary,
      elevation: 0,
      centerTitle: true,
      titleTextStyle: AppTextStyles.h5.copyWith(color: Colors.white),
      iconTheme: IconThemeData(color: Colors.white),
    ),

    cardTheme: CardTheme(
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppSpacing.radiusLarge),
      ),
      color: AppColors.cardBackground,
    ),

    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        padding: EdgeInsets.symmetric(
          horizontal: AppSpacing.lg,
          vertical: AppSpacing.md,
        ),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppSpacing.radiusMedium),
        ),
        elevation: 2,
      ),
    ),

    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: Colors.white,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppSpacing.radiusMedium),
        borderSide: BorderSide(color: AppColors.gray300),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppSpacing.radiusMedium),
        borderSide: BorderSide(color: AppColors.gray300),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppSpacing.radiusMedium),
        borderSide: BorderSide(color: AppColors.primary, width: 2),
      ),
      contentPadding: EdgeInsets.all(AppSpacing.md),
    ),
  );
}
```

### 14.7 Pantallas de Ejemplo con Estilo Volt

#### Home Screen (Lista de Órdenes)

```dart
class HomeScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Mis Órdenes'),
        actions: [
          IconButton(
            icon: Icon(Icons.notifications_outlined),
            onPressed: () {},
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          // Refrescar órdenes
        },
        child: ListView(
          padding: EdgeInsets.all(AppSpacing.md),
          children: [
            // Header con estadísticas
            VoltCard(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: [
                  _buildStat('Activas', '3', AppColors.warning),
                  _buildStat('Listas', '1', AppColors.success),
                  _buildStat('Total', '12', AppColors.info),
                ],
              ),
            ),
            SizedBox(height: AppSpacing.lg),

            // Título de sección
            Text('Órdenes Activas', style: AppTextStyles.h5),
            SizedBox(height: AppSpacing.md),

            // Lista de órdenes
            OrderCard(order: order1, onTap: () {}),
            SizedBox(height: AppSpacing.md),
            OrderCard(order: order2, onTap: () {}),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          // Escanear nuevo QR
        },
        icon: Icon(Icons.qr_code_scanner),
        label: Text('Escanear QR'),
        backgroundColor: AppColors.primary,
      ),
    );
  }

  Widget _buildStat(String label, String value, Color color) {
    return Column(
      children: [
        Text(
          value,
          style: AppTextStyles.h3.copyWith(color: color),
        ),
        SizedBox(height: 4),
        Text(label, style: AppTextStyles.caption),
      ],
    );
  }
}
```

### 14.8 Animaciones (Sutiles como Volt)

```dart
class AppAnimations {
  static const Duration fast = Duration(milliseconds: 150);
  static const Duration normal = Duration(milliseconds: 300);
  static const Duration slow = Duration(milliseconds: 500);

  static const Curve defaultCurve = Curves.easeInOut;

  // Fade In
  static Widget fadeIn(Widget child, {Duration? duration}) {
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: duration ?? normal,
      curve: defaultCurve,
      builder: (context, value, child) {
        return Opacity(opacity: value, child: child);
      },
      child: child,
    );
  }

  // Slide In
  static Widget slideIn(Widget child, {Duration? duration}) {
    return TweenAnimationBuilder<Offset>(
      tween: Tween(begin: Offset(0, 0.1), end: Offset.zero),
      duration: duration ?? normal,
      curve: defaultCurve,
      builder: (context, value, child) {
        return Transform.translate(offset: value, child: child);
      },
      child: child,
    );
  }
}
```

---

## 15. Notas Finales

### Características Opcionales (Módulo de Chat)
Si el negocio tiene activado `has_chat_module`:
- Implementar chat en tiempo real (WebSockets o Firebase Realtime Database)
- Botón de chat visible en el detalle de orden
- Notificaciones de nuevos mensajes

### Mejoras Futuras
- Historial completo de órdenes
- Calificación del servicio
- Compartir orden con otro usuario
- Pago desde la app
- Orden programada (pre-order)

---

**Documento creado el**: 2025-11-05
**Versión del sistema**: 1.0.1
**Última actualización**: 2025-11-05
