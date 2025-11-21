# Order QR System - CETAM

**Centro de Servicios (CS) - Sistema de Órdenes con QR**

Sistema de gestión de órdenes con código QR para negocios, integrado con aplicación móvil, notificaciones push, chat en tiempo real y pagos con MercadoPago.

---

## 🚀 Características

- ✅ **Generación automática de códigos QR** para cada orden
- ✅ **Aplicación móvil** (Flutter) para usuarios
- ✅ **Notificaciones push** en tiempo real (Firebase)
- ✅ **Chat integrado** entre negocio y cliente
- ✅ **Sistema de pagos** con MercadoPago
- ✅ **Dashboard con análisis** y reportes
- ✅ **Múltiples planes** de suscripción
- ✅ **Cumplimiento estándares CETAM** v3.0

---

## 📋 Requisitos

- **PHP:** 8.2.x o superior (actual: 8.3.26) ✓
- **Composer:** 2.8.x o superior ✓
- **Node.js:** 22.x (actual: 22.20.0) ✓
- **MySQL:** 5.7+ o MariaDB 10.3+
- **Laravel:** 12.x (actual: 12.36.1) ✓

---

## 🛠️ Instalación Rápida

### 1. Clonar el Repositorio

```bash
git clone <url-del-repositorio>
cd volt-laravel-dashboard-1.0.1-main
```

### 2. Instalar Dependencias

```bash
# Dependencias PHP
composer install

# Dependencias JavaScript
npm install
```

### 3. Configurar Entorno

```bash
# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar Base de Datos

Editar `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=volt_dashboard
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Ejecutar Migraciones

```bash
# Crear tablas
php artisan migrate

# Cargar datos iniciales (opcional)
php artisan db:seed
```

### 6. Compilar Assets

```bash
# Desarrollo (con watch)
npm run dev

# Producción
npm run build
```

### 7. Iniciar Servidor

```bash
php artisan serve
```

Accede a: http://localhost:8000

---

## 📚 Documentación

### Documentación Principal

- **[RESUMEN_IMPLEMENTACION_CETAM.md](RESUMEN_IMPLEMENTACION_CETAM.md)** - Resumen completo de la implementación
- **[GUIA_ESTANDARES_CETAM.md](GUIA_ESTANDARES_CETAM.md)** - Guía de uso de estándares
- **[CABECERAS_CETAM.md](CABECERAS_CETAM.md)** - Plantillas de cabeceras
- **[INSTRUCCIONES_IMPLEMENTACION_LARAVEL_CETAM.md](INSTRUCCIONES_IMPLEMENTACION_LARAVEL_CETAM.md)** - Manual completo

### Documentación Antigua

Toda la documentación de versiones anteriores se encuentra en `_docs/old-documentation/`

---

## 🎨 Componentes CETAM

### Componente Icon

Uso de iconos estandarizados con Font Awesome:

```blade
{{-- Básico --}}
<x-icon name="user" />
<x-icon name="qrcode" />
<x-icon name="order" />

{{-- En botones --}}
<button class="btn btn-primary">
    <x-icon name="save" /> Guardar
</button>

{{-- Con clases CSS --}}
<x-icon name="success" class="text-success fs-4" />
```

**Iconos disponibles:** Ver `config/icons.php` (60+ iconos)

### Componente Alert

Sistema de alertas estandarizado:

```blade
{{-- Diferentes tipos --}}
<x-cs-alert type="success" message="¡Operación exitosa!" />
<x-cs-alert type="error" message="Error al procesar" />
<x-cs-alert type="warning" message="Advertencia" />
<x-cs-alert type="info" message="Información" />

{{-- Con sesión flash --}}
@if(session('success'))
    <x-cs-alert type="success" :message="session('success')" />
@endif
```

### Configuración CETAM

Acceso a configuración institucional:

```php
// Paginación
$perPage = config('cetam.cs.pagination.per_page'); // 15

// Features
$chatEnabled = config('cetam.cs.features.chat'); // true

// Nombre del proyecto
$projectName = config('cetam.cs.name');
```

