# 📦 Guía de Instalación - Order QR System

## 📋 Requisitos Previos

Antes de instalar el proyecto, asegúrate de tener lo siguiente instalado en tu sistema:

### Windows:

- **PHP 8.1 o superior**
- **Composer** (gestor de dependencias de PHP)
- **MySQL 5.7 o superior** (o MariaDB)
- **Laragon** (recomendado) o XAMPP
- **Git**
- **Node.js 16+** (opcional, solo si trabajas con assets)

---

## 🚀 Instalación Paso a Paso

### PASO 1: Clonar el Repositorio

```bash
git clone https://github.com/TU_USUARIO/order-qr-system.git
cd order-qr-system
```

---

### PASO 2: Instalar Dependencias de PHP

```bash
composer install
```

**Si te da error de memoria:**
```bash
php -d memory_limit=-1 C:\ruta\a\composer.phar install
```

---

### PASO 3: Configurar Variables de Entorno

1. **Copiar el archivo de ejemplo:**
   ```bash
   copy .env.example .env
   ```

2. **Editar `.env` con tus datos:**

   Abre el archivo `.env` y configura:

   ```env
   APP_NAME="Order QR System"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=order_qr_system
   DB_USERNAME=root
   DB_PASSWORD=

   # Google Maps API Key (opcional)
   GOOGLE_MAPS_API_KEY=tu_api_key_aqui

   # Stripe (opcional para pagos)
   STRIPE_KEY=pk_test_placeholder
   STRIPE_SECRET=sk_test_placeholder
   STRIPE_WEBHOOK_SECRET=whsec_placeholder
   ```

---

### PASO 4: Generar Key de Aplicación

```bash
php artisan key:generate
```

---

### PASO 5: Crear Base de Datos

**Opción A: Usando Laragon**
1. Abre Laragon
2. Click derecho en icono → MySQL → Create Database
3. Nombre: `order_qr_system`

**Opción B: Usando línea de comandos**
```bash
mysql -u root -p
CREATE DATABASE order_qr_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

---

### PASO 6: Ejecutar Migraciones

```bash
php artisan migrate
```

**Si te pregunta si quieres crear la base de datos, responde `yes`**

---

### PASO 7: Ejecutar Seeders (Datos de Prueba)

```bash
# Seeder del SuperAdmin
php artisan db:seed --class=SuperAdminSeeder

# Seeder de la app móvil (opcional)
php artisan db:seed --class=MobileAppSeeder

# O todos los seeders a la vez
php artisan db:seed
```

**Esto creará:**
- SuperAdministrador con email: `admin@example.com` / password: `password`
- Datos de prueba para el sistema

---

### PASO 8: Crear Link Simbólico para Storage

```bash
php artisan storage:link
```

Esto permite que las imágenes subidas sean accesibles públicamente.

---

### PASO 9: Dar Permisos a Carpetas (Linux/Mac)

**Solo si estás en Linux o Mac:**

```bash
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

**En Windows con Laragon generalmente no es necesario.**

---

### PASO 10: Iniciar el Servidor

**Opción A: Servidor de desarrollo de Laravel**
```bash
php artisan serve
```

Visita: `http://localhost:8000`

**Opción B: Usar Laragon**
1. Abre Laragon
2. Click en "Start All"
3. Visita: `http://order-qr.test`

---

## 🌐 Configuración de ngrok (Para desarrollo remoto)

Si quieres que la aplicación sea accesible desde internet (por ejemplo, para probar con la app móvil):

### 1. Instalar ngrok

Descarga de: https://ngrok.com/download

### 2. Configurar authtoken

```bash
ngrok config add-authtoken TU_TOKEN_AQUI
```

Obtén tu token de: https://dashboard.ngrok.com/get-started/your-authtoken

### 3. Obtener dominio gratuito (opcional pero recomendado)

1. Ve a: https://dashboard.ngrok.com/cloud-edge/domains
2. Click en "Create Domain"
3. Copia tu dominio (ej: `tu-proyecto.ngrok-free.dev`)

### 4. Iniciar ngrok con dominio fijo

```bash
ngrok http 8000 --domain=tu-proyecto.ngrok-free.dev
```

### 5. Actualizar .env

```env
APP_URL=https://tu-proyecto.ngrok-free.dev
```

### 6. Limpiar caché

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📱 Configuración de la App Móvil (Flutter)

Si vas a usar la app móvil:

1. **En el proyecto Flutter, actualiza la URL del API:**

   Archivo: `lib/services/api_service.dart`

   ```dart
   static const String baseUrl = "https://tu-proyecto.ngrok-free.dev/api/v1";
   ```

2. **Reconstruir la app:**
   ```bash
   flutter clean
   flutter pub get
   flutter run
   ```

---

## 🔐 Credenciales de Prueba

### Panel de SuperAdministrador
- URL: `http://localhost:8000/superadmin/login`
- Email: `admin@example.com`
- Password: `password`

