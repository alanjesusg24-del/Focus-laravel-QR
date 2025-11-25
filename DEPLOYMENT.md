# 🚀 Guía de Despliegue - Order QR System

Esta guía detalla cómo desplegar el sistema Order QR en diferentes entornos.

---

## 📋 Tabla de Contenidos

- [Preparación para Producción](#preparación-para-producción)
- [Despliegue en Servidor VPS](#despliegue-en-servidor-vps)
- [Despliegue en Hosting Compartido](#despliegue-en-hosting-compartido)
- [Despliegue con Docker](#despliegue-con-docker)
- [Configuración de SSL](#configuración-de-ssl)
- [Optimizaciones de Producción](#optimizaciones-de-producción)
- [Backups y Mantenimiento](#backups-y-mantenimiento)

---

## 🛠 Preparación para Producción

### 1. Configurar Variables de Entorno

```env
# .env (PRODUCCIÓN)
APP_NAME="Order QR System"
APP_ENV=production
APP_DEBUG=false  # ⚠️ IMPORTANTE: false en producción
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_base_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=contraseña_segura

# Credenciales de Producción MercadoPago
MERCADOPAGO_PUBLIC_KEY=APP-xxxxxxxxxxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=APP-xxxxxxxxxxxxxxxx
MERCADOPAGO_MODE=production

# Google Maps API (con restricciones de dominio)
GOOGLE_MAPS_API_KEY=tu_api_key_produccion
```

### 2. Optimizar Dependencias

```bash
# Instalar solo dependencias de producción
composer install --optimize-autoloader --no-dev

# Compilar assets para producción
npm run production
```

### 3. Generar Clave de Aplicación

```bash
php artisan key:generate
```

### 4. Caché de Configuración

```bash
# Cachear configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimizar autoloader
composer dump-autoload --optimize
```

---

## 🖥️ Despliegue en Servidor VPS

### Requisitos del Servidor

- **Sistema Operativo:** Ubuntu 22.04 LTS (recomendado)
- **RAM:** Mínimo 2GB
- **Almacenamiento:** 20GB SSD
- **Acceso:** SSH root o sudo

### Paso 1: Conectar al Servidor

```bash
ssh usuario@tu-servidor.com
```

### Paso 2: Actualizar Sistema

```bash
sudo apt update && sudo apt upgrade -y
```

### Paso 3: Instalar Stack LAMP

#### Instalar PHP 8.2

```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml \
php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd \
php8.2-bcmath php8.2-tokenizer -y
```

#### Instalar MySQL 8.0

```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

#### Configurar Base de Datos

```bash
sudo mysql -u root -p

CREATE DATABASE order_qr_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'orderqr_user'@'localhost' IDENTIFIED BY 'contraseña_segura_aqui';
GRANT ALL PRIVILEGES ON order_qr_system.* TO 'orderqr_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Instalar Nginx

```bash
sudo apt install nginx -y
sudo systemctl start nginx
sudo systemctl enable nginx
```

### Paso 4: Instalar Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### Paso 5: Instalar Node.js y npm

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install nodejs -y
node --version
npm --version
```

### Paso 6: Clonar Proyecto

```bash
cd /var/www/
sudo git clone https://github.com/tu-usuario/order-qr-system.git
sudo chown -R www-data:www-data order-qr-system
cd order-qr-system
```

### Paso 7: Configurar Proyecto

```bash
# Copiar variables de entorno
sudo cp .env.example .env
sudo nano .env  # Editar con credenciales de producción

# Instalar dependencias
composer install --optimize-autoloader --no-dev
npm ci --production

# Compilar assets
npm run production

# Configurar Laravel
php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed --force  # Solo si quieres datos de prueba

# Optimizaciones
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 8: Configurar Nginx

```bash
sudo nano /etc/nginx/sites-available/orderqr
```

**Contenido del archivo:**

```nginx
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/order-qr-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Activar sitio:**

```bash
sudo ln -s /etc/nginx/sites-available/orderqr /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Paso 9: Configurar Permisos

```bash
cd /var/www/order-qr-system
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

### Paso 10: Configurar Firewall

```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

---

## 🏠 Despliegue en Hosting Compartido

### Requisitos

- PHP 8.2+ con extensiones requeridas
- Acceso SSH o File Manager
- MySQL 8.0+
- Soporte para Laravel

### Método 1: Via SSH

```bash
# 1. Conectar
ssh usuario@tu-hosting.com

# 2. Navegar a directorio web
cd public_html

# 3. Clonar proyecto
git clone https://github.com/tu-usuario/order-qr-system.git
cd order-qr-system

# 4. Configurar
composer install --optimize-autoloader --no-dev
cp .env.example .env
nano .env  # Configurar

php artisan key:generate
php artisan migrate --force
php artisan storage:link

# 5. Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Método 2: Via File Manager (cPanel)

1. **Comprimir proyecto localmente:**
   ```bash
   git archive --format=zip --output=orderqr.zip HEAD
   ```

2. **Subir ZIP a hosting** via File Manager

3. **Descomprimir** en `public_html/`

4. **Configurar via SSH** o **Terminal de cPanel**

5. **Crear enlace simbólico:**
   - Renombrar `public` a `public_laravel`
   - Mover contenido de `public_laravel` a `public_html`
   - Editar `index.php` ajustando rutas

### Configurar .htaccess

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## 🐳 Despliegue con Docker

### Crear docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    image: orderqr-app
    container_name: orderqr_app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - orderqr-network

  nginx:
    image: nginx:alpine
    container_name: orderqr_nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - orderqr-network

  mysql:
    image: mysql:8.0
    container_name: orderqr_mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: order_qr_system
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_USER: orderqr_user
      MYSQL_PASSWORD: orderqr_password
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - orderqr-network

networks:
  orderqr-network:
    driver: bridge

volumes:
  mysql-data:
    driver: local
```

### Crear Dockerfile

```dockerfile
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar proyecto
COPY . /var/www

# Instalar dependencias
RUN composer install --optimize-autoloader --no-dev

# Permisos
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

### Desplegar

```bash
# Construir y levantar contenedores
docker-compose up -d --build

# Ejecutar migraciones
docker-compose exec app php artisan migrate --force

# Ver logs
docker-compose logs -f app
```

---

## 🔒 Configuración de SSL

### Opción 1: Let's Encrypt (Certbot)

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtener certificado
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

# Renovación automática (ya configurada)
sudo systemctl status certbot.timer
```

### Opción 2: SSL Compartido (cPanel)

1. Panel de control → SSL/TLS
2. AutoSSL → Habilitar
3. Esperar generación automática

### Verificar SSL

```bash
openssl s_client -connect tu-dominio.com:443
```

---

## ⚡ Optimizaciones de Producción

### 1. Optimizar OPcache

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.validate_timestamps=0
opcache.fast_shutdown=1
```

### 2. Configurar Queue Worker

```bash
sudo nano /etc/supervisor/conf.d/orderqr-worker.conf
```

```ini
[program:orderqr-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/order-qr-system/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/order-qr-system/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start orderqr-worker:*
```

### 3. Configurar Cron Jobs

```bash
sudo crontab -e -u www-data
```

```cron
* * * * * cd /var/www/order-qr-system && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Habilitar Compresión Gzip

```nginx
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss;
```

---

## 💾 Backups y Mantenimiento

### Backup Automático de Base de Datos

```bash
#!/bin/bash
# /usr/local/bin/backup-orderqr.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/orderqr"
DB_NAME="order_qr_system"
DB_USER="orderqr_user"
DB_PASS="tu_password"

mkdir -p $BACKUP_DIR

# Backup MySQL
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# Comprimir
gzip $BACKUP_DIR/db_$DATE.sql

# Eliminar backups antiguos (más de 30 días)
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Backup completado: db_$DATE.sql.gz"
```

**Programar backup diario:**

```bash
sudo chmod +x /usr/local/bin/backup-orderqr.sh
sudo crontab -e
```

```cron
0 2 * * * /usr/local/bin/backup-orderqr.sh >> /var/log/orderqr-backup.log 2>&1
```

### Monitoreo de Logs

```bash
# Ver logs de Laravel
tail -f /var/www/order-qr-system/storage/logs/laravel.log

# Ver logs de Nginx
tail -f /var/log/nginx/error.log

# Ver logs de PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

### Actualizar Aplicación

```bash
cd /var/www/order-qr-system

# Poner en modo mantenimiento
php artisan down

# Actualizar código
git pull origin main

# Actualizar dependencias
composer install --optimize-autoloader --no-dev
npm ci --production
npm run production

# Ejecutar migraciones
php artisan migrate --force

# Limpiar cachés
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Salir de modo mantenimiento
php artisan up
```

---

## 🔍 Monitoreo y Debugging

### Herramientas Recomendadas

- **Laravel Telescope** - Debug y profiling
- **Sentry** - Error tracking
- **New Relic** - Application monitoring
- **UptimeRobot** - Monitoring de uptime

### Habilitar Error Logging

```env
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### Configurar Sentry (Opcional)

```bash
composer require sentry/sentry-laravel
```

```env
SENTRY_LARAVEL_DSN=https://xxxxx@sentry.io/xxxxx
```

---

## ✅ Checklist Pre-Despliegue

- [ ] `.env` configurado para producción
- [ ] `APP_DEBUG=false`
- [ ] Base de datos creada y configurada
- [ ] Migraciones ejecutadas
- [ ] Storage enlazado (`storage:link`)
- [ ] Assets compilados (`npm run production`)
- [ ] Cachés generados (config, route, view)
- [ ] Permisos configurados (775 storage)
- [ ] SSL instalado y verificado
- [ ] Backups automatizados configurados
- [ ] Cron jobs configurados
- [ ] Firewall configurado
- [ ] Logs monitoreados

---

<div align="center">

**© 2025 CETAM - Guía de Despliegue Order QR System**

</div>
