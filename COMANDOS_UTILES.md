# 🛠️ Comandos Útiles - Sistema de Órdenes Móvil

## 🚀 Iniciar Servidor

### Laravel Local
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### Ngrok (Túnel para Testing)
```bash
ngrok http 8000
```

---

## 🗄️ Base de Datos

### Migraciones
```bash
# Ejecutar migraciones pendientes
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# Rehacer todas las migraciones (¡CUIDADO! Borra datos)
php artisan migrate:fresh

# Rehacer migraciones con seeders
php artisan migrate:fresh --seed
```

### Inspeccionar Base de Datos
```bash
# Acceder a Tinker (consola interactiva)
php artisan tinker

# En Tinker:
>>> App\Models\MobileUser::count()
>>> App\Models\Order::where('mobile_user_id', 12)->get()
>>> App\Models\Business::all()
```

### Queries Útiles en Tinker
```php
# Ver todos los usuarios móviles
App\Models\MobileUser::all();

# Buscar usuario por email
App\Models\MobileUser::where('email', 'prueba2@outlook.com')->first();

# Ver órdenes de un usuario
App\Models\Order::where('mobile_user_id', 12)->with('items')->get();

# Contar órdenes por estado
App\Models\Order::groupBy('status')->selectRaw('status, count(*) as total')->get();

# Ver negocios activos
App\Models\Business::where('is_active', 1)->get();
```

---

## 📋 Logs

### Ver Logs en Tiempo Real
```bash
# Windows PowerShell
Get-Content storage/logs/laravel.log -Wait -Tail 50

# Windows CMD
powershell -command "Get-Content storage/logs/laravel.log -Wait -Tail 50"

# Git Bash o WSL
tail -f storage/logs/laravel.log
```

### Ver Últimas Líneas
```bash
# Últimas 50 líneas
tail -50 storage/logs/laravel.log

# Últimas 100 líneas
tail -100 storage/logs/laravel.log
```

### Buscar en Logs
```bash
# Buscar errores
grep "ERROR" storage/logs/laravel.log

# Buscar por fecha específica
grep "2025-11-27" storage/logs/laravel.log

# Buscar palabra clave
grep "ASOCIAR ORDEN" storage/logs/laravel.log
```

### Limpiar Logs
```bash
# Vaciar archivo de logs
echo "" > storage/logs/laravel.log

# O en Windows CMD
type nul > storage/logs/laravel.log
```

---

## 🧹 Limpiar Cache

```bash
# Limpiar caché de aplicación
php artisan cache:clear

# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear

# Limpiar caché de vistas
php artisan view:clear

# Limpiar todo
php artisan optimize:clear
```

---

## 🔐 Sanctum

### Generar Token Manualmente
```bash
php artisan tinker

# En Tinker:
>>> $user = App\Models\MobileUser::find(12);
>>> $token = $user->createToken('test-token');
>>> echo $token->plainTextToken;
```

### Ver Tokens Activos
```bash
php artisan tinker

# En Tinker:
>>> $user = App\Models\MobileUser::find(12);
>>> $user->tokens;
```

### Revocar Todos los Tokens de un Usuario
```bash
php artisan tinker

# En Tinker:
>>> $user = App\Models\MobileUser::find(12);
>>> $user->tokens()->delete();
```

---

## 🧪 Testing con cURL

### Variables de Entorno
```bash
# Guardar en variables (Git Bash o PowerShell)
export NGROK_URL="https://gerald-ironical-contradictorily.ngrok-free.dev"
export TOKEN="tu_token_aqui"

# PowerShell
$NGROK_URL = "https://gerald-ironical-contradictorily.ngrok-free.dev"
$TOKEN = "tu_token_aqui"
```

### Login
```bash
curl -X POST "$NGROK_URL/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true" \
  -d "{\"email\":\"prueba2@outlook.com\",\"password\":\"123456\",\"device_id\":\"test-001\"}"
```

### Listar Órdenes
```bash
curl -X GET "$NGROK_URL/api/v1/mobile/orders" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true"
```

### Asociar Orden
```bash
curl -X POST "$NGROK_URL/api/v1/mobile/orders/associate" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "ngrok-skip-browser-warning: true" \
  -d "{\"qr_token\":\"ABC123XYZ\"}"
```

---

## 📊 Rutas

### Listar Todas las Rutas
```bash
php artisan route:list
```

### Filtrar Rutas API
```bash
php artisan route:list --path=api
```

### Buscar Ruta Específica
```bash
php artisan route:list --name=mobile
```

---

## 🔍 Verificar Configuración

### Ver Variables de Entorno
```bash
php artisan tinker

# En Tinker:
>>> env('APP_URL')
>>> env('DB_DATABASE')
>>> config('app.name')
```

### Ver Configuración Completa
```bash
php artisan config:show
```

### Verificar Base de Datos
```bash
php artisan db:show
```

---

## 🗂️ Seeders

### Crear Seeder
```bash
php artisan make:seeder BusinessSeeder
```

### Ejecutar Seeder
```bash
php artisan db:seed --class=BusinessSeeder
```

