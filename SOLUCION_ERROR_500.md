# 🔧 Solución al Error 500 - Backend Laravel

> El error 500 indica que el backend Laravel tiene un problema interno al procesar la petición `/businesses/nearby`

---

## 🔍 Diagnóstico del Problema

### Paso 1: Revisar los Logs de Laravel

En tu proyecto Laravel, revisa el archivo de logs:

```bash
# En el proyecto Laravel
tail -f storage/logs/laravel.log
```

Esto te mostrará el error exacto que está ocurriendo en el servidor.

---

## ✅ Soluciones Comunes

### Solución 1: Verificar que existan las columnas en la tabla `businesses`

**Problema:** La tabla `businesses` no tiene las columnas de ubicación.

**Verificar en MySQL/PostgreSQL:**

```sql
DESCRIBE businesses;
-- o
SHOW COLUMNS FROM businesses;
```

**Si faltan las columnas, crear la migración:**

```bash
php artisan make:migration add_location_to_businesses_table
```

**Contenido de la migración:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('phone');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->text('address_details')->nullable()->after('address');
            $table->string('city', 100)->nullable()->after('address_details');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            $table->boolean('is_location_public')->default(true)->after('postal_code');

            // Índices
            $table->index(['latitude', 'longitude'], 'idx_location');
        });
    }

    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex('idx_location');
            $table->dropColumn([
                'latitude',
                'longitude',
                'address_details',
                'city',
                'state',
                'postal_code',
                'is_location_public',
            ]);
        });
    }
};
```

**Ejecutar la migración:**

```bash
php artisan migrate
```

---

### Solución 2: Implementar el endpoint `/businesses/nearby`

**Si el endpoint no existe o tiene errores, aquí está la implementación completa:**

#### 1. Crear el Controlador (si no existe)

```bash
php artisan make:controller Api/BusinessLocationController
```

#### 2. Implementar el método `nearby()`

**Archivo:** `app/Http/Controllers/Api/BusinessLocationController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class BusinessLocationController extends Controller
{
    /**
     * Obtener negocios cercanos a una ubicación
     */
    public function nearby(Request $request)
    {
        try {
            // Validar parámetros
            $validated = $request->validate([
                'latitude' => 'required|numeric|min:-90|max:90',
                'longitude' => 'required|numeric|min:-180|max:180',
                'radius' => 'nullable|integer|min:1|max:100',
                'limit' => 'nullable|integer|min:1|max:50',
            ]);

            $userLat = $validated['latitude'];
            $userLng = $validated['longitude'];
            $radius = $validated['radius'] ?? 10; // Default 10km
            $limit = $validated['limit'] ?? 20;   // Default 20 resultados

            // Fórmula Haversine para calcular distancia
            $businesses = Business::select('*')
                ->selectRaw(
                    '( 6371 * acos( cos( radians(?) ) *
                    cos( radians( latitude ) ) *
                    cos( radians( longitude ) - radians(?) ) +
                    sin( radians(?) ) *
                    sin( radians( latitude ) ) ) ) AS distance_km',
                    [$userLat, $userLng, $userLat]
                )
                ->where('is_location_public', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->having('distance_km', '<=', $radius)
                ->orderBy('distance_km', 'asc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'businesses' => $businesses,
                    'user_location' => [
                        'latitude' => $userLat,
                        'longitude' => $userLng,
                    ],
                    'search_radius_km' => $radius,
                ],
                'message' => 'Negocios cercanos obtenidos exitosamente',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de ubicación inválidos',
                'errors' => $e->errors(),
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Error en nearby businesses: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener negocios cercanos',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor',
            ], 500);
        }
    }
}
```

#### 3. Registrar la Ruta

**Archivo:** `routes/api.php`

```php
use App\Http\Controllers\Api\BusinessLocationController;

// Dentro de Route::prefix('v1')->group(function () {
Route::get('/businesses/nearby', [BusinessLocationController::class, 'nearby']);
```

---

### Solución 3: Agregar Datos de Prueba

**El endpoint necesita al menos un negocio con ubicación para funcionar.**

**Insertar datos de prueba:**

```sql
-- Actualizar un negocio existente con ubicación
UPDATE businesses
SET
    latitude = 19.432847,
    longitude = -99.133208,
    address_details = 'Av. Insurgentes Sur 123, Col. Roma',
    city = 'Ciudad de México',
    state = 'CDMX',
    postal_code = '06700',
    is_location_public = 1
