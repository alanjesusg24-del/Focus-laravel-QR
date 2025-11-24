# CETAM - Centro de Servicios (CS)

**Order QR System**
**Versión: 1.0.0**
**CETAM © 2025**

---

## DESCRIPCIÓN DEL PROYECTO

Sistema de gestión de órdenes con códigos QR desarrollado bajo los estándares institucionales CETAM. Permite a negocios crear, gestionar y rastrear órdenes mediante tecnología QR, con módulos de chat, pagos y reportes.

---

## STACK TECNOLÓGICO

### Backend
- **Framework:** Laravel 12.x
- **PHP:** 8.2.x
- **Base de Datos:** MySQL 8.0+
- **Autenticación:** Laravel Guards (Multi-guard)

### Frontend
- **Template:** Volt Dashboard (Bootstrap 5.3.x)
- **CSS Framework:** Bootstrap 5.3.x
- **Preprocesador:** SCSS
- **JavaScript:** Vanilla JS + Alpine.js
- **Compilador:** Laravel Mix 6.x
- **Node:** 22.x

### Librerías Principales
- **Iconos:** Font Awesome 5.11.2
- **Notificaciones:** SweetAlert2, Notyf
- **Charts:** ApexCharts, Chartist
- **Componentes:** Simplebar, Vanillajs-datepicker
- **Pagos:** Stripe, MercadoPago SDK v3.x
- **Push Notifications:** Firebase Cloud Messaging (FCM API v1)

---

## REQUISITOS DEL SISTEMA

### Software Obligatorio
```bash
PHP >= 8.2.x
Composer >= 2.8.x
Node.js = 22.x
NPM >= 10.x
MySQL >= 8.0
```

### Extensiones PHP Requeridas
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

---

## INSTALACIÓN

### 1. Clonar Repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd volt-laravel-dashboard-1.0.1-main
```

### 2. Instalar Dependencias
```bash
# PHP
composer install

# Node.js
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
Editar `.env`:
```env
DB_DATABASE=cetam_cs_orderqr
DB_USERNAME=root
DB_PASSWORD=
```

Ejecutar migraciones:
```bash
php artisan migrate --seed
```

### 5. Compilar Assets
```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 6. Iniciar Servidor
```bash
php artisan serve
```

Acceder a: `http://localhost:8000`

---

## ESTRUCTURA DEL PROYECTO

```
proyecto/
├── app/
│   ├── Http/Controllers/          # Controladores
│   ├── Models/                    # Modelos Eloquent
│   ├── View/Components/           # Componentes Blade
│   └── Services/                  # Servicios de negocio
├── config/
│   ├── cetam.cs.php              # Configuración CETAM
│   └── icons.php                 # Catálogo de iconos
├── database/
│   ├── migrations/               # Migraciones
│   └── seeders/                  # Seeders
├── resources/
│   ├── scss/                     # Estilos SCSS
│   │   ├── custom/              # Estilos personalizados CETAM
│   │   └── volt.scss            # Archivo principal
│   ├── js/                      # JavaScript
│   └── views/                   # Vistas Blade
│       ├── layouts/            # Layouts base
│       ├── components/         # Componentes
│       ├── business/           # Vistas de negocios
│       └── superadmin/         # Vistas de superadmin
├── routes/
│   ├── web.php                 # Rutas web
│   └── api.php                 # Rutas API
└── public/
    ├── css/                    # CSS compilado
    └── js/                     # JS compilado
```

---

## PALETA DE COLORES INSTITUCIONAL

### Colores Principales
- **Primary:** `#1F2937` (Gris oscuro slate)
- **Secondary:** `#FB503B` (Naranja rojizo vibrante)
- **Tertiary:** `#31316A` (Azul índigo oscuro)

### Colores Semánticos
- **Success:** `#10B981` (Verde)
- **Danger:** `#E11D48` (Rojo)
- **Warning:** `#FBA918` (Ámbar)
- **Info:** `#1E90FF` (Azul claro)

### Tipografía
- **Font Family:** Nunito Sans
- **Weights:** 300, 400, 600, 700, 800

---

## USUARIOS DE PRUEBA

### Super Administrador
```
Email: superadmin@cetam.mx
Password: [Ver seeders]
```

### Business (Negocio)
```
Email: business@cetam.mx
Password: [Ver seeders]
```

---

## MÓDULOS DEL SISTEMA

### 1. **Gestión de Negocios** (SuperAdmin)
- CRUD de negocios
- Asignación de planes
- Monitoreo de actividad
- Gestión de pagos y suscripciones

### 2. **Gestión de Órdenes** (Business)
- Crear órdenes con descripción
- Generar códigos QR únicos
- Vincular órdenes a dispositivos móviles
- Estados: Pendiente, Listo, Entregado, Cancelado
- Chat en tiempo real con clientes

### 3. **Sistema de Pagos**
- Integración con MercadoPago (Sandbox/Producción)
- Integración con Stripe
- Webhooks para confirmación de pagos
- Historial de transacciones

### 4. **Reportes y Dashboard**
- Estadísticas de órdenes
- Gráficas de ingresos
- Top negocios por revenue
- Métricas en tiempo real

### 5. **Notificaciones Push**
- Firebase Cloud Messaging (FCM)
- Notificaciones a dispositivos móviles
- Estados de órdenes en tiempo real

---

## COMANDOS ÚTILES

### Desarrollo
```bash
php artisan serve                    # Servidor local
npm run dev                          # Watch assets
php artisan migrate:fresh --seed     # Resetear BD
```

### Caché
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Producción
```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ESTÁNDARES DE DESARROLLO

Este proyecto sigue los **Estándares CETAM v3.0** que incluyen:

- ✅ PSR-12 para código PHP
- ✅ Nomenclatura institucional para archivos
- ✅ Cabeceras documentadas en todos los archivos
- ✅ Paleta de colores institucional
- ✅ Componente `<x-icon>` para iconos
- ✅ Prefijos CETAM_CS_ en configuración
- ✅ Máximo 120 caracteres por línea

---

## DOCUMENTACIÓN

Para más detalles sobre implementación, consultar:
- `INSTRUCCIONES_IMPLEMENTACION_LARAVEL_CETAM.md` - Manual completo CETAM

---

## SOPORTE Y CONTACTO

**CETAM - Centro de Desarrollo Tecnológico Aplicado de México**
Proyecto: Centro de Servicios (CS)

---

## LICENCIA

Propiedad de CETAM © 2025. Todos los derechos reservados.

Este proyecto es de uso interno institucional y no debe ser distribuido sin autorización.

---

**Última actualización:** Noviembre 2025
**Versión del Manual:** 3.0