### Panel de Negocio
- URL: `http://localhost:8000/business/login`
- Email: `test@example.com`
- Password: `password123`

**IMPORTANTE:** Cambia estas credenciales en producción.

---

## 🧪 Verificar Instalación

### 1. Verificar que Laravel está corriendo

```bash
curl http://localhost:8000/api/server-info
```

Deberías ver un JSON con información del servidor.

### 2. Verificar rutas API

```bash
php artisan route:list --path=api
```

### 3. Verificar base de datos

```bash
php artisan tinker
>>> \App\Models\SuperAdmin::count()
```

Debería devolver 2 (dos superadmins del seeder).

---

## 📂 Estructura del Proyecto

```
order-qr-system/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/V1/         # Controladores API para móvil
│   │   ├── SuperAdmin/     # Controladores del SuperAdmin
│   │   └── ...
│   ├── Models/
│   │   ├── Business.php
│   │   ├── Order.php
│   │   ├── MobileUser.php
│   │   └── SuperAdmin.php
│   └── ...
├── database/
│   ├── migrations/         # Migraciones de BD
│   └── seeders/           # Datos de prueba
├── routes/
│   ├── web.php            # Rutas web
│   └── api.php            # Rutas API
├── resources/
│   └── views/             # Vistas Blade
├── storage/
│   ├── app/
│   │   └── qr_codes/     # Códigos QR generados
│   └── logs/
├── .env.example           # Plantilla de configuración
├── composer.json          # Dependencias PHP
└── README.md
```

---

## 🔧 Comandos Útiles

### Limpiar caché

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Refrescar base de datos (CUIDADO: borra todo)

```bash
php artisan migrate:fresh --seed
```

### Ver logs en tiempo real

```bash
tail -f storage/logs/laravel.log
```

### Generar QR de órdenes

```bash
php artisan tinker
>>> $order = \App\Models\Order::find(1);
>>> $order->generateQrCode();
```

---

## 🐛 Solución de Problemas

### Error: "Class not found"

```bash
composer dump-autoload
```

### Error: "Access denied for user"

Verifica tus credenciales en `.env`:
- `DB_USERNAME` debe ser tu usuario de MySQL (generalmente `root`)
- `DB_PASSWORD` debe estar vacío en Laragon o ser tu contraseña

### Error: "SQLSTATE[HY000] [2002]"

MySQL no está corriendo. Si usas Laragon:
1. Abre Laragon
2. Click en "Start All"

### Error: "storage/logs/laravel.log could not be opened"

```bash
# Windows
mkdir storage\logs
# Linux/Mac
mkdir -p storage/logs
```

### Error: "The stream or file could not be opened in append mode"

Da permisos a la carpeta storage:
```bash
# Linux/Mac
sudo chmod -R 775 storage
```

### Puerto 8000 ya está en uso

```bash
# Cambiar puerto
php artisan serve --port=8001

# O matar proceso que usa el puerto
# Windows
netstat -ano | findstr :8000
taskkill /F /PID [PID]

# Linux/Mac
lsof -ti:8000 | xargs kill -9
```

---

## 📚 Documentación Adicional

- **Documentación de Laravel:** https://laravel.com/docs
- **Configuración de ngrok:** Ver `NGROK_SETUP.md`
- **API Móvil:** Ver `MOBILE_API_README.md`
- **Especificaciones:** Ver `MOBILE_APP_SPECIFICATIONS.md`

---

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver archivo `LICENSE` para más detalles.

---

## 👨‍💻 Autor

**CETAM - Centro de Desarrollo Tecnológico Aplicado de México**

---

## 📞 Soporte

Si tienes problemas:

1. Revisa la sección **Solución de Problemas** arriba
2. Verifica los logs: `storage/logs/laravel.log`
3. Abre un issue en GitHub con:
   - Descripción del problema
   - Pasos para reproducir
   - Logs de error
   - Versión de PHP y Laravel

---

## ✅ Checklist de Instalación

- [ ] PHP 8.1+ instalado
- [ ] Composer instalado
- [ ] MySQL/Laragon corriendo
- [ ] Proyecto clonado
- [ ] `composer install` ejecutado
- [ ] `.env` configurado
- [ ] `php artisan key:generate` ejecutado
- [ ] Base de datos creada
- [ ] Migraciones ejecutadas (`php artisan migrate`)
- [ ] Seeders ejecutados (`php artisan db:seed`)
- [ ] Storage link creado (`php artisan storage:link`)
- [ ] Servidor corriendo (`php artisan serve`)
- [ ] Login funciona con credenciales de prueba

---

**¡Listo para usar!** 🚀

Si todo funcionó correctamente, deberías poder acceder a:
- Panel Web: `http://localhost:8000/business/login`
- SuperAdmin: `http://localhost:8000/superadmin/login`
- API: `http://localhost:8000/api/v1/`