### Ejecutar Todos los Seeders
```bash
php artisan db:seed
```

---

## 🔨 Crear Componentes

### Crear Migración
```bash
php artisan make:migration create_table_name
```

### Crear Modelo con Todo
```bash
php artisan make:model ModelName -mfsc
# -m: migration
# -f: factory
# -s: seeder
# -c: controller
```

### Crear Controlador
```bash
php artisan make:controller Api/V1/NombreController
```

### Crear Request
```bash
php artisan make:request NombreRequest
```

---

## 🛠️ Mantenimiento

### Optimizar para Producción
```bash
# Cachear configuración
php artisan config:cache

# Cachear rutas
php artisan route:cache

# Cachear vistas
php artisan view:cache

# Optimizar autoload
composer dump-autoload -o
```

### Deshacer Optimizaciones (Desarrollo)
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🔐 Permisos (Linux/Mac)

```bash
# Dar permisos a storage y bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📦 Composer

### Instalar Dependencias
```bash
composer install
```

### Actualizar Dependencias
```bash
composer update
```

### Instalar Paquete Específico
```bash
composer require vendor/package
```

### Remover Paquete
```bash
composer remove vendor/package
```

---

## 🔍 Debug

### Modo Debug ON
```bash
# En .env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Inspeccionar Query SQL
```bash
php artisan tinker

# En Tinker:
>>> DB::enableQueryLog();
>>> App\Models\Order::where('mobile_user_id', 12)->get();
>>> DB::getQueryLog();
```

### Ver Errores Detallados
```bash
# En .env
APP_DEBUG=true

# Luego hacer request y ver logs
tail -f storage/logs/laravel.log
```

---

## 🌐 Testing de API

### Instalar HTTPie (Alternativa a cURL)
```bash
# Windows con Scoop
scoop install httpie

# Usar HTTPie
http POST https://ngrok-url/api/v1/auth/login \
  email=test@test.com \
  password=123456
```

### Postman Collection Export
```bash
# Exportar rutas para Postman
php artisan route:list --json > routes.json
```

---

## 🔄 Git

### Ver Cambios
```bash
git status
git diff
```

### Commit
```bash
git add .
git commit -m "Mensaje del commit"
```

### Ver Historial
```bash
git log --oneline -10
```

---

## 📱 Firebase (Cuando esté configurado)

### Verificar Credenciales
```bash
php artisan tinker

# En Tinker:
>>> config('services.firebase.credentials')
>>> file_exists(storage_path('firebase-credentials.json'))
```

---

## 🧪 Testing Rápido

### Test Completo del Sistema
```bash
# 1. Login
curl -X POST "$NGROK_URL/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"prueba2@outlook.com\",\"password\":\"123456\"}" \
  | jq '.data.token'

# 2. Guardar token (reemplazar con el token real)
export TOKEN="token_obtenido"

# 3. Ver órdenes
curl -X GET "$NGROK_URL/api/v1/mobile/orders" \
  -H "Authorization: Bearer $TOKEN" \
  | jq '.'

# 4. Ver negocios
curl -X GET "$NGROK_URL/api/v1/businesses" \
  | jq '.data.businesses'
```

---

## 💾 Backup

### Backup de Base de Datos (MySQL)
```bash
# Exportar
mysqldump -u root volt_dashboard > backup_$(date +%Y%m%d).sql

# Importar
mysql -u root volt_dashboard < backup_20251127.sql
```

### Backup de Archivos
```bash
# Comprimir proyecto
tar -czf backup_proyecto_$(date +%Y%m%d).tar.gz \
  --exclude=node_modules \
  --exclude=vendor \
  --exclude=storage/logs \
  .
```

---

## 📊 Monitoreo

### Ver Procesos de Laravel
```bash
# Windows
tasklist | findstr php

# Linux/Mac
ps aux | grep php
```

### Matar Proceso
```bash
# Windows (reemplazar PID)
taskkill /F /PID 12345

# Linux/Mac
kill -9 12345
```

---

## 🎯 Atajos Útiles

### Alias para Comandos Frecuentes
```bash
# Agregar a ~/.bashrc o ~/.zshrc
alias pa='php artisan'
alias pam='php artisan migrate'
alias pas='php artisan serve'
alias pat='php artisan tinker'
alias pao='php artisan optimize:clear'
```

---

## 🔧 Solución de Problemas Comunes

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: "SQLSTATE connection refused"
```bash
# Verificar MySQL está corriendo
# Windows: services.msc
# Linux: sudo service mysql status
```

### Error: "419 CSRF token mismatch"
```bash
php artisan config:clear
php artisan cache:clear
```

### Error: "Storage permission denied"
```bash
# Windows: ejecutar como administrador
# Linux/Mac:
chmod -R 775 storage
```

---

## 📚 Referencias Rápidas

### Laravel Artisan
```bash
php artisan list
php artisan help migrate
```

### Composer
```bash
composer --version
composer show
```

### Ver Versión de PHP
```bash
php -v
php -m  # Ver módulos instalados
```

---

**Última Actualización**: 28 de Noviembre 2025
**Generado por**: Claude Code
