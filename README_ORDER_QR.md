# 📱 Order QR System

Sistema de gestión de órdenes con códigos QR que conecta negocios con sus clientes mediante una aplicación web (Laravel) y una aplicación móvil (Flutter).

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![License](https://img.shields.io/badge/license-MIT-green)

---

## 🎯 Características

### Panel Web (Laravel)
- ✅ Gestión completa de órdenes
- ✅ Panel de SuperAdministrador
- ✅ Panel de Negocios
- ✅ Generación automática de códigos QR
- ✅ Sistema de pagos con Stripe
- ✅ Notificaciones push (Firebase FCM)
- ✅ Chat en tiempo real
- ✅ Dashboard con métricas
- ✅ Gestión de planes y suscripciones

### API REST (Móvil)
- ✅ Registro de dispositivos
- ✅ Asociación de órdenes por QR
- ✅ Consulta de órdenes en tiempo real
- ✅ Historial de órdenes
- ✅ Notificaciones push

### App Móvil (Flutter)
- ✅ Escaneo de códigos QR
- ✅ Seguimiento de órdenes
- ✅ Notificaciones en tiempo real
- ✅ Historial de pedidos
- ✅ Interfaz intuitiva

---

## 🚀 Inicio Rápido

### Requisitos

- PHP 8.1 o superior
- Composer
- MySQL 5.7+ o MariaDB
- Laragon o XAMPP
- Git

### Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/TU_USUARIO/order-qr-system.git
cd order-qr-system

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
# Editar DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Migrar base de datos
php artisan migrate

# 6. Seeders (datos de prueba)
php artisan db:seed --class=SuperAdminSeeder

# 7. Link de storage
php artisan storage:link

# 8. Iniciar servidor
php artisan serve
```

Visita: `http://localhost:8000`

📖 **[Ver guía de instalación completa →](INSTALACION.md)**

---

## 🔐 Credenciales de Prueba

### SuperAdministrador
- **URL:** `/superadmin/login` o `/business/login`
- **Email:** `admin@example.com`
- **Password:** `password`

### Negocio
- **URL:** `/business/login`
- **Email:** `test@example.com`
- **Password:** `password123`

---

## 📱 Configuración con App Móvil

### Desarrollo Remoto con ngrok (RECOMENDADO)

```bash
# 1. Instalar ngrok
# Descargar de: https://ngrok.com/download

# 2. Configurar authtoken
ngrok config add-authtoken TU_TOKEN

# 3. Reservar dominio gratuito en: https://dashboard.ngrok.com/cloud-edge/domains

# 4. Iniciar túnel
ngrok http 8000 --domain=tu-dominio.ngrok-free.dev
```

En la app Flutter:
```dart
static const String baseUrl = "https://tu-dominio.ngrok-free.dev/api/v1";
```

📖 **[Ver guía completa de ngrok →](NGROK_SETUP.md)**

---

## 📂 Estructura del Proyecto

```
order-qr-system/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/V1/          # API para móvil
│   │   ├── SuperAdmin/      # Panel SuperAdmin
│   │   └── ...
│   ├── Models/              # Modelos Eloquent
│   └── ...
├── database/
│   ├── migrations/          # Migraciones de BD
│   └── seeders/            # Datos de prueba
├── routes/
│   ├── web.php             # Rutas web
│   └── api.php             # Rutas API
├── storage/
│   └── app/qr_codes/       # QR generados
└── ...
```

---

## 🌐 API Endpoints

### Autenticación Móvil

```
POST   /api/v1/mobile/register           # Registrar dispositivo
POST   /api/v1/mobile/orders/associate   # Asociar orden con QR
GET    /api/v1/mobile/orders              # Listar órdenes
GET    /api/v1/mobile/orders/{id}        # Detalle de orden
PUT    /api/v1/mobile/update-token       # Actualizar FCM token
```

📖 **[Ver documentación completa de la API →](MOBILE_API_README.md)**

---

## 🛠️ Tecnologías

- **Backend:** Laravel 12.x, PHP 8.1+
- **Base de Datos:** MySQL 5.7+
- **Template:** Volt Dashboard (Bootstrap 5)
- **Pagos:** Stripe
- **Notificaciones:** Firebase Cloud Messaging (FCM API v1)
- **QR Generator:** SimpleSoftwareIO/simple-qrcode
- **Móvil:** Flutter + Dart

---

## 🔄 Flujo del Sistema

1. **Negocio** crea orden → Se genera QR automáticamente
2. **Cliente** escanea QR → Orden se asocia al dispositivo
3. **Negocio** marca como lista → Push notification al cliente
4. **Cliente** muestra QR → **Negocio** escanea → Marca como entregada

---

## 📖 Documentación

- **[Instalación Completa](INSTALACION.md)** - Guía paso a paso
- **[Configuración de ngrok](NGROK_SETUP.md)** - Desarrollo remoto
- **[API Móvil](MOBILE_API_README.md)** - Endpoints y ejemplos
- **[Especificaciones](MOBILE_APP_SPECIFICATIONS.md)** - Arquitectura
- **[Configurar App Móvil](CONFIGURAR_APP_MOVIL.md)** - Setup de Flutter
- **[Inicio Rápido](EMPIEZA_AQUI.txt)** - Guía ultra rápida

---

## 🤝 Contribuir

Las contribuciones son bienvenidas! Por favor:

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT.

---

## 👨‍💻 Autor

**CETAM - Centro de Desarrollo Tecnológico Aplicado de México**

---

**Made with ❤️ by CETAM**
