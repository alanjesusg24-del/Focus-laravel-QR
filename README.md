# 📱 Order QR System

> Sistema de gestión de órdenes con códigos QR - CETAM 2025

Sistema completo de gestión de órdenes basado en códigos QR, desarrollado con Laravel 12 y Volt Dashboard, diseñado para negocios que necesitan gestionar órdenes de forma eficiente mediante escaneo de códigos QR.

[![Laravel](https://img.shields.io/badge/Laravel-12.36-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)
[![CETAM](https://img.shields.io/badge/CETAM-2025-blue.svg)](https://cetam.mx)

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Tecnologías](#-tecnologías)
- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Base de Datos](#-base-de-datos)
- [Usuarios de Prueba](#-usuarios-de-prueba)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Estándares CETAM](#-estándares-cetam)
- [Comandos Útiles](#-comandos-útiles)
- [Troubleshooting](#-troubleshooting)
- [Contribuir](#-contribuir)
- [Licencia](#-licencia)

---

## ✨ Características

### 🏢 Panel de Negocios
- ✅ Registro y autenticación de negocios
- ✅ Gestión completa de órdenes con QR
- ✅ Dashboard con estadísticas en tiempo real
- ✅ Sistema de pagos con MercadoPago
- ✅ Perfiles personalizables con ubicación en mapa
- ✅ Chat en tiempo real (módulo opcional)
- ✅ Sistema de re-alertas para órdenes
- ✅ Gestión de tickets de soporte

### 👨‍💼 Panel de SuperAdmin
- ✅ Gestión de negocios registrados
- ✅ Administración de planes y precios
- ✅ Monitoreo de pagos y suscripciones
- ✅ Dashboard con métricas globales
- ✅ Gestión de tickets de soporte

### 🎨 Frontend Estandarizado
- ✅ Plantilla Volt Dashboard Bootstrap 5
- ✅ Sistema de iconos estandarizado (Font Awesome 6)
- ✅ Paleta de colores CETAM institucional
- ✅ Componente Blade `<x-icon>` centralizado
- ✅ Diseño responsive y accesible
- ✅ Notificaciones con SweetAlert2 y Notyf

### 🔧 Funcionalidades Técnicas
- ✅ Generación automática de códigos QR
- ✅ Multi-autenticación (Business y SuperAdmin)
- ✅ API REST para integración móvil
- ✅ Firebase Cloud Messaging (notificaciones push)
- ✅ Google Maps API (ubicación de negocios)
- ✅ Sistema de subscripciones con planes personalizables
- ✅ Retención de datos configurable por plan

---

## 🛠 Tecnologías

### Backend
- **Laravel 12.36** - Framework PHP
- **PHP 8.2+** - Lenguaje de programación
- **MySQL** - Base de datos relacional
- **Livewire 3.5** - Componentes reactivos

### Frontend
- **Volt Dashboard** - Plantilla administrativa Bootstrap 5
- **Bootstrap 5.3** - Framework CSS
- **Font Awesome 6.4** - Iconos (Solid)
- **SweetAlert2** - Modales de confirmación
- **Notyf** - Notificaciones toast
- **Chart.js** - Gráficos y estadísticas
- **Google Maps API** - Mapas interactivos

### Integraciones
- **MercadoPago SDK** - Pagos online
- **Firebase PHP SDK** - Notificaciones push
- **SimpleSoftwareIO QR Code** - Generación de códigos QR
- **Laravel Sanctum** - Autenticación API

---

## 💻 Requisitos del Sistema

### Software Requerido

| Software | Versión Mínima | Recomendada |
|----------|----------------|-------------|
| **PHP** | 8.2 | 8.3+ |
| **Composer** | 2.0 | 2.7+ |
| **Node.js** | 20.x | 22.x |
| **npm** | 9.x | 10.x |
| **MySQL** | 8.0 | 8.0+ |
| **Git** | 2.0 | 2.44+ |

### Extensiones PHP Requeridas
```bash
php -m | grep -E 'pdo|mysql|mbstring|xml|bcmath|json|openssl|tokenizer|fileinfo|gd'
```

**Extensiones necesarias:**
- PDO
- pdo_mysql
- mbstring
- xml
- bcmath
- json
- openssl
- tokenizer
- fileinfo
- gd (para generación de QR)

### Servicios Externos (Opcionales)
- **Google Maps API** - Para ubicación de negocios
- **MercadoPago** - Para procesamiento de pagos
- **Firebase** - Para notificaciones push

---

## 🚀 Instalación

### Paso 1: Clonar el Repositorio

```bash
# Clonar el proyecto
git clone https://github.com/tu-usuario/order-qr-system.git
cd order-qr-system
```

### Paso 2: Instalar Dependencias de PHP

```bash
# Instalar dependencias de Composer
composer install
```

Si encuentras errores, prueba:
```bash
composer install --ignore-platform-reqs
composer update
```

### Paso 3: Instalar Dependencias de Node.js

```bash
# Instalar dependencias de npm
npm install

# Si usas Node.js 22+
npm install --legacy-peer-deps
```

### Paso 4: Configurar Variables de Entorno

```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### Paso 5: Configurar Base de Datos

Edita el archivo `.env` con tus credenciales de MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=order_qr_system
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

### Paso 6: Crear Base de Datos

```bash
# Opción 1: Desde MySQL CLI
mysql -u root -p
CREATE DATABASE order_qr_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Opción 2: Desde artisan (si tienes permisos)
php artisan db:create order_qr_system
```

### Paso 7: Ejecutar Migraciones y Seeders

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (datos de prueba)
php artisan db:seed
```

**⚠️ IMPORTANTE:** Los seeders crearán:
- 2 SuperAdmins de prueba
- 3 Planes de suscripción
- 5 Negocios de prueba
- 20+ Órdenes de ejemplo
- Pagos y tickets de soporte

### Paso 8: Crear Enlace Simbólico de Storage

```bash
php artisan storage:link
```

### Paso 9: Compilar Assets

```bash
# Desarrollo
npm run dev

# Producción
npm run production
```

### Paso 10: Iniciar Servidor

```bash
# Servidor de desarrollo Laravel
php artisan serve

# La aplicación estará disponible en: http://127.0.0.1:8000
```

---

## ⚙️ Configuración

### Variables de Entorno Principales

#### Configuración Básica
```env
APP_NAME="Order QR System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

#### Google Maps API (Opcional)
```env
GOOGLE_MAPS_API_KEY=tu_api_key_aqui
```

**Obtener API Key:**
1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un proyecto nuevo
3. Habilita "Maps JavaScript API" y "Geocoding API"
4. Crea credenciales (API Key)
5. Copia la clave en `.env`

#### MercadoPago (Opcional - para pagos)
```env
# Credenciales de prueba (sandbox)
MERCADOPAGO_PUBLIC_KEY=TEST-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=TEST-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxxxxxxxx-xxxxxxxx
MERCADOPAGO_MODE=sandbox
```

**Obtener credenciales:**
1. Regístrate en [MercadoPago Developers](https://www.mercadopago.com.mx/developers/)
2. Ve a "Credenciales" en tu cuenta
3. Copia las credenciales de prueba (TEST)
4. Para producción, usa las credenciales reales

#### Firebase (Opcional - para notificaciones push)
```env
FIREBASE_CREDENTIALS=path/to/firebase-credentials.json
```

### Configuración de Email (Opcional)

Para notificaciones por email:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@orderqr.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Servicios recomendados para pruebas:**
- [Mailtrap](https://mailtrap.io/) - Email testing
- [MailHog](https://github.com/mailhog/MailHog) - Local SMTP server

---

## 🗄️ Base de Datos

### Estructura Principal

#### Tablas Core
- `businesses` - Negocios registrados
- `orders` - Órdenes generadas
- `plans` - Planes de suscripción
- `payments` - Historial de pagos
- `super_admins` - Administradores del sistema

#### Tablas Adicionales
- `users` - Usuarios móviles (clientes finales)
- `mobile_devices` - Dispositivos registrados
- `order_items` - Items de cada orden
- `order_status_history` - Historial de estados
- `order_realerts` - Re-alertas de órdenes
- `chat_messages` - Mensajes de chat
- `support_tickets` - Tickets de soporte
- `notifications` - Notificaciones push

### Diagrama de Relaciones

```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│   plans     │────────<│ businesses  │────────<│   orders    │
└─────────────┘         └─────────────┘         └─────────────┘
                              │                        │
                              │                        │
                              ▼                        ▼
                        ┌─────────────┐         ┌─────────────┐
                        │  payments   │         │    users    │
                        └─────────────┘         └─────────────┘
```

### Comandos de Base de Datos

```bash
# Resetear base de datos completamente
php artisan migrate:fresh --seed

# Solo migraciones (sin datos)
php artisan migrate:fresh

# Revertir última migración
php artisan migrate:rollback

# Ver estado de migraciones
php artisan migrate:status

# Ejecutar solo un seeder específico
php artisan db:seed --class=SuperAdminSeeder
```

---

## 👥 Usuarios de Prueba

Después de ejecutar `php artisan db:seed`, tendrás acceso a estas cuentas:

### 🔑 SuperAdmin

| Email | Contraseña | Nombre |
|-------|-----------|---------|
| `admin@example.com` | `password` | Super Administrador |
| `superadmin@cetam.mx` | `password123` | CETAM Administrator |

**Acceso:** `http://127.0.0.1:8000/superadmin/login`

### 🏢 Negocios de Prueba

Los seeders crean 5 negocios de ejemplo. Ejemplo:

| Email | Contraseña | Negocio |
|-------|-----------|---------|
| `business1@example.com` | `password` | Cafetería Central |
| `business2@example.com` | `password` | Restaurante El Buen Sabor |

**Acceso:** `http://127.0.0.1:8000/business/login`

### 📋 Planes Disponibles

| Plan | Precio | Chat | Retención | Re-alertas |
|------|--------|------|-----------|------------|
| Básico | $99/mes | ❌ | 30 días | ❌ |
| Profesional | $199/mes | ✅ | 90 días | ✅ |
| Empresarial | $299/mes | ✅ | 365 días | ✅ |

---

## 📁 Estructura del Proyecto

```
order-qr-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/              # Autenticación
│   │   │   ├── Api/               # API REST
│   │   │   ├── SuperAdmin/        # Controladores SuperAdmin
│   │   │   ├── BusinessController.php
│   │   │   ├── OrderController.php
│   │   │   ├── PaymentController.php
│   │   │   └── ...
│   │   └── Middleware/            # Middlewares personalizados
│   ├── Models/                    # Modelos Eloquent
│   ├── View/
│   │   └── Components/            # Componentes Blade
│   │       └── Icon.php           # Componente <x-icon>
│   └── ...
├── config/
│   └── icons.php                  # Catálogo de iconos CETAM
├── database/
│   ├── migrations/                # Migraciones de BD
│   └── seeders/                   # Seeders de datos
├── public/
│   ├── assets/                    # Assets de Volt Dashboard
│   ├── css/                       # CSS compilado
│   │   ├── app.css
│   │   ├── volt.css
│   │   └── cetam-colors.css      # Colores CETAM
│   └── js/                        # JavaScript compilado
├── resources/
│   ├── js/
│   │   └── app.js                # JavaScript principal
│   ├── sass/
│   │   ├── custom/
│   │   │   ├── _variables.scss   # Variables CETAM
│   │   │   └── _custom.scss
│   │   └── app.scss
│   └── views/
│       ├── auth/                 # Vistas de login
│       ├── business/             # Vistas de negocios
│       ├── superadmin/           # Vistas de superadmin
│       ├── layouts/              # Layouts base
│       └── components/           # Componentes Blade
│           └── icon.blade.php    # Vista del componente icon
├── routes/
│   ├── web.php                   # Rutas web
│   └── api.php                   # Rutas API
├── .env.example                  # Plantilla de variables
├── composer.json                 # Dependencias PHP
├── package.json                  # Dependencias Node
├── webpack.mix.js                # Configuración Laravel Mix
├── estandares-frontend-laravel.md # Estándares CETAM
└── README.md                     # Este archivo
```

---

## 🎨 Estándares CETAM

Este proyecto sigue los **Estándares de Frontend CETAM 2025**, documentados en:

📄 [`estandares-frontend-laravel.md`](estandares-frontend-laravel.md)

### Puntos Clave

#### 🎨 Paleta de Colores Institucional
```scss
$primary: #1F2937;      // Gris oscuro slate
$secondary: #FB503B;    // Naranja rojizo vibrante
$tertiary: #31316A;     // Azul índigo oscuro
$success: #10B981;      // Verde
$danger: #EF4444;       // Rojo
$warning: #FBA918;      // Ámbar
$info: #3B82F6;         // Azul
```

#### 🔠 Tipografía
- **Fuente:** Nunito Sans (Google Fonts)
- **Uso:** Obligatorio en toda la aplicación

#### ✨ Sistema de Iconos

**OBLIGATORIO:** Usar solo el componente `<x-icon>` con Font Awesome Solid 6.4

```blade
<!-- ✅ CORRECTO -->
<x-icon name="user" />
<x-icon name="save" class="me-2" />

<!-- ❌ INCORRECTO -->
<i class="fa-solid fa-user"></i>
<svg>...</svg>
```

**Catálogo completo:** [`config/icons.php`](config/icons.php)

#### 🎯 Notificaciones

**SweetAlert2** - Para confirmaciones críticas:
```javascript
Swal.fire({
    title: '¿Eliminar usuario?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    iconColor: '#FBA918',
    showCancelButton: true,
    confirmButtonColor: '#EF4444',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
});
```

**Notyf** - Para notificaciones rápidas:
```javascript
notyf.success('Datos guardados correctamente');
notyf.error('Error al procesar la solicitud');
```

---

## 🔧 Comandos Útiles

### Laravel Artisan

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Listar rutas
php artisan route:list

# Crear nuevo controlador
php artisan make:controller NombreController

# Crear nuevo modelo con migración
php artisan make:model NombreModelo -m

# Crear seeder
php artisan make:seeder NombreSeeder

# Optimizar aplicación (producción)
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### NPM Scripts

```bash
# Desarrollo con watch
npm run watch

# Desarrollo con hot reload
npm run hot

# Producción (minificado)
npm run production

# Ver versión de dependencias
npm list --depth=0
```

### Git Workflow

```bash
# Clonar con submódulos
git clone --recursive https://github.com/tu-usuario/order-qr-system.git

# Crear nueva rama
git checkout -b feature/nueva-funcionalidad

# Commit con estándares
git add .
git commit -m "feat: Descripción del cambio"

# Push a GitHub
git push origin feature/nueva-funcionalidad
```

---

## 🐛 Troubleshooting

### Error: "Class 'App\View\Components\Icon' not found"

**Solución:**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Error: "SQLSTATE[HY000] [1045] Access denied"

**Solución:**
1. Verifica credenciales en `.env`
2. Confirma que MySQL está corriendo
3. Prueba conexión: `mysql -u root -p`

### Error: "npm ERR! peer dependencies"

**Solución:**
```bash
npm install --legacy-peer-deps
```

### Error: "File storage/logs/laravel.log not writable"

**Solución (Windows):**
```bash
# Dar permisos completos a carpetas
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

**Solución (Linux/Mac):**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Error: "The stream or file could not be opened"

**Solución:**
```bash
php artisan cache:clear
chmod -R 775 storage
```

### Assets no se cargan (404)

**Solución:**
```bash
npm run dev
php artisan storage:link
```

### Google Maps no funciona

**Solución:**
1. Verifica que `GOOGLE_MAPS_API_KEY` esté en `.env`
2. Habilita APIs necesarias en Google Cloud Console:
   - Maps JavaScript API
   - Geocoding API
   - Places API

### MercadoPago no procesa pagos

**Solución:**
1. Verifica credenciales en `.env`
2. Confirma que estás usando credenciales TEST (sandbox)
3. Revisa logs: `storage/logs/laravel.log`

---

## 🤝 Contribuir

### Proceso de Contribución

1. **Fork** el repositorio
2. **Crea** una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** tus cambios (`git commit -m 'feat: Add some AmazingFeature'`)
4. **Push** a la rama (`git push origin feature/AmazingFeature`)
5. **Abre** un Pull Request

### Estándares de Commits

Seguimos [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: Nueva funcionalidad
fix: Corrección de bug
docs: Cambios en documentación
style: Cambios de formato (no afectan código)
refactor: Refactorización de código
test: Agregar o corregir tests
chore: Cambios en build o dependencias
```

### Código de Conducta

- Respetar los estándares CETAM
- Usar componente `<x-icon>` para todos los iconos
- Seguir paleta de colores institucional
- Documentar cambios significativos
- Escribir código limpio y legible

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo [`LICENSE.md`](LICENSE.md) para más detalles.

---

## 📞 Soporte

### Documentación

- **Estándares Frontend:** [`estandares-frontend-laravel.md`](estandares-frontend-laravel.md)
- **Implementación:** [`IMPLEMENTACION-ESTANDARES-CETAM.md`](IMPLEMENTACION-ESTANDARES-CETAM.md)
- **Limpieza de Archivos:** [`REPORTE_LIMPIEZA_ARCHIVOS.md`](REPORTE_LIMPIEZA_ARCHIVOS.md)

### Recursos Externos

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Volt Dashboard](https://themesberg.com/product/admin-dashboard/volt-bootstrap-5-dashboard)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3/)
- [Font Awesome Icons](https://fontawesome.com/icons)
- [MercadoPago Docs](https://www.mercadopago.com.mx/developers/)

### Reportar Problemas

Si encuentras bugs o tienes sugerencias:

1. Revisa issues existentes en GitHub
2. Crea un nuevo issue con:
   - Descripción del problema
   - Pasos para reproducir
   - Screenshots (si aplica)
   - Logs de error

---

## 🏆 Créditos

### Desarrollo

- **CETAM** - Centro de Desarrollo Tecnológico Aplicado de México
- **Plantilla Base:** [Volt Laravel Dashboard](https://github.com/themesberg/volt-laravel-dashboard) by Themesberg

### Tecnologías

- **Laravel** - The PHP Framework For Web Artisans
- **Bootstrap** - The most popular HTML, CSS, and JS library
- **Font Awesome** - The internet's icon library and toolkit
- **MercadoPago** - Plataforma de pagos online

---

## 🎯 Roadmap

### Version 1.1 (Próximamente)
- [ ] Panel de Analytics avanzado
- [ ] Exportación de reportes PDF/Excel
- [ ] Sistema de roles y permisos granular
- [ ] App móvil nativa (Flutter)
- [ ] Integración con WhatsApp Business API

### Version 2.0 (Futuro)
- [ ] Multi-tenancy completo
- [ ] Sistema de inventario
- [ ] Programa de lealtad para clientes
- [ ] Integración con ERPs
- [ ] Machine Learning para predicciones

---

<div align="center">

### 🚀 ¡Listo para usar Order QR System!

**Desarrollado con ❤️ por CETAM**

[Reportar Bug](https://github.com/tu-usuario/order-qr-system/issues) · [Solicitar Feature](https://github.com/tu-usuario/order-qr-system/issues) · [Documentación](https://github.com/tu-usuario/order-qr-system/wiki)

---

**© 2025 CETAM - Centro de Desarrollo Tecnológico Aplicado de México**

</div>