---

## 🗂️ Estructura del Proyecto

```
volt-laravel-dashboard/
├── app/
│   ├── Http/Controllers/
│   │   ├── CS/                    # Controladores CETAM
│   │   │   ├── DashboardController.php
│   │   │   └── OrderController.php
│   │   ├── BusinessController.php
│   │   ├── ChatController.php
│   │   ├── OrderController.php
│   │   └── PaymentController.php
│   ├── Models/
│   │   ├── Order.php             # Modelo de órdenes con QR
│   │   ├── Business.php          # Modelo de negocios
│   │   └── User.php
│   ├── Services/
│   │   ├── OrderService.php      # Lógica de negocios
│   │   ├── PaymentService.php
│   │   └── PushNotificationService.php
│   └── View/Components/
│       ├── Icon.php              # Componente de iconos
│       └── CS/
│           └── Alert.php         # Componente de alertas
├── config/
│   ├── cetam.cs.php              # Configuración CETAM
│   └── icons.php                 # Catálogo de iconos
├── resources/
│   ├── views/
│   │   ├── components/           # Vistas de componentes
│   │   ├── dashboard/
│   │   ├── orders/
│   │   └── business/
│   ├── sass/
│   └── js/
└── routes/
    ├── web.php
    └── api.php
```

---

## 🔐 Credenciales de Prueba

### Usuario Business (Negocio)

```
Email: test@business.com
Password: password
```

### Super Admin

```
Email: admin@cetam.mx
Password: cetam2025
```

---

## ⚙️ Configuraciones Importantes

### MercadoPago (Pagos)

En `.env`:

```env
MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
MERCADOPAGO_MODE=sandbox
```

### Firebase (Notificaciones Push)

1. Descargar credenciales JSON desde Firebase Console
2. Guardar en `storage/firebase-credentials.json`
3. Configurar en `.env`:

```env
FIREBASE_CREDENTIALS_PATH=storage/firebase-credentials.json
```

### Google Maps (Opcional)

```env
GOOGLE_MAPS_API_KEY=tu_api_key
```

---

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Con coverage
php artisan test --coverage
```

---

## 📱 App Móvil

La aplicación móvil Flutter se encuentra en un repositorio separado.

**Características:**
- Escaneo de códigos QR
- Notificaciones push
- Chat en tiempo real
- Historial de órdenes
- Diseño con Volt Dashboard

---

## 🔄 Comandos Útiles

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Regenerar autoload
composer dump-autoload

# Ver configuración
php artisan tinker
>>> config('cetam.cs')
>>> config('icons.icons')

# Compilar assets
npm run dev      # Desarrollo con watch
npm run build    # Producción
```

---

## 🐛 Resolución de Problemas

### Error de permisos en storage

```bash
chmod -R 775 storage bootstrap/cache
```

### Caché de configuración

```bash
php artisan config:cache
php artisan route:cache
```

### Regenerar assets

```bash
rm -rf node_modules public/build
npm install
npm run build
```

---

## 📄 Licencia

Propietario - CETAM © 2025

---

## 👥 Equipo

**Desarrollado por:** CETAM Dev Team

**Proyecto:** Centro de Servicios (CS)

**Versión:** 1.0.0

---

## 🔗 Enlaces Útiles

- [Manual de Estándares CETAM](INSTRUCCIONES_IMPLEMENTACION_LARAVEL_CETAM.md)
- [Guía de Componentes](GUIA_ESTANDARES_CETAM.md)
- [Resumen de Implementación](RESUMEN_IMPLEMENTACION_CETAM.md)
- [Laravel Documentation](https://laravel.com/docs)
- [Volt Dashboard](https://themesberg.com/product/admin-dashboard/volt-bootstrap-5-dashboard)

---

## 📞 Soporte

Para soporte técnico o consultas:
- Email: soporte@cetam.mx
- Documentación: Ver archivos MD en el proyecto

---

**¡Gracias por usar Order QR System!** 🎉