WHERE business_id = 1;

-- O insertar un nuevo negocio
INSERT INTO businesses (
    business_name,
    phone,
    address,
    latitude,
    longitude,
    city,
    state,
    postal_code,
    is_location_public,
    created_at,
    updated_at
) VALUES (
    'Café de Prueba',
    '+52 55 1234 5678',
    'Av. Insurgentes Sur 123',
    19.432847,
    -99.133208,
    'Ciudad de México',
    'CDMX',
    '06700',
    1,
    NOW(),
    NOW()
);
```

---

### Solución 4: Verificar el Modelo Business

**Asegúrate de que el modelo incluya los campos de ubicación:**

**Archivo:** `app/Models/Business.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = 'businesses';
    protected $primaryKey = 'business_id';

    protected $fillable = [
        'business_name',
        'phone',
        'address',
        'address_details',
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'is_location_public',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_location_public' => 'boolean',
    ];

    // Atributos que se deben ocultar en JSON (si aplica)
    protected $hidden = [];

    // Relaciones (si las tienes)
    // public function orders() { ... }
}
```

---

## 🧪 Probar el Endpoint

Una vez implementado, prueba desde la terminal:

```bash
curl -X GET "http://localhost:8000/api/v1/businesses/nearby?latitude=19.432847&longitude=-99.133208&radius=10&limit=20" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

O desde Postman:
```
GET http://localhost:8000/api/v1/businesses/nearby
Query Params:
  - latitude: 19.432847
  - longitude: -99.133208
  - radius: 10
  - limit: 20
```

---

## 📊 Checklist de Verificación

Revisa cada punto:

- [ ] ✅ Tabla `businesses` tiene columnas `latitude` y `longitude`
- [ ] ✅ Migración ejecutada exitosamente (`php artisan migrate`)
- [ ] ✅ Controlador `BusinessLocationController` creado
- [ ] ✅ Método `nearby()` implementado con fórmula Haversine
- [ ] ✅ Ruta registrada en `routes/api.php`
- [ ] ✅ Modelo `Business` incluye campos de ubicación en `$fillable`
- [ ] ✅ Al menos 1 negocio en BD con `latitude` y `longitude` no nulos
- [ ] ✅ Columna `is_location_public` existe y está en `true`
- [ ] ✅ Logs de Laravel revisados (`storage/logs/laravel.log`)
- [ ] ✅ Endpoint probado con curl/Postman y retorna 200

---

## 🔍 Revisar Logs Específicos

En el archivo `storage/logs/laravel.log` busca líneas que contengan:

```
[ERROR] SQLSTATE
[ERROR] Call to undefined method
[ERROR] Column not found
```

Estos errores te dirán exactamente qué está fallando.

---

## 💡 Respuesta Esperada (Exitosa)

Cuando funcione correctamente, deberías recibir:

```json
{
  "success": true,
  "data": {
    "businesses": [
      {
        "business_id": 1,
        "business_name": "Café de Prueba",
        "phone": "+52 55 1234 5678",
        "address": "Av. Insurgentes Sur 123",
        "latitude": 19.432847,
        "longitude": -99.133208,
        "distance_km": 0.0,
        "city": "Ciudad de México",
        "state": "CDMX"
      }
    ],
    "user_location": {
      "latitude": 19.432847,
      "longitude": -99.133208
    },
    "search_radius_km": 10
  },
  "message": "Negocios cercanos obtenidos exitosamente"
}
```

---

## 🚨 Si el Error Persiste

1. **Revisa los logs completos de Laravel:** `storage/logs/laravel.log`
2. **Habilita el modo debug en Laravel:** En `.env` pon `APP_DEBUG=true`
3. **Verifica la conexión a la base de datos:** `php artisan migrate:status`
4. **Prueba una consulta simple primero:**
   ```php
   // En tinker: php artisan tinker
   Business::whereNotNull('latitude')->count();
   ```

---

**Fecha:** 2024-11-26
**App:** Order QR Mobile
**Backend:** Laravel

---

Una vez que el backend responda correctamente (status 200), **la app Flutter funcionará automáticamente** porque ya tiene todo el código implementado.
