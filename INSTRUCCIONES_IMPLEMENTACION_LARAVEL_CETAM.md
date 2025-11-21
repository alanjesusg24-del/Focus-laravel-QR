# GUÍA COMPLETA DE IMPLEMENTACIÓN - PROYECTO LARAVEL CETAM

**Manual de Programación Laravel - CETAM**  
**Versión: 3.0**  
**Fecha: Noviembre 2025**

---

## TABLA DE CONTENIDOS

1. [Archivos a Eliminar](#1-archivos-a-eliminar)
2. [Configuración Inicial del Proyecto](#2-configuración-inicial-del-proyecto)
3. [Instalación de Dependencias](#3-instalación-de-dependencias)
4. [Configuración de la Plantilla Volt](#4-configuración-de-la-plantilla-volt)
5. [Estructura de Carpetas](#5-estructura-de-carpetas)
6. [Configuración del Proyecto](#6-configuración-del-proyecto)
7. [Base de Datos](#7-base-de-datos)
8. [Sistema de Rutas](#8-sistema-de-rutas)
9. [Controladores](#9-controladores)
10. [Modelos](#10-modelos)
11. [Vistas y Layouts](#11-vistas-y-layouts)
12. [Componentes Blade](#12-componentes-blade)
13. [Estándares de Código](#13-estándares-de-código)
14. [Sistema de Iconos](#14-sistema-de-iconos)
15. [Sistema de Notificaciones](#15-sistema-de-notificaciones)
16. [Validación y Form Requests](#16-validación-y-form-requests)
17. [Servicios y Repositorios](#17-servicios-y-repositorios)
18. [Assets y Compilación](#18-assets-y-compilación)
19. [Checklist de Implementación](#19-checklist-de-implementación)

---

## 1. ARCHIVOS A ELIMINAR

### 1.1. Archivos de Documentación Innecesarios

Eliminar los siguientes archivos antes de comenzar:

```bash
# Archivos markdown de documentación
README.md
CHANGELOG.md
CONTRIBUTING.md
CODE_OF_CONDUCT.md
LICENSE.md

# Archivos de Claude Code (si existen)
.claude/
.clinerules
.clinerules.json

# Archivos de ejemplo de Laravel
resources/views/welcome.blade.php
routes/channels.php  # Solo si no se usa broadcasting
```

### 1.2. Comando para Limpieza

```bash
# Ejecutar en la raíz del proyecto
rm -f README.md CHANGELOG.md CONTRIBUTING.md CODE_OF_CONDUCT.md LICENSE.md
rm -rf .claude .clinerules .clinerules.json
rm -f resources/views/welcome.blade.php
```

---

## 2. CONFIGURACIÓN INICIAL DEL PROYECTO

### 2.1. Verificar Versiones Requeridas

**OBLIGATORIO - Versiones exactas según manual CETAM:**

```bash
# Verificar versiones
php --version    # Debe ser 8.2.x
composer --version    # Debe ser 2.8.x o superior
node --version    # Debe ser 22.x
npm --version    # Versión asociada a Node 22.x

# Laravel Framework
php artisan --version    # Debe ser Laravel 12.x
```

### 2.2. Variables de Entorno (.env)

Configurar el archivo `.env` con el prefijo institucional:

```env
# Identificación del Proyecto
# CETAM_<PROJ>_* donde <PROJ> es el código del proyecto (2-4 letras mayúsculas)
# Ejemplo: CS = Centro de Servicios, EMM = Expo México Mujer

APP_NAME="CETAM - [Nombre del Proyecto]"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Database con prefijo institucional
CETAM_CS_DB_CONNECTION=mysql
CETAM_CS_DB_HOST=127.0.0.1
CETAM_CS_DB_PORT=3306
CETAM_CS_DB_DATABASE=cetam_cs_db
CETAM_CS_DB_USERNAME=root
CETAM_CS_DB_PASSWORD=

# Mail Configuration
CETAM_CS_MAIL_MAILER=smtp
CETAM_CS_MAIL_HOST=smtp.gmail.com
CETAM_CS_MAIL_PORT=587
CETAM_CS_MAIL_USERNAME=
CETAM_CS_MAIL_PASSWORD=
CETAM_CS_MAIL_ENCRYPTION=tls

# Pusher/Broadcasting (si se usa)
CETAM_CS_PUSHER_APP_ID=
CETAM_CS_PUSHER_APP_KEY=
CETAM_CS_PUSHER_APP_SECRET=
CETAM_CS_PUSHER_APP_CLUSTER=mt1

# Features
CETAM_CS_FEATURE_INVOICING=true
CETAM_CS_FEATURE_REPORTING=true
```

---

## 3. INSTALACIÓN DE DEPENDENCIAS

### 3.1. Dependencias PHP (Backend)

```bash
# Dependencias obligatorias
composer require laravel/sanctum  # Autenticación API
composer require livewire/livewire  # Componentes interactivos

# Dependencias de desarrollo
composer require --dev barryvdh/laravel-debugbar
composer require --dev laravel/pint  # Code style fixer
```

### 3.2. Dependencias JavaScript (Frontend)

```bash
# Bootstrap 5.3.x (OBLIGATORIO)
npm install bootstrap@5.3.x

# Font Awesome para iconos (Estilo Classic Solid)
npm install @fortawesome/fontawesome-free

# SweetAlert2 para modales
npm install sweetalert2

# Notyf para notificaciones flotantes
npm install notyf

# Dependencias de compilación
npm install --save-dev sass
npm install --save-dev vite
npm install --save-dev laravel-vite-plugin
```

### 3.3. Instalación Completa

```bash
# Instalar todas las dependencias
composer install
npm install
```

---

## 4. CONFIGURACIÓN DE LA PLANTILLA VOLT

### 4.1. Integración de Volt Laravel Admin Dashboard

La plantilla Volt es proporcionada por CETAM y debe integrarse siguiendo estos pasos:

```bash
# 1. Copiar archivos de la plantilla Volt al proyecto
# La plantilla debe estar en una carpeta temporal (ajustar ruta según ubicación)

# Copiar assets de Volt
cp -r /ruta-plantilla-volt/public/assets public/
cp -r /ruta-plantilla-volt/resources/sass resources/
cp -r /ruta-plantilla-volt/resources/js resources/

# Copiar layouts y componentes base
cp -r /ruta-plantilla-volt/resources/views/layouts resources/views/
cp -r /ruta-plantilla-volt/resources/views/components resources/views/
```

### 4.2. Estructura de Assets de Volt

```
resources/
├── sass/
│   ├── _variables.scss        # Variables institucionales
│   ├── _mixins.scss
│   ├── _components.scss
│   ├── _volt.scss             # Estilos principales de Volt
│   └── app.scss               # Archivo principal
├── js/
│   ├── app.js                 # JavaScript principal
│   └── volt.js                # Scripts de Volt
└── views/
    ├── layouts/
    │   ├── app.blade.php      # Layout principal
    │   └── dashboard.blade.php # Layout con sidebar
    ├── components/
    │   ├── alert.blade.php
    │   ├── modal.blade.php
    │   └── sidebar.blade.php
    └── partials/
        ├── header.blade.php
        └── footer.blade.php
```

### 4.3. Configurar Variables SCSS Institucionales

Editar `resources/sass/_variables.scss`:

```scss
// ============================================
// VARIABLES INSTITUCIONALES CETAM
// Paleta de colores basada en plantilla Volt
// ============================================

// Colores Principales
$primary: #1F2937;        // Gris oscuro slate
$secondary: #FB503B;      // Naranja rojizo vibrante
$tertiary: #31316A;       // Azul índigo oscuro

// Colores Semánticos
$success: #10B981;        // Verde
$danger: #E11D48;         // Rojo
$warning: #FBA918;        // Ámbar
$info: #1E90FF;           // Azul claro

// Escala de Grises
$white: #FFFFFF;
$gray-50: #F9FAFB;
$gray-100: #F2F4F6;
$gray-200: #E5E7EB;
$gray-300: #D1D5DB;
$gray-400: #9CA3AF;
$gray-500: #6B7280;
$gray-600: #4B5563;
$gray-700: #374151;
$gray-800: #1F2937;
$gray-900: #111827;

// Tipografía Institucional
$font-family-base: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
$font-size-base: 1rem;
$line-height-base: 1.5;

// Espaciado
$spacer: 1rem;
$spacers: (
  0: 0,
  1: $spacer * 0.25,
  2: $spacer * 0.5,
  3: $spacer,
  4: $spacer * 1.5,
  5: $spacer * 3,
);

// Importar Bootstrap con las variables personalizadas
@import "~bootstrap/scss/bootstrap";
```

---

## 5. ESTRUCTURA DE CARPETAS

### 5.1. Estructura Completa del Proyecto

```
proyecto-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── <PROJ>/              # Carpeta por proyecto
│   │   │       ├── Admin/
│   │   │       │   ├── UserController.php
│   │   │       │   └── RoleController.php
│   │   │       └── DashboardController.php
│   │   ├── Middleware/
│   │   └── Requests/
│   │       └── <PROJ>/
│   │           ├── StoreUserRequest.php
│   │           └── UpdateUserRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   └── Permission.php
│   ├── Services/
│   │   └── <PROJ>/
│   │       ├── UserService.php
│   │       └── ReportService.php
│   ├── Repositories/
│   │   └── <PROJ>/
│   │       └── UserRepository.php
│   ├── Helpers/
│   │   └── helpers.php
│   └── Livewire/
│       └── <PROJ>/
│           └── UsersTable.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php
│   │   └── UserSeeder.php
│   └── factories/
│       └── UserFactory.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   └── dashboard.blade.php
│   │   ├── components/
│   │   │   └── <proj-slug>/
│   │   │       ├── alert.blade.php
│   │   │       ├── modal.blade.php
│   │   │       └── user-card.blade.php
│   │   ├── partials/
│   │   │   ├── header.blade.php
│   │   │   ├── footer.blade.php
│   │   │   └── sidebar.blade.php
│   │   └── modules/
│   │       └── <proj-slug>/
│   │           └── users/
│   │               ├── index.blade.php
│   │               ├── create.blade.php
│   │               └── edit.blade.php
│   ├── sass/
│   │   ├── _variables.scss
│   │   └── app.scss
│   └── js/
│       └── app.js
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
├── config/
│   ├── cetam.<proj-slug>.php    # Configuración del proyecto
│   └── icons.php                # Configuración de iconos
├── public/
│   ├── assets/
│   │   └── volt/
│   └── images/
├── .env
├── composer.json
├── package.json
└── vite.config.js
```

### 5.2. Crear Estructura de Carpetas

```bash
# Ejecutar desde la raíz del proyecto

# Controllers por proyecto
mkdir -p app/Http/Controllers/CS/Admin

# Requests
mkdir -p app/Http/Requests/CS

# Services y Repositories
mkdir -p app/Services/CS
mkdir -p app/Repositories/CS

# Helpers
mkdir -p app/Helpers

# Livewire
mkdir -p app/Livewire/CS

# Views por módulo
mkdir -p resources/views/layouts
mkdir -p resources/views/components/cs
mkdir -p resources/views/partials
mkdir -p resources/views/modules/cs/users
mkdir -p resources/views/modules/cs/dashboard

# Config personalizada
touch config/cetam.cs.php
touch config/icons.php

# Public assets
mkdir -p public/images
```

---

## 6. CONFIGURACIÓN DEL PROYECTO

### 6.1. Archivo de Configuración del Proyecto

Crear `config/cetam.cs.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        cetam.cs.php
 * @description Configuración específica del proyecto CS
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 * 
 * ============================================
 */

return [
    // Identificación del proyecto
    'code' => 'CS',
    'slug' => 'cs',
    'name' => 'Centro de Servicios',
    'version' => '1.0.0',

    // Características habilitadas
    'features' => [
        'invoicing' => env('CETAM_CS_FEATURE_INVOICING', false),
        'reporting' => env('CETAM_CS_FEATURE_REPORTING', false),
        'notifications' => true,
        'chat' => false,
    ],

    // Configuración de base de datos
    'database' => [
        'connection' => env('CETAM_CS_DB_CONNECTION', 'mysql'),
        'host' => env('CETAM_CS_DB_HOST', '127.0.0.1'),
        'port' => env('CETAM_CS_DB_PORT', '3306'),
        'database' => env('CETAM_CS_DB_DATABASE', 'cetam_cs_db'),
        'username' => env('CETAM_CS_DB_USERNAME', 'root'),
        'password' => env('CETAM_CS_DB_PASSWORD', ''),
    ],

    // Paginación
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
    ],

    // Rutas
    'routes' => [
        'prefix' => '/p/cs',
        'middleware' => ['web', 'auth'],
    ],
];
```

### 6.2. Configuración de Iconos

Crear `config/icons.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Configuración de Iconos
 * ============================================
 * 
 * Font Awesome Classic Solid Icons
 * Catálogo estandarizado de iconos institucionales
 * 
 * @file        icons.php
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

return [
    'icons' => [
        // Usuarios y Roles
        'user' => 'fa-solid fa-user',
        'userCircle' => 'fa-solid fa-circle-user',
        'userAdd' => 'fa-solid fa-user-plus',
        'userRemove' => 'fa-solid fa-user-minus',
        'userGroup' => 'fa-solid fa-users',
        'userTie' => 'fa-solid fa-user-tie',

        // Acciones CRUD
        'add' => 'fa-solid fa-plus',
        'create' => 'fa-solid fa-plus',
        'edit' => 'fa-solid fa-pen-to-square',
        'delete' => 'fa-solid fa-trash',
        'view' => 'fa-solid fa-eye',
        'save' => 'fa-solid fa-floppy-disk',
        'cancel' => 'fa-solid fa-xmark',
        'send' => 'fa-solid fa-paper-plane',
        'download' => 'fa-solid fa-download',
        'upload' => 'fa-solid fa-upload',
        'search' => 'fa-solid fa-magnifying-glass',
        'refresh' => 'fa-solid fa-arrows-rotate',

        // Estados y Alertas
        'success' => 'fa-solid fa-circle-check',
        'error' => 'fa-solid fa-circle-xmark',
        'warning' => 'fa-solid fa-triangle-exclamation',
        'info' => 'fa-solid fa-circle-info',
        'pending' => 'fa-solid fa-clock',
        'notification' => 'fa-solid fa-bell',
        'notificationOff' => 'fa-solid fa-bell-slash',

        // Archivos
        'file' => 'fa-solid fa-file',
        'filePdf' => 'fa-solid fa-file-pdf',
        'fileWord' => 'fa-solid fa-file-word',
        'fileExcel' => 'fa-solid fa-file-excel',
        'fileImage' => 'fa-solid fa-file-image',
        'folder' => 'fa-solid fa-folder',
        'folderOpen' => 'fa-solid fa-folder-open',
        'attachment' => 'fa-solid fa-paperclip',

        // Navegación
        'home' => 'fa-solid fa-house',
        'dashboard' => 'fa-solid fa-gauge-high',
        'menu' => 'fa-solid fa-bars',
        'back' => 'fa-solid fa-arrow-left',
        'forward' => 'fa-solid fa-arrow-right',
        'up' => 'fa-solid fa-arrow-up',
        'down' => 'fa-solid fa-arrow-down',
        'close' => 'fa-solid fa-xmark',
        'externalLink' => 'fa-solid fa-arrow-up-right-from-square',

        // Configuración
        'settings' => 'fa-solid fa-cog',
        'loading' => 'fa-solid fa-spinner',
        'process' => 'fa-solid fa-gears',
        'sync' => 'fa-solid fa-sync',

        // Listas y Filtros
        'list' => 'fa-solid fa-list-ul',
        'listOrdered' => 'fa-solid fa-list-ol',
        'filter' => 'fa-solid fa-filter',
        'sort' => 'fa-solid fa-sort',
        'sortUp' => 'fa-solid fa-sort-up',
        'sortDown' => 'fa-solid fa-sort-down',

        // Seguridad
        'login' => 'fa-solid fa-right-to-bracket',
        'logout' => 'fa-solid fa-right-from-bracket',
        'lock' => 'fa-solid fa-lock',
        'unlock' => 'fa-solid fa-lock-open',
        'key' => 'fa-solid fa-key',
        'shield' => 'fa-solid fa-shield-halved',

        // Reportes
        'report' => 'fa-solid fa-chart-line',
        'reportBar' => 'fa-solid fa-chart-bar',
        'reportPie' => 'fa-solid fa-chart-pie',
        'print' => 'fa-solid fa-print',
        'downloadReport' => 'fa-solid fa-file-arrow-down',

        // Comunicación
        'email' => 'fa-solid fa-envelope',
        'phone' => 'fa-solid fa-phone',
        'chat' => 'fa-solid fa-comments',
        'support' => 'fa-solid fa-life-ring',
        'help' => 'fa-solid fa-circle-question',

        // Finanzas
        'money' => 'fa-solid fa-dollar-sign',
        'coins' => 'fa-solid fa-coins',
        'card' => 'fa-solid fa-credit-card',
        'invoice' => 'fa-solid fa-file-invoice-dollar',
    ],
];
```

---

## 7. BASE DE DATOS

### 7.1. Migración de Usuarios y Roles

Crear `database/migrations/2025_11_20_000001_create_roles_table.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Sistema de Roles
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        2025_11_20_000001_create_roles_table.php
 * @description Migración para tabla de roles del sistema
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
```

Crear `database/migrations/2025_11_20_000002_update_users_table.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Actualización Tabla Usuarios
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        2025_11_20_000002_update_users_table.php
 * @description Agrega campos institucionales a tabla de usuarios
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->after('id')
                ->nullable()
                ->constrained('roles')
                ->onDelete('set null');

            $table->string('phone', 15)->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('password');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();

            // Índices adicionales
            $table->index('role_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'role_id',
                'phone',
                'is_active',
                'last_login_at',
                'deleted_at'
            ]);
        });
    }
};
```

### 7.2. Seeders

Crear `database/seeders/RoleSeeder.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Seeder de Roles
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        RoleSeeder.php
 * @description Carga roles iniciales del sistema
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Administrador',
                'slug' => 'super-admin',
                'description' => 'Acceso total al sistema',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Gestión completa de usuarios y contenido',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Usuario',
                'slug' => 'user',
                'description' => 'Usuario estándar del sistema',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
```

Crear `database/seeders/UserSeeder.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Seeder de Usuarios
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        UserSeeder.php
 * @description Carga usuarios iniciales del sistema
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRoleId = DB::table('roles')->where('slug', 'super-admin')->value('id');

        DB::table('users')->insert([
            'role_id' => $superAdminRoleId,
            'name' => 'Administrador CETAM',
            'email' => 'admin@cetam.mx',
            'phone' => '7221234567',
            'password' => Hash::make('cetam2025'),
            'email_verified_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
```

Actualizar `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
```

### 7.3. Ejecutar Migraciones y Seeders

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# O todo junto (resetear base de datos)
php artisan migrate:fresh --seed
```

---

## 8. SISTEMA DE RUTAS

### 8.1. Rutas Web

Editar `routes/web.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Rutas Web
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        web.php
 * @description Definición de rutas web del proyecto
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CS\DashboardController;
use App\Http\Controllers\CS\Admin\UserController;
use App\Http\Controllers\CS\Admin\RoleController;

/*
 * Ruta pública de bienvenida
 */
Route::get('/', function () {
    return redirect()->route('cs.dashboard.index');
});

/*
 * Grupo de rutas del proyecto CS
 * Prefijo: /p/cs
 * Middleware: web, auth
 */
Route::prefix('/p/cs')
    ->name('cs.')
    ->middleware(['web', 'auth'])
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard.index');

        // Módulo de Usuarios
        Route::prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::get('/{user}', [UserController::class, 'show'])->name('show');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            });

        // Módulo de Roles
        Route::resource('roles', RoleController::class)
            ->names([
                'index' => 'roles.index',
                'create' => 'roles.create',
                'store' => 'roles.store',
                'show' => 'roles.show',
                'edit' => 'roles.edit',
                'update' => 'roles.update',
                'destroy' => 'roles.destroy',
            ]);
    });

/*
 * Rutas de autenticación (Laravel Breeze/Jetstream)
 */
require __DIR__.'/auth.php';
```

### 8.2. Rutas API

Editar `routes/api.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Rutas API
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        api.php
 * @description Definición de rutas API REST
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

use Illuminate\Support\Facades\Route;

/*
 * Grupo de rutas API v1
 * Prefijo: /api/v1/cs
 * Middleware: api, auth:sanctum
 */
Route::prefix('/v1/cs')
    ->middleware(['api', 'auth:sanctum'])
    ->group(function () {
        
        // Endpoints de usuarios
        Route::apiResource('users', 'App\Http\Controllers\Api\V1\CS\UserController');
        
        // Endpoints de roles
        Route::apiResource('roles', 'App\Http\Controllers\Api\V1\CS\RoleController');
    });
```

---

## 9. CONTROLADORES

### 9.1. Dashboard Controller

Crear `app/Http/Controllers/CS/DashboardController.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Dashboard Controller
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        DashboardController.php
 * @description Controlador principal del dashboard
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 * 
 * ============================================
 */

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display dashboard main view
     * 
     * @return View
     */
    public function index(): View
    {
        $data = [
            'totalUsers' => 0,
            'activeUsers' => 0,
            'totalRoles' => 0,
        ];

        return view('modules.cs.dashboard.index', compact('data'));
    }
}
```

### 9.2. User Controller

Crear `app/Http/Controllers/CS/Admin/UserController.php`:

```php
<?php

/**
 * ============================================
 * CETAM - User Controller
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        UserController.php
 * @description Controlador CRUD de usuarios
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 * 
 * ============================================
 */

namespace App\Http\Controllers\CS\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CS\StoreUserRequest;
use App\Http\Requests\CS\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users
     * 
     * @return View
     */
    public function index(): View
    {
        $users = User::with('role')
            ->latest()
            ->paginate(config('cetam.cs.pagination.per_page', 15));

        return view('modules.cs.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     * 
     * @return View
     */
    public function create(): View
    {
        $roles = Role::where('is_active', true)->get();

        return view('modules.cs.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     * 
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $userData = $request->validated();
        $user = User::create($userData);

        return redirect()
            ->route('cs.users.index')
            ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Display the specified user
     * 
     * @param User $user
     * @return View
     */
    public function show(User $user): View
    {
        $user->load('role');

        return view('modules.cs.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     * 
     * @param User $user
     * @return View
     */
    public function edit(User $user): View
    {
        $roles = Role::where('is_active', true)->get();

        return view('modules.cs.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     * 
     * @param UpdateUserRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $userData = $request->validated();
        $user->update($userData);

        return redirect()
            ->route('cs.users.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Remove the specified user
     * 
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('cs.users.index')
            ->with('success', 'Usuario eliminado correctamente');
    }
}
```

---

## 10. MODELOS

### 10.1. User Model

Actualizar `app/Models/User.php`:

```php
<?php

/**
 * ============================================
 * CETAM - User Model
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        User.php
 * @description Modelo de usuario del sistema
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 * 
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the role that owns the user
     * 
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Scope query to only active users
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if user has specific role
     * 
     * @param string $roleSlug
     * @return bool
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role?->slug === $roleSlug;
    }
}
```

### 10.2. Role Model

Crear `app/Models/Role.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Role Model
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        Role.php
 * @description Modelo de rol del sistema
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 * 
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the users for the role
     * 
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope query to only active roles
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

---

## 11. VISTAS Y LAYOUTS

### 11.1. Layout Principal

Crear `resources/views/layouts/dashboard.blade.php`:

```blade
{{--
============================================
CETAM - Layout Dashboard Principal
============================================

@project     Centro de Servicios (CS)
@file        dashboard.blade.php
@description Layout principal con sidebar para módulos del sistema
@author      [Nombre del Desarrollador]
@created     2025-11-20
@version     1.0.0
@copyright   CETAM © 2025

============================================
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Styles --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('styles')
</head>
<body>
    <div id="app">
        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <main class="content">
            {{-- Header --}}
            @include('partials.header')

            {{-- Page Content --}}
            <div class="py-4">
                @yield('content')
            </div>

            {{-- Footer --}}
            @include('partials.footer')
        </main>
    </div>

    {{-- Scripts --}}
    @yield('scripts')
</body>
</html>
```

### 11.2. Sidebar

Crear `resources/views/partials/sidebar.blade.php`:

```blade
{{--
============================================
CETAM - Sidebar
============================================

@project     Centro de Servicios (CS)
@file        sidebar.blade.php
@description Barra lateral de navegación
@created     2025-11-20

============================================
--}}

<nav id="sidebar" class="sidebar">
    <div class="sidebar-content">
        {{-- Logo --}}
        <div class="sidebar-brand">
            <a href="{{ route('cs.dashboard.index') }}">
                <span class="align-middle">CETAM - CS</span>
            </a>
        </div>

        {{-- Navigation Menu --}}
        <ul class="sidebar-nav">
            {{-- Dashboard --}}
            <li class="sidebar-item {{ request()->routeIs('cs.dashboard.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('cs.dashboard.index') }}">
                    <x-icon name="dashboard" /> 
                    <span class="align-middle">Dashboard</span>
                </a>
            </li>

            {{-- Administración --}}
            <li class="sidebar-header">Administración</li>

            {{-- Usuarios --}}
            <li class="sidebar-item {{ request()->routeIs('cs.users.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('cs.users.index') }}">
                    <x-icon name="userGroup" /> 
                    <span class="align-middle">Usuarios</span>
                </a>
            </li>

            {{-- Roles --}}
            <li class="sidebar-item {{ request()->routeIs('cs.roles.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('cs.roles.index') }}">
                    <x-icon name="shield" /> 
                    <span class="align-middle">Roles</span>
                </a>
            </li>

            {{-- Configuración --}}
            <li class="sidebar-header">Sistema</li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="#">
                    <x-icon name="settings" /> 
                    <span class="align-middle">Configuración</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
```

### 11.3. Header

Crear `resources/views/partials/header.blade.php`:

```blade
{{--
============================================
CETAM - Header
============================================

@project     Centro de Servicios (CS)
@file        header.blade.php
@description Encabezado superior
@created     2025-11-20

============================================
--}}

<nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="sidebar-toggle d-flex">
        <x-icon name="menu" />
    </a>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">
            {{-- Notifications --}}
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
                    <x-icon name="notification" />
                    <span class="indicator">4</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
                    <div class="dropdown-menu-header">
                        4 Nuevas Notificaciones
                    </div>
                    <div class="list-group">
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2">
                                    <x-icon name="info" class="text-info" />
                                </div>
                                <div class="col-10">
                                    <div class="text-dark">Notificación de ejemplo</div>
                                    <div class="text-muted small mt-1">Hace 5 minutos</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </li>

            {{-- User Dropdown --}}
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                    <x-icon name="settings" />
                </a>

                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                    <span class="text-dark">{{ Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="#">
                        <x-icon name="user" /> Perfil
                    </a>
                    <a class="dropdown-item" href="#">
                        <x-icon name="settings" /> Configuración
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <x-icon name="logout" /> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
```

### 11.4. Vista de Usuarios - Index

Crear `resources/views/modules/cs/users/index.blade.php`:

```blade
{{--
============================================
CETAM - Listado de Usuarios
============================================

@project     Centro de Servicios (CS)
@file        index.blade.php
@description Vista principal del módulo de usuarios
@created     2025-11-20

============================================
--}}

@extends('layouts.dashboard')

@section('title', 'Usuarios')

@section('content')
<div class="container-fluid p-0">
    {{-- Page Header --}}
    <div class="mb-3">
        <h1 class="h3 d-inline align-middle">Usuarios</h1>
        <a href="{{ route('cs.users.create') }}" class="btn btn-primary float-end">
            <x-icon name="userAdd" /> Nuevo Usuario
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <x-cs-alert type="success" :message="session('success')" />
    @endif

    {{-- Users Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Listado de Usuarios</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $user->role->name ?? 'Sin rol' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">
                                                <x-icon name="success" /> Activo
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <x-icon name="error" /> Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('cs.users.show', $user) }}" class="btn btn-sm btn-info">
                                            <x-icon name="view" />
                                        </a>
                                        <a href="{{ route('cs.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                            <x-icon name="edit" />
                                        </a>
                                        <form action="{{ route('cs.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar usuario?')">
                                                <x-icon name="delete" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay usuarios registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 12. COMPONENTES BLADE

### 12.1. Componente de Icono

Crear `app/View/Components/Icon.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Icon Component
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        Icon.php
 * @description Componente Blade para renderizar iconos
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    public string $iconClass;
    public string $additionalClasses;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public ?string $class = ''
    ) {
        $icons = config('icons.icons', []);
        $this->iconClass = $icons[$name] ?? 'fa-solid fa-circle-question';
        $this->additionalClasses = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.icon');
    }
}
```

Crear `resources/views/components/icon.blade.php`:

```blade
{{--
============================================
CETAM - Icon Component View
============================================

@props name - Alias del icono del catálogo
@props class - Clases CSS adicionales

============================================
--}}

<i class="{{ $iconClass }} {{ $additionalClasses }}" aria-hidden="true"></i>
```

### 12.2. Componente de Alerta

Crear `app/View/Components/CS/Alert.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Alert Component
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        Alert.php
 * @description Componente de alertas Bootstrap
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

namespace App\View\Components\CS;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $alertClass;
    public string $iconName;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $type = 'info',
        public string $message = '',
        public bool $dismissible = true
    ) {
        $this->alertClass = match($type) {
            'success' => 'alert-success',
            'danger', 'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-info',
        };

        $this->iconName = match($type) {
            'success' => 'success',
            'danger', 'error' => 'error',
            'warning' => 'warning',
            'info' => 'info',
            default => 'info',
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cs.alert');
    }
}
```

Crear `resources/views/components/cs/alert.blade.php`:

```blade
{{--
============================================
CETAM - Alert Component View
============================================

@props type - Tipo de alerta (success, danger, warning, info)
@props message - Mensaje a mostrar
@props dismissible - Permite cerrar la alerta

============================================
--}}

<div class="alert {{ $alertClass }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
    <x-icon :name="$iconName" class="me-2" />
    {{ $message }}
    
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
```

---

## 13. ESTÁNDARES DE CÓDIGO

### 13.1. Convenciones de Nomenclatura

**IMPORTANTE - Todos los nombres deben seguir estas reglas:**

```
CLASES Y MODELOS:
- PascalCase
- Singular
- Ejemplos: User, OrderDetail, InvoiceService

CONTROLADORES:
- PascalCase + sufijo Controller
- Ejemplos: UserController, OrderController

MÉTODOS Y FUNCIONES:
- camelCase
- Ejemplos: getUserList(), calculateTotal()

VARIABLES:
- camelCase
- Booleanos con prefijos: is/has/can/should
- Colecciones en plural
- Ejemplos: $userName, $isActive, $users

CONSTANTES:
- UPPER_SNAKE_CASE
- Ejemplo: DEFAULT_PAGE_SIZE

ARCHIVOS BLADE:
- kebab-case
- Ejemplos: user-profile.blade.php, dashboard-card.blade.php

RUTAS:
- Prefijo: /p/<proj-slug>/
- Nombres: <proj-slug>.<módulo>.<acción>
- Ejemplos: /p/cs/users, cs.users.index
```

### 13.2. Cabeceras de Archivos

**OBLIGATORIO - Todos los archivos PHP y Blade deben tener cabecera:**

```php
<?php

/**
 * ============================================
 * CETAM - [Nombre del Archivo]
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        [nombre-archivo].php
 * @description [Descripción breve del propósito del archivo]
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 * 
 * ============================================
 */
```

Para Blade:

```blade
{{--
============================================
CETAM - [Nombre del Archivo]
============================================

@project     Centro de Servicios (CS)
@file        [nombre-archivo].blade.php
@description [Descripción breve]
@created     2025-11-20

============================================
--}}
```

### 13.3. Estándares de Código

```php
// ✅ CORRECTO - Seguir PSR-12

// Sangría: 4 espacios
class UserController extends Controller
{
    // Llaves en nueva línea para clases y métodos
    public function index(): View
    {
        // Llaves en misma línea para estructuras de control
        if ($condition) {
            // Código aquí
        }

        // Early returns
        if (!$user) {
            return redirect()->back();
        }

        // No más de 3 niveles de anidamiento
        foreach ($users as $user) {
            if ($user->isActive()) {
                // Procesar
            }
        }

        // Límite de 120 caracteres por línea
        $result = $this->service
            ->withParameter($param)
            ->execute();

        return view('users.index', compact('users'));
    }
}
```

### 13.4. Comentarios

```php
// Comentario de una línea para aclaraciones breves

/*
 * Comentario multilínea para explicar
 * lógica compleja o secciones extensas
 */

/**
 * DocBlock obligatorio para métodos públicos
 * 
 * @param Request $request
 * @return View
 */
public function index(Request $request): View
{
    // ...
}
```

---

## 14. SISTEMA DE ICONOS

### 14.1. Uso de Componente Icon

El sistema de iconos está basado en Font Awesome Classic Solid y usa el componente `<x-icon>`.

```blade
{{-- USO BÁSICO --}}
<x-icon name="user" />
<x-icon name="edit" />
<x-icon name="delete" />

{{-- CON CLASES ADICIONALES --}}
<x-icon name="success" class="text-success fs-4" />
<x-icon name="warning" class="text-warning me-2" />

{{-- EN BOTONES --}}
<button class="btn btn-primary">
    <x-icon name="save" /> Guardar
</button>

<button class="btn btn-danger">
    <x-icon name="delete" /> Eliminar
</button>

{{-- EN BADGES --}}
<span class="badge bg-success">
    <x-icon name="success" /> Activo
</span>
```

### 14.2. Catálogo de Iconos Disponibles

**Iconos más comunes:**

```
USUARIOS:
- user, userCircle, userAdd, userRemove, userGroup, userTie

ACCIONES:
- add/create, edit, delete, view, save, cancel, send
- download, upload, search, refresh

ESTADOS:
- success, error, warning, info, pending
- notification, notificationOff

NAVEGACIÓN:
- home, dashboard, menu, back, forward, up, down, close

ARCHIVOS:
- file, filePdf, fileWord, fileExcel, fileImage
- folder, folderOpen, attachment

SEGURIDAD:
- login, logout, lock, unlock, key, shield

Ver lista completa en config/icons.php
```

---

## 15. SISTEMA DE NOTIFICACIONES

### 15.1. SweetAlert2 (Modales)

Para confirmaciones y mensajes importantes:

```javascript
// Archivo: resources/js/app.js

// Alerta de éxito
Swal.fire({
    icon: 'success',
    title: 'Éxito',
    text: 'Datos guardados correctamente',
    confirmButtonText: 'Aceptar',
    confirmButtonColor: '#10B981'
});

// Alerta de error
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'No se pudo completar la operación',
    confirmButtonText: 'Reintentar',
    confirmButtonColor: '#E11D48'
});

// Confirmación de eliminación
Swal.fire({
    icon: 'question',
    title: '¿Eliminar registro?',
    text: 'Esta acción no se puede deshacer',
    showCancelButton: true,
    confirmButtonText: 'Eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#E11D48',
    cancelButtonColor: '#6B7280'
}).then((result) => {
    if (result.isConfirmed) {
        // Ejecutar eliminación
    }
});
```

### 15.2. Notyf (Notificaciones Flotantes)

Para mensajes rápidos y no intrusivos:

```javascript
// Inicializar Notyf
const notyf = new Notyf({
    duration: 4000,
    position: {
        x: 'right',
        y: 'top'
    }
});

// Notificación de éxito
notyf.success('Datos guardados correctamente');

// Notificación de error
notyf.error('Error al procesar la solicitud');

// Notificación personalizada
notyf.open({
    type: 'warning',
    message: 'Campos pendientes de completar',
    duration: 5000,
    position: {
        x: 'left',
        y: 'bottom'
    }
});
```

---

## 16. VALIDACIÓN Y FORM REQUESTS

### 16.1. Form Request para Usuario

Crear `app/Http/Requests/CS/StoreUserRequest.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Store User Request
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        StoreUserRequest.php
 * @description Validación para creación de usuarios
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role_id.required' => 'El rol es obligatorio',
            'role_id.exists' => 'El rol seleccionado no existe',
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];
    }
}
```

Crear `app/Http/Requests/CS/UpdateUserRequest.php`:

```php
<?php

/**
 * ============================================
 * CETAM - Update User Request
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        UpdateUserRequest.php
 * @description Validación para actualización de usuarios
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:15'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role_id.required' => 'El rol es obligatorio',
            'name.required' => 'El nombre es obligatorio',
            'email.unique' => 'Este correo ya está en uso',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];
    }
}
```

---

## 17. SERVICIOS Y REPOSITORIOS

### 17.1. User Service

Crear `app/Services/CS/UserService.php`:

```php
<?php

/**
 * ============================================
 * CETAM - User Service
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        UserService.php
 * @description Lógica de negocio para usuarios
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * @version     1.0.0
 * 
 * ============================================
 */

namespace App\Services\CS;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class UserService
{
    /**
     * Get active users with their roles
     * 
     * @return Collection
     */
    public function getActiveUsersWithRoles(): Collection
    {
        return User::with('role')
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Create new user with hashed password
     * 
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        
        return User::create($data);
    }

    /**
     * Update user and hash password if provided
     * 
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        
        return $user->fresh();
    }

    /**
     * Deactivate user instead of deleting
     * 
     * @param User $user
     * @return bool
     */
    public function deactivateUser(User $user): bool
    {
        return $user->update(['is_active' => false]);
    }
}
```

---

## 18. ASSETS Y COMPILACIÓN

### 18.1. Configurar Vite

Editar `vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
```

### 18.2. Configurar SCSS Principal

Editar `resources/sass/app.scss`:

```scss
// ============================================
// CETAM - Estilos Principales
// ============================================

// Variables Institucionales
@import 'variables';

// Font Awesome
@import '~@fortawesome/fontawesome-free/scss/fontawesome';
@import '~@fortawesome/fontawesome-free/scss/solid';

// Bootstrap
@import '~bootstrap/scss/bootstrap';

// Plantilla Volt
@import 'volt';

// Estilos personalizados del proyecto
@import 'custom';
```

Crear `resources/sass/_custom.scss`:

```scss
// ============================================
// CETAM - Estilos Personalizados
// ============================================

// Sidebar
.sidebar {
    background-color: $primary;
    color: $white;

    .sidebar-link {
        color: $gray-400;
        
        &:hover,
        &.active {
            color: $white;
            background-color: rgba(255, 255, 255, 0.1);
        }
    }
}

// Cards
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

// Tables
.table {
    thead {
        background-color: $gray-50;
    }
}

// Buttons
.btn {
    font-weight: 600;
    
    i {
        margin-right: 0.25rem;
    }
}
```

### 18.3. JavaScript Principal

Editar `resources/js/app.js`:

```javascript
/**
 * ============================================
 * CETAM - JavaScript Principal
 * ============================================
 * 
 * @project     Centro de Servicios (CS)
 * @file        app.js
 * @author      [Nombre del Desarrollador]
 * @created     2025-11-20
 * 
 * ============================================
 */

// Bootstrap
import * as bootstrap from 'bootstrap';

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Notyf
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

window.notyf = new Notyf({
    duration: 4000,
    position: {
        x: 'right',
        y: 'top'
    },
    types: [
        {
            type: 'success',
            background: '#10B981',
            icon: {
                className: 'fas fa-circle-check',
                tagName: 'i',
            }
        },
        {
            type: 'error',
            background: '#E11D48',
            icon: {
                className: 'fas fa-circle-xmark',
                tagName: 'i',
            }
        },
        {
            type: 'warning',
            background: '#FBA918',
            icon: {
                className: 'fas fa-triangle-exclamation',
                tagName: 'i',
            }
        },
        {
            type: 'info',
            background: '#1E90FF',
            icon: {
                className: 'fas fa-circle-info',
                tagName: 'i',
            }
        }
    ]
});

// Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('#sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
});
```

### 18.4. Compilar Assets

```bash
# Desarrollo (watch mode)
npm run dev

# Producción
npm run build
```

---

## 19. CHECKLIST DE IMPLEMENTACIÓN

### ✅ FASE 1: CONFIGURACIÓN INICIAL

- [ ] Verificar versiones de software (PHP 8.2.x, Laravel 12.x, Node 22.x)
- [ ] Configurar archivo `.env` con prefijos CETAM
- [ ] Eliminar archivos innecesarios (.md, .claude, welcome.blade.php)
- [ ] Instalar dependencias PHP: `composer install`
- [ ] Instalar dependencias JS: `npm install`
- [ ] Generar APP_KEY: `php artisan key:generate`

### ✅ FASE 2: BASE DE DATOS

- [ ] Configurar conexión a base de datos en `.env`
- [ ] Crear migraciones de roles y actualizar users
- [ ] Crear seeders (RoleSeeder, UserSeeder)
- [ ] Ejecutar migraciones: `php artisan migrate`
- [ ] Ejecutar seeders: `php artisan db:seed`

### ✅ FASE 3: PLANTILLA VOLT

- [ ] Integrar archivos de plantilla Volt
- [ ] Configurar variables SCSS institucionales
- [ ] Configurar paleta de colores en `_variables.scss`
- [ ] Crear layouts (dashboard.blade.php)
- [ ] Crear partials (sidebar, header, footer)

### ✅ FASE 4: ESTRUCTURA DEL PROYECTO

- [ ] Crear carpetas de controllers por proyecto (CS/Admin)
- [ ] Crear carpetas de requests (CS/)
- [ ] Crear carpetas de services y repositories (CS/)
- [ ] Crear carpetas de views por módulo (cs/users, cs/dashboard)
- [ ] Crear carpetas de components (cs/)

### ✅ FASE 5: CONFIGURACIÓN

- [ ] Crear `config/cetam.cs.php`
- [ ] Crear `config/icons.php` con catálogo completo
- [ ] Configurar rutas en `routes/web.php`
- [ ] Configurar rutas API en `routes/api.php` (si aplica)

### ✅ FASE 6: MODELOS Y CONTROLADORES

- [ ] Actualizar User Model con relaciones
- [ ] Crear Role Model
- [ ] Crear DashboardController
- [ ] Crear UserController con métodos RESTful
- [ ] Crear RoleController (si aplica)

### ✅ FASE 7: VALIDACIÓN

- [ ] Crear StoreUserRequest
- [ ] Crear UpdateUserRequest
- [ ] Crear otros Form Requests necesarios

### ✅ FASE 8: VISTAS

- [ ] Crear vista dashboard/index.blade.php
- [ ] Crear vista users/index.blade.php
- [ ] Crear vista users/create.blade.php
- [ ] Crear vista users/edit.blade.php
- [ ] Crear vista users/show.blade.php (si aplica)

### ✅ FASE 9: COMPONENTES BLADE

- [ ] Crear componente Icon (clase + vista)
- [ ] Crear componente Alert (clase + vista)
- [ ] Crear otros componentes necesarios (modal, card, etc.)

### ✅ FASE 10: ASSETS

- [ ] Configurar `vite.config.js`
- [ ] Configurar `app.scss` con imports
- [ ] Crear `_custom.scss` con estilos personalizados
- [ ] Configurar `app.js` con librerías
- [ ] Compilar assets: `npm run dev`

### ✅ FASE 11: SERVICIOS (OPCIONAL)

- [ ] Crear UserService (si hay lógica compleja)
- [ ] Crear otros Services necesarios

### ✅ FASE 12: TESTING

- [ ] Probar autenticación de usuarios
- [ ] Probar CRUD de usuarios
- [ ] Probar notificaciones (SweetAlert2, Notyf)
- [ ] Probar componentes Blade
- [ ] Probar responsive design
- [ ] Probar iconos del catálogo

### ✅ FASE 13: DOCUMENTACIÓN

- [ ] Agregar cabeceras a todos los archivos PHP
- [ ] Agregar cabeceras a todos los archivos Blade
- [ ] Documentar métodos con PHPDoc
- [ ] Documentar componentes personalizados

### ✅ FASE 14: OPTIMIZACIÓN

- [ ] Ejecutar `composer dump-autoload`
- [ ] Ejecutar `php artisan config:cache`
- [ ] Ejecutar `php artisan route:cache`
- [ ] Ejecutar `php artisan view:cache`
- [ ] Compilar assets para producción: `npm run build`

---

## COMANDOS ÚTILES

```bash
# Desarrollo
php artisan serve                    # Iniciar servidor
npm run dev                          # Compilar assets (watch)
php artisan migrate:fresh --seed     # Resetear BD

# Generadores
php artisan make:controller CS/Admin/UserController --resource
php artisan make:model User
php artisan make:migration create_users_table
php artisan make:seeder UserSeeder
php artisan make:request CS/StoreUserRequest
php artisan make:component CS/Alert

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Producción
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## NOTAS IMPORTANTES

1. **VERSIONES OBLIGATORIAS**: PHP 8.2.x, Laravel 12.x, Node 22.x, Bootstrap 5.3.x
2. **PREFIJOS**: Usar CETAM_<PROJ>_ en variables de entorno
3. **NOMENCLATURA**: Seguir estrictamente PSR-12 y convenciones del manual
4. **CABECERAS**: Obligatorias en todos los archivos creados manualmente
5. **ICONOS**: Usar solo componente `<x-icon>` con alias del catálogo
6. **COLORES**: Respetar paleta institucional de plantilla Volt
7. **LÍMITE**: Máximo 120 caracteres por línea de código
8. **COMENTARIOS**: Usar inglés técnico en código, español en vistas

---

## ESTRUCTURA FINAL ESPERADA

```
proyecto-laravel/
├── app/
│   ├── Http/Controllers/CS/Admin/
│   ├── Http/Requests/CS/
│   ├── Models/
│   ├── Services/CS/
│   ├── Repositories/CS/
│   └── View/Components/
├── config/
│   ├── cetam.cs.php
│   └── icons.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── sass/
│   │   ├── _variables.scss
│   │   ├── _custom.scss
│   │   └── app.scss
│   ├── js/
│   │   └── app.js
│   └── views/
│       ├── layouts/
│       ├── components/
│       ├── partials/
│       └── modules/cs/
├── routes/
│   ├── web.php
│   └── api.php
└── public/
    └── assets/
```

---

**FIN DEL DOCUMENTO**

Este documento contiene todas las instrucciones necesarias para implementar un proyecto Laravel siguiendo los estándares CETAM. La funcionalidad debe mantenerse intacta durante toda la implementación.

**Creado por:** CETAM Dev Team  
**Versión:** 3.0  
**Fecha:** Noviembre 2025
