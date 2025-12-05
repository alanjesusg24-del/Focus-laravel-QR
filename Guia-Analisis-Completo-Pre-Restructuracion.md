# Guía de Análisis Completo del Proyecto - Focus QR System
## Pre-Requisito para Reestructuración según Estándar CETAM

**Proyecto:** Focus QR System  
**Código:** FQR  
**Propósito:** Analizar COMPLETAMENTE el proyecto actual antes de realizar cualquier cambio estructural

---

## ⚠️ ADVERTENCIA CRÍTICA

**NO MUEVAS NINGÚN ARCHIVO** hasta completar este análisis exhaustivo.

La descripción general que proporcionaste es solo una visión de alto nivel. Para una migración **exitosa y sin errores**, necesitamos:

1. ✅ **Inventario completo** de todos los archivos
2. ✅ **Mapeo de dependencias** entre archivos
3. ✅ **Identificación de funcionalidades** no mencionadas
4. ✅ **Análisis de integraciones** con servicios externos
5. ✅ **Detección de código personalizado** fuera del estándar
6. ✅ **Comprensión de la lógica de negocio** real implementada

---

## 📋 ÍNDICE

1. [Análisis Inicial del Proyecto](#1-análisis-inicial-del-proyecto)
2. [Inventario Completo de Archivos](#2-inventario-completo-de-archivos)
3. [Análisis de Controllers](#3-análisis-de-controllers)
4. [Análisis de Models y Relaciones](#4-análisis-de-models-y-relaciones)
5. [Análisis de Rutas](#5-análisis-de-rutas)
6. [Análisis de Vistas](#6-análisis-de-vistas)
7. [Análisis de Middleware y Autenticación](#7-análisis-de-middleware-y-autenticación)
8. [Análisis de Servicios Externos](#8-análisis-de-servicios-externos)
9. [Análisis de Base de Datos](#9-análisis-de-base-de-datos)
10. [Análisis de Dependencias](#10-análisis-de-dependencias)
11. [Análisis de API para Flutter](#11-análisis-de-api-para-flutter)
12. [Análisis de Jobs y Colas](#12-análisis-de-jobs-y-colas)
13. [Análisis de Assets y Frontend](#13-análisis-de-assets-y-frontend)
14. [Generación de Mapa de Reestructuración](#14-generación-de-mapa-de-reestructuración)

---

## 1. ANÁLISIS INICIAL DEL PROYECTO

### 🔍 Comandos de Exploración Básica

```bash
# 1. Ver estructura completa del proyecto
tree -L 3 -I 'vendor|node_modules|storage'

# 2. Contar archivos por tipo
echo "Controllers:"
find app/Http/Controllers -name "*.php" | wc -l

echo "Models:"
find app/Models -name "*.php" 2>/dev/null | wc -l
find app -name "*.php" -path "*/Models/*" | wc -l

echo "Views:"
find resources/views -name "*.blade.php" | wc -l

echo "Migrations:"
find database/migrations -name "*.php" | wc -l

echo "Routes:"
wc -l routes/*.php

# 3. Ver tamaño del proyecto
du -sh .
du -sh app/
du -sh resources/
du -sh database/

# 4. Versiones actuales
php -v
php artisan --version
composer show laravel/framework
```

### 📊 Generar Reporte Inicial

**Crear archivo:** `project-analysis.txt`

```bash
# Ejecutar este comando para generar un reporte completo
cat > project-analysis.txt << 'EOF'
==============================================
ANÁLISIS DEL PROYECTO FOCUS QR SYSTEM
==============================================

FECHA: $(date)

1. ESTRUCTURA DEL PROYECTO
--------------------------
EOF

tree -L 3 -I 'vendor|node_modules|storage' >> project-analysis.txt

echo "" >> project-analysis.txt
echo "2. ARCHIVOS PHP" >> project-analysis.txt
echo "--------------------------" >> project-analysis.txt
find . -name "*.php" -not -path "./vendor/*" -not -path "./storage/*" >> project-analysis.txt

echo "" >> project-analysis.txt
echo "3. VISTAS BLADE" >> project-analysis.txt
echo "--------------------------" >> project-analysis.txt
find resources/views -name "*.blade.php" >> project-analysis.txt

echo "Reporte generado en: project-analysis.txt"
```

---

## 2. INVENTARIO COMPLETO DE ARCHIVOS

### 📂 Listar TODOS los Controllers

```bash
# Ejecutar desde la raíz del proyecto
echo "=== CONTROLLERS EXISTENTES ===" > controllers-inventory.txt
find app/Http/Controllers -name "*.php" -type f | while read file; do
    echo "" >> controllers-inventory.txt
    echo "Archivo: $file" >> controllers-inventory.txt
    echo "Namespace:" >> controllers-inventory.txt
    grep -m 1 "^namespace" "$file" >> controllers-inventory.txt
    echo "Clase:" >> controllers-inventory.txt
    grep -m 1 "^class" "$file" >> controllers-inventory.txt
    echo "Métodos públicos:" >> controllers-inventory.txt
    grep "public function" "$file" >> controllers-inventory.txt
    echo "---" >> controllers-inventory.txt
done

cat controllers-inventory.txt
```

**IMPORTANTE:** Revisa este archivo completo. Pueden existir controllers que no conoces:
- `AuthController`
- `DashboardController`
- `ReportController`
- `SettingsController`
- `UserController`
- `RoleController`
- etc.

### 📂 Listar TODOS los Models

```bash
echo "=== MODELS EXISTENTES ===" > models-inventory.txt

# Buscar en app/Models
if [ -d "app/Models" ]; then
    find app/Models -name "*.php" -type f | while read file; do
        echo "" >> models-inventory.txt
        echo "Archivo: $file" >> models-inventory.txt
        grep -m 1 "^class" "$file" >> models-inventory.txt
        echo "Relaciones:" >> models-inventory.txt
        grep "public function.*\()" "$file" | grep -E "(belongsTo|hasMany|hasOne|belongsToMany)" >> models-inventory.txt
        echo "---" >> models-inventory.txt
    done
fi

# Buscar en app/ (Laravel antiguo)
find app -name "*.php" -type f -not -path "*/Http/*" -not -path "*/Console/*" | while read file; do
    if grep -q "extends Model" "$file"; then
        echo "" >> models-inventory.txt
        echo "Archivo: $file" >> models-inventory.txt
        grep -m 1 "^class" "$file" >> models-inventory.txt
        echo "---" >> models-inventory.txt
    fi
done

cat models-inventory.txt
```

### 📂 Listar TODAS las Vistas

```bash
echo "=== VISTAS EXISTENTES ===" > views-inventory.txt

find resources/views -name "*.blade.php" | while read file; do
    echo "$file" >> views-inventory.txt
    
    # Detectar @extends
    extends=$(grep "@extends" "$file" | head -1)
    if [ ! -z "$extends" ]; then
        echo "  Extiende: $extends" >> views-inventory.txt
    fi
    
    # Detectar @include
    includes=$(grep "@include" "$file" | wc -l)
    if [ $includes -gt 0 ]; then
        echo "  Includes: $includes archivos" >> views-inventory.txt
    fi
    
    # Detectar componentes <x-
    components=$(grep -o "<x-[a-zA-Z-]*" "$file" | sort -u)
    if [ ! -z "$components" ]; then
        echo "  Componentes usados:" >> views-inventory.txt
        echo "$components" >> views-inventory.txt
    fi
    
    echo "" >> views-inventory.txt
done

cat views-inventory.txt
```

---

## 3. ANÁLISIS DE CONTROLLERS

### 🔍 Análisis Detallado por Controller

Para **CADA controller** que encuentres, completa esta tabla:

| Controller | Métodos | Usa Models | Usa Services | Retorna Vistas | API JSON | Notas |
|------------|---------|------------|--------------|----------------|----------|-------|
| OrderController | index, create, store, show, edit, update, destroy | Order, QrCode | OrderService | orders.index, orders.create | No | CRUD completo |
| QrController | ? | ? | ? | ? | ? | **ANALIZAR** |
| DeviceController | ? | ? | ? | ? | ? | **ANALIZAR** |
| ... | ... | ... | ... | ... | ... | ... |

### 📝 Script de Análisis de Controller

```bash
# Analizar un controller específico
analyze_controller() {
    local file=$1
    echo "========================================"
    echo "ANÁLISIS: $file"
    echo "========================================"
    echo ""
    
    echo "NAMESPACE:"
    grep "^namespace" "$file"
    echo ""
    
    echo "IMPORTS (USE):"
    grep "^use" "$file"
    echo ""
    
    echo "CLASE:"
    grep "^class" "$file"
    echo ""
    
    echo "PROPIEDADES:"
    grep "private\|protected\|public" "$file" | grep -v "function"
    echo ""
    
    echo "MÉTODOS:"
    grep "public function" "$file"
    echo ""
    
    echo "VISTAS RETORNADAS:"
    grep "return view(" "$file" | sed 's/.*view(\x27//;s/\x27.*//' | sed "s/.*view('//;s/'.*//" | sed 's/.*view("//;s/".*//'
    echo ""
    
    echo "RUTAS USADAS:"
    grep "route(" "$file" | head -10
    echo ""
    
    echo "REDIRECCIONES:"
    grep "redirect()" "$file"
    echo ""
    
    echo "VALIDACIONES:"
    grep "validate\|Requests" "$file"
    echo ""
}

# Ejecutar para cada controller
for controller in app/Http/Controllers/*.php; do
    if [ -f "$controller" ]; then
        analyze_controller "$controller" > "analysis-$(basename $controller).txt"
    fi
done

# También para subdirectorios
find app/Http/Controllers -name "*.php" -type f | while read controller; do
    analyze_controller "$controller" > "analysis-$(basename $controller).txt"
done
```

### ❓ Preguntas Críticas para Cada Controller

Para cada controller encontrado, responde:

1. **¿Qué hace este controller?**
   - Descripción de funcionalidad

2. **¿Qué modelos utiliza?**
   - Lista de modelos

3. **¿Tiene lógica de negocio en el controller?**
   - Sí/No
   - Si sí: ¿Qué lógica? → Debe moverse a Service

4. **¿Retorna vistas o JSON?**
   - Vistas: ¿Cuáles?
   - JSON: ¿Para qué? (API móvil, AJAX, etc.)

5. **¿Qué nombre debería tener según el estándar?**
   - Nombre actual vs nombre estándar

6. **¿En qué subcarpeta debe estar?**
   - app/Http/Controllers/[¿Qué?]/

---

## 4. ANÁLISIS DE MODELS Y RELACIONES

### 🔍 Script de Análisis de Modelos

```bash
analyze_model() {
    local file=$1
    local model_name=$(basename "$file" .php)
    
    echo "========================================"
    echo "MODELO: $model_name"
    echo "========================================"
    echo ""
    
    echo "TABLA:"
    grep "protected \$table" "$file" || echo "  (usa nombre por defecto: $(echo $model_name | sed 's/\(.*\)/\L\1/' | sed 's/\(.*\)/\1s/'))"
    echo ""
    
    echo "FILLABLE:"
    grep -A 20 "protected \$fillable" "$file" | grep -E "'\w+'" | head -20
    echo ""
    
    echo "CASTS:"
    grep -A 10 "protected \$casts" "$file" | grep -E "'\w+'" | head -10
    echo ""
    
    echo "RELACIONES:"
    grep "public function.*\()" "$file" | grep -v "__construct"
    echo ""
    
    echo "SCOPES:"
    grep "public function scope" "$file"
    echo ""
    
    echo "CONSTANTES:"
    grep "const " "$file"
    echo ""
}

# Analizar todos los modelos
find app/Models -name "*.php" -type f 2>/dev/null | while read model; do
    analyze_model "$model" > "model-analysis-$(basename $model).txt"
done

# Si los modelos están en app/
find app -maxdepth 1 -name "*.php" -type f | while read file; do
    if grep -q "extends Model" "$file"; then
        analyze_model "$file" > "model-analysis-$(basename $file).txt"
    fi
done
```

### 📊 Tabla de Relaciones

Construye esta tabla con **TODOS** tus modelos:

| Modelo | Tabla | Relaciones | Scopes | Constantes | Notas |
|--------|-------|------------|--------|------------|-------|
| Order | orders | qrCode (hasOne), device (belongsTo), notifications (hasMany), delivery (hasOne) | pending, ready, delivered | STATUS_PENDING, STATUS_READY, etc. | ✅ Completo |
| QrCode | ? | ? | ? | ? | **ANALIZAR** |
| Device | ? | ? | ? | ? | **ANALIZAR** |
| Notification | ? | ? | ? | ? | **ANALIZAR** |
| User | ? | ? | ? | ? | **ANALIZAR** |
| ... | ... | ... | ... | ... | ... |

---

## 5. ANÁLISIS DE RUTAS

### 🔍 Análisis Completo de routes/web.php

```bash
echo "=== ANÁLISIS DE RUTAS WEB ===" > routes-analysis.txt

echo "Todas las rutas definidas:" >> routes-analysis.txt
php artisan route:list >> routes-analysis.txt

echo "" >> routes-analysis.txt
echo "Rutas por método:" >> routes-analysis.txt
php artisan route:list --method=GET >> routes-analysis.txt
php artisan route:list --method=POST >> routes-analysis.txt
php artisan route:list --method=PUT >> routes-analysis.txt
php artisan route:list --method=DELETE >> routes-analysis.txt

cat routes-analysis.txt
```

### 📝 Extraer Estructura de Rutas Actual

```bash
# Ver routes/web.php limpio
cat routes/web.php | grep -v "^//" | grep -v "^#" > routes-clean.txt

# Analizar
echo "Controllers usados en rutas:"
grep "Controller" routes/web.php | sed 's/.*\[//;s/\].*//' | sort -u

echo ""
echo "Nombres de rutas:"
grep "->name(" routes/web.php | sed "s/.*->name('//;s/').*//" | sed 's/.*->name("//;s/").*//'

echo ""
echo "Prefijos de ruta:"
grep "Route::prefix" routes/web.php

echo ""
echo "Grupos de rutas:"
grep "Route::group\|->group" routes/web.php
```

### ❓ Preguntas Críticas sobre Rutas

1. **¿Cuántas rutas hay en total?**
   ```bash
   php artisan route:list | wc -l
   ```

2. **¿Hay rutas sin nombre?**
   ```bash
   php artisan route:list | grep -v "│ [a-zA-Z]" | grep "│ GET\|│ POST"
   ```

3. **¿Qué rutas NO tienen el prefijo `p/focus-qr`?**
   ```bash
   php artisan route:list | grep -v "p/focus-qr"
   ```

4. **¿Hay rutas de autenticación?**
   ```bash
   php artisan route:list | grep -i "login\|register\|password"
   ```

5. **¿Hay rutas de dashboard/admin?**
   ```bash
   php artisan route:list | grep -i "dashboard\|admin"
   ```

---

## 6. ANÁLISIS DE VISTAS

### 🔍 Detectar Estructura Actual de Vistas

```bash
echo "=== ESTRUCTURA DE VISTAS ===" > views-structure.txt

# Ver estructura de carpetas
tree resources/views >> views-structure.txt

echo "" >> views-structure.txt
echo "=== LAYOUTS DETECTADOS ===" >> views-structure.txt
find resources/views -name "*.blade.php" | xargs grep -l "@yield" | head -10 >> views-structure.txt

echo "" >> views-structure.txt
echo "=== VISTAS QUE EXTIENDEN ===" >> views-structure.txt
find resources/views -name "*.blade.php" -exec grep -H "@extends" {} \; >> views-structure.txt

echo "" >> views-structure.txt
echo "=== PARTIALS (@include) ===" >> views-structure.txt
find resources/views -name "*.blade.php" -exec grep -H "@include" {} \; | cut -d: -f2 | sort -u >> views-structure.txt

echo "" >> views-structure.txt
echo "=== COMPONENTES BLADE (<x-) ===" >> views-structure.txt
find resources/views -name "*.blade.php" -exec grep -oH "<x-[a-zA-Z-]*" {} \; | cut -d: -f2 | sort -u >> views-structure.txt

cat views-structure.txt
```

### 📊 Mapa de Vistas

Crea un mapa de **todas las vistas** y sus dependencias:

```
Vista: resources/views/orders/index.blade.php
├── Extiende: layout.blade.php
├── Incluye: 
│   ├── header.blade.php
│   └── sidebar.blade.php
├── Componentes usados:
│   ├── <x-alert>
│   └── <x-table>
└── Llamada desde: OrderController@index
```

### ❓ Preguntas Críticas sobre Vistas

1. **¿Cuántos layouts diferentes hay?**
   ```bash
   find resources/views -name "*.blade.php" | xargs grep "@yield" | wc -l
   ```

2. **¿Hay vistas que NO extienden ningún layout?**
   ```bash
   find resources/views -name "*.blade.php" -exec sh -c 'grep -L "@extends" "$1" | grep -v "layout\|partial\|component"' _ {} \;
   ```

3. **¿Hay vistas huérfanas (no llamadas desde ningún controller)?**
   - Revisar manualmente

4. **¿Se usa Livewire?**
   ```bash
   find resources/views -name "*.blade.php" -exec grep -l "livewire\|wire:" {} \;
   ```

5. **¿Se usa Vue/React?**
   ```bash
   grep -r "vue\|react\|@vue\|@react" resources/views/
   ```

---

## 7. ANÁLISIS DE MIDDLEWARE Y AUTENTICACIÓN

### 🔍 Detectar Middleware Personalizado

```bash
echo "=== MIDDLEWARE EXISTENTE ===" > middleware-analysis.txt

# Listar middleware
ls -la app/Http/Middleware/ >> middleware-analysis.txt

# Middleware en Kernel
echo "" >> middleware-analysis.txt
echo "Middleware registrado en Kernel:" >> middleware-analysis.txt
grep "protected.*middleware" app/Http/Kernel.php -A 30 >> middleware-analysis.txt

# Middleware en rutas
echo "" >> middleware-analysis.txt
echo "Middleware aplicado en rutas:" >> middleware-analysis.txt
grep "middleware(" routes/*.php >> middleware-analysis.txt

cat middleware-analysis.txt
```

### ❓ Preguntas sobre Autenticación

1. **¿Usa Laravel Breeze/Jetstream/Fortify?**
   ```bash
   composer show | grep -E "breeze|jetstream|fortify"
   ```

2. **¿Hay sistema de roles/permisos?**
   ```bash
   composer show | grep -E "spatie/laravel-permission|permission"
   find app -name "*Role*.php" -o -name "*Permission*.php"
   ```

3. **¿Hay autenticación API (Sanctum/Passport)?**
   ```bash
   composer show | grep -E "sanctum|passport"
   ```

4. **¿Qué usuarios/roles existen?**
   - Revisar seeders
   - Revisar migraciones

---

## 8. ANÁLISIS DE SERVICIOS EXTERNOS

### 🔍 Detectar Integraciones

```bash
echo "=== SERVICIOS EXTERNOS ===" > external-services.txt

# Revisar .env
echo "Variables de entorno (API keys, etc.):" >> external-services.txt
grep -E "API_KEY|SECRET|TOKEN|FCM|FIREBASE|PUSHER|MAIL|SMS" .env.example >> external-services.txt

# Revisar composer.json
echo "" >> external-services.txt
echo "Paquetes de terceros instalados:" >> external-services.txt
composer show >> external-services.txt

cat external-services.txt
```

### ❓ Preguntas Críticas

1. **¿Usa Firebase Cloud Messaging (FCM)?**
   ```bash
   grep -r "FCM\|firebase\|notification" app/
   ```

2. **¿Usa alguna librería de QR?**
   ```bash
   composer show | grep -i qr
   ```

3. **¿Usa servicios de email/SMS?**
   ```bash
   grep -r "Mail::\|Notification::\|SMS" app/
   ```

4. **¿Usa almacenamiento externo (S3, Cloudinary)?**
   ```bash
   grep -r "Storage::\|s3\|cloudinary" app/
   ```

5. **¿Usa caché externo (Redis, Memcached)?**
   ```bash
   grep "CACHE_DRIVER\|REDIS" .env.example
   ```

---

## 9. ANÁLISIS DE BASE DE DATOS

### 🔍 Análisis de Migrations

```bash
echo "=== MIGRACIONES ===" > database-analysis.txt

# Listar migrations
ls -la database/migrations/ >> database-analysis.txt

# Tablas creadas
echo "" >> database-analysis.txt
echo "Tablas en migraciones:" >> database-analysis.txt
grep "Schema::create" database/migrations/*.php | sed "s/.*create('//;s/'.*//" >> database-analysis.txt

# Relaciones (foreign keys)
echo "" >> database-analysis.txt
echo "Foreign Keys:" >> database-analysis.txt
grep "foreign\|references\|constrained" database/migrations/*.php >> database-analysis.txt

cat database-analysis.txt
```

### 📊 Diagrama de Base de Datos

Genera una lista de **todas las tablas** y sus columnas:

```bash
# Si tienes acceso a la DB
php artisan tinker << 'EOF'
$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $tableName = reset($table);
    echo "\nTabla: $tableName\n";
    $columns = DB::select("SHOW COLUMNS FROM $tableName");
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
}
EOF
```

### ❓ Preguntas sobre Base de Datos

1. **¿Cuántas tablas hay?**
   ```bash
   ls database/migrations/*.php | wc -l
   ```

2. **¿Hay tablas pivot (many-to-many)?**
   ```bash
   grep "Schema::create.*_.*_" database/migrations/*.php
   ```

3. **¿Usa soft deletes?**
   ```bash
   grep "softDeletes" database/migrations/*.php
   ```

4. **¿Hay tablas sin modelo correspondiente?**
   - Comparar lista de tablas vs lista de modelos

---

## 10. ANÁLISIS DE DEPENDENCIAS

### 🔍 Paquetes Instalados

```bash
echo "=== DEPENDENCIAS COMPOSER ===" > dependencies.txt
composer show >> dependencies.txt

echo "" >> dependencies.txt
echo "=== DEPENDENCIAS NPM ===" >> dependencies.txt
npm list --depth=0 >> dependencies.txt

cat dependencies.txt
```

### ❓ Preguntas sobre Dependencias

1. **¿Qué paquetes Laravel específicos usa?**
   ```bash
   composer show | grep laravel/
   ```

2. **¿Usa paquetes deprecados o viejos?**
   - Revisar versiones

3. **¿Hay paquetes personalizados/privados?**
   ```bash
   grep "repositories" composer.json
   ```

---

## 11. ANÁLISIS DE API PARA FLUTTER

### 🔍 Detectar Rutas API

```bash
echo "=== API ROUTES ===" > api-analysis.txt

# Ver routes/api.php
cat routes/api.php >> api-analysis.txt

# Listar rutas API
php artisan route:list --path=api >> api-analysis.txt

cat api-analysis.txt
```

### ❓ Preguntas sobre API

1. **¿Qué endpoints existen para Flutter?**
   ```bash
   php artisan route:list --path=api
   ```

2. **¿Usa autenticación API (tokens)?**
   ```bash
   grep "sanctum\|passport\|token" routes/api.php
   ```

3. **¿Retorna JSON estandarizado?**
   - Revisar controllers API

4. **¿Hay versionado de API (v1, v2)?**
   ```bash
   grep "v1\|v2\|version" routes/api.php
   ```

5. **¿Qué funciones de la app móvil dependen de esta API?**
   - Revisar con equipo Flutter

---

## 12. ANÁLISIS DE JOBS Y COLAS

### 🔍 Detectar Jobs/Queue

```bash
echo "=== JOBS ===" > jobs-analysis.txt

# Listar jobs
ls -la app/Jobs/ 2>/dev/null >> jobs-analysis.txt || echo "No hay carpeta Jobs" >> jobs-analysis.txt

# Detectar uso de colas
grep -r "dispatch\|Queue::" app/ >> jobs-analysis.txt

cat jobs-analysis.txt
```

### ❓ Preguntas sobre Jobs

1. **¿Usa colas (queues)?**
   ```bash
   grep "QUEUE_CONNECTION" .env.example
   ```

2. **¿Qué se procesa en background?**
   - Envío de notificaciones
   - Generación de QR
   - Otros

3. **¿Usa Laravel Horizon (Redis)?**
   ```bash
   composer show | grep horizon
   ```

---

## 13. ANÁLISIS DE ASSETS Y FRONTEND

### 🔍 Análisis de JavaScript y CSS

```bash
echo "=== FRONTEND ASSETS ===" > frontend-analysis.txt

# Listar archivos JS
echo "JavaScript:" >> frontend-analysis.txt
find resources/js -name "*.js" 2>/dev/null >> frontend-analysis.txt

# Listar archivos CSS/SCSS
echo "" >> frontend-analysis.txt
echo "Estilos:" >> frontend-analysis.txt
find resources/css resources/scss resources/sass -name "*.css" -o -name "*.scss" -o -name "*.sass" 2>/dev/null >> frontend-analysis.txt

# Detectar framework CSS
echo "" >> frontend-analysis.txt
echo "Framework CSS detectado:" >> frontend-analysis.txt
grep -i "bootstrap\|tailwind\|bulma\|foundation" package.json >> frontend-analysis.txt

cat frontend-analysis.txt
```

### ❓ Preguntas sobre Frontend

1. **¿Usa Bootstrap o Tailwind?**
   ```bash
   grep "bootstrap\|tailwind" package.json
   ```

2. **¿Usa Vue/React/Alpine?**
   ```bash
   grep "vue\|react\|alpine" package.json
   ```

3. **¿Usa Vite o Laravel Mix?**
   ```bash
   ls -la vite.config.js webpack.mix.js 2>/dev/null
   ```

4. **¿Hay JavaScript personalizado para escaneo QR?**
   ```bash
   find resources/js -name "*qr*" -o -name "*scan*"
   ```

---

## 14. GENERACIÓN DE MAPA DE REESTRUCTURACIÓN

### 📋 Documento Final de Análisis

Con toda la información recopilada, genera este documento:

**Archivo:** `RESTRUCTURE-PLAN.md`

```markdown
# Plan de Reestructuración - Focus QR System

## Fecha: [FECHA]
## Analista: [TU NOMBRE]

---

## 1. INVENTARIO COMPLETO

### Controllers Encontrados
- [ ] OrderController → app/Http/Controllers/Orders/
- [ ] QrController → app/Http/Controllers/QrCodes/QrCodeController.php
- [ ] [LISTAR TODOS]

### Models Encontrados
- [ ] Order (Tabla: orders)
- [ ] QrCode (Tabla: qr_codes)
- [ ] [LISTAR TODOS]

### Vistas Encontradas
- [ ] resources/views/orders/*.blade.php → focus-qr/modules/orders/
- [ ] [LISTAR TODOS]

---

## 2. FUNCIONALIDADES IDENTIFICADAS

### Módulos del Sistema
1. **Órdenes**
   - Crear orden
   - Listar órdenes
   - Marcar como lista
   - Marcar como entregada
   - [AGREGAR MÁS]

2. **Códigos QR**
   - [LISTAR FUNCIONALIDADES]

3. **[OTROS MÓDULOS]**
   - [LISTAR FUNCIONALIDADES]

---

## 3. SERVICIOS EXTERNOS IDENTIFICADOS

- [ ] Firebase Cloud Messaging (Notificaciones push)
- [ ] Librería QR: [¿Cuál?]
- [ ] [OTROS]

---

## 4. DEPENDENCIAS CRÍTICAS

### Entre Controllers
- OrderController depende de: QrCodeService, NotificationService
- [MAPEAR TODAS]

### Entre Models
- Order tiene relación con: QrCode, Device, Notification
- [MAPEAR TODAS]

---

## 5. API PARA FLUTTER

### Endpoints Identificados
- POST /api/orders/associate
- GET /api/orders/{id}/status
- [LISTAR TODOS]

---

## 6. RUTAS A ACTUALIZAR

### Rutas Web
- [LISTAR TODAS CON CAMBIO NECESARIO]

### Rutas API
- [LISTAR TODAS CON CAMBIO NECESARIO]

---

## 7. ARCHIVOS A CREAR

### Services Necesarios
- [ ] OrderService.php
- [ ] QrCodeService.php
- [ ] NotificationService.php
- [ ] [OTROS]

### Repositories Necesarios
- [ ] OrderRepository.php
- [ ] DeviceRepository.php
- [ ] [OTROS]

---

## 8. ARCHIVOS A MOVER

### Controllers
| Archivo Actual | Destino | Namespace Nuevo |
|---------------|---------|-----------------|
| OrderController.php | Orders/OrderController.php | App\Http\Controllers\Orders |
| [TODOS] | ... | ... |

### Vistas
| Archivo Actual | Destino |
|---------------|---------|
| orders/index.blade.php | focus-qr/modules/orders/index.blade.php |
| [TODAS] | ... |

---

## 9. REFERENCIAS A ACTUALIZAR

### En Controllers
- [ ] Actualizar namespaces
- [ ] Actualizar view() calls
- [ ] Actualizar route() calls
- [ ] Actualizar redirect() calls

### En Vistas
- [ ] Actualizar @extends
- [ ] Actualizar @include
- [ ] Actualizar route()
- [ ] Actualizar asset()

### En Rutas
- [ ] Agregar prefijo p/focus-qr
- [ ] Actualizar use statements
- [ ] Agregar nombres de ruta

---

## 10. ORDEN DE EJECUCIÓN

1. Crear estructura de carpetas
2. Crear Services y Repositories
3. Mover Controllers y actualizar namespaces
4. Mover Vistas
5. Actualizar Rutas
6. Actualizar referencias en Controllers
7. Actualizar referencias en Vistas
8. Limpiar cachés
9. Probar funcionalidad
10. Commit

---

## 11. RIESGOS IDENTIFICADOS

- [ ] [RIESGO 1]
- [ ] [RIESGO 2]

---

## 12. TIEMPO ESTIMADO

- Análisis: [X] horas
- Reestructuración: [X] horas
- Pruebas: [X] horas
- **Total: [X] horas**

```

---

## 🎯 PRÓXIMOS PASOS

Una vez completado todo este análisis:

1. ✅ Tienes un inventario **completo y exacto** del proyecto
2. ✅ Conoces **todas las dependencias** entre archivos
3. ✅ Identificaste **toda la funcionalidad** implementada
4. ✅ Detectaste **servicios externos** y configuraciones
5. ✅ Generaste un **plan de reestructuración** detallado

**AHORA SÍ** puedes proceder con seguridad a:
- Ejecutar la guía de reestructuración de carpetas
- Mover archivos sin romper funcionalidad
- Actualizar referencias de manera sistemática

---

## 🚨 ADVERTENCIA FINAL

**NO OMITAS ESTE ANÁLISIS.** 

Mover archivos sin entender completamente la estructura actual es como operar a ciegas. Puede resultar en:

- ❌ Pérdida de funcionalidad
- ❌ Errores difíciles de rastrear
- ❌ Horas de debugging
- ❌ Frustración y retrabajo

**INVIERTE EL TIEMPO EN ANÁLISIS.** Te ahorrará 10x ese tiempo en correcciones.

---

## 📞 CHECKLIST FINAL DE ANÁLISIS

Antes de empezar la reestructuración, confirma que completaste:

- [ ] Generé inventario completo de Controllers
- [ ] Generé inventario completo de Models
- [ ] Generé inventario completo de Vistas
- [ ] Analicé TODAS las rutas (web.php y api.php)
- [ ] Identifiqué middleware personalizado
- [ ] Identifiqué servicios externos (FCM, QR, etc.)
- [ ] Analicé estructura de base de datos
- [ ] Revisé dependencias en composer.json
- [ ] Analicé API para Flutter
- [ ] Detecté Jobs y colas
- [ ] Analicé assets de frontend
- [ ] Generé documento RESTRUCTURE-PLAN.md completo
- [ ] Revisé con el equipo el plan generado

**Solo cuando todos los checkboxes estén ✅, procede con la reestructuración.**

---

## 🤖 INSTRUCCIONES PARA CLAUDE CODE

Si estás usando Claude Code para este análisis:

1. **Ejecuta TODOS los scripts de este documento**
2. **Genera TODOS los archivos de análisis**
3. **Lee TODOS los controllers, modelos y vistas**
4. **Crea el documento RESTRUCTURE-PLAN.md COMPLETO**
5. **Pregunta por CUALQUIER duda antes de mover archivos**

Claude Code: **NO asumas nada. ANALIZA todo. PREGUNTA todo lo necesario.**

---

**¡Éxito en el análisis completo! 🔍📊**
