# Cómo se implementaron los Endpoints de Ubicación de Negocios

## 📌 Resumen

Se implementaron endpoints de geolocalización para que la app móvil pueda buscar negocios cercanos usando coordenadas GPS.

**Endpoints implementados:**
- `GET /api/v1/businesses` - Listar todos los negocios
- `GET /api/v1/businesses/nearby` - Buscar negocios cercanos

---

## 🗂️ Archivos creados/modificados

### 1. **Migración de Base de Datos** ✅

**Archivo:** `database/migrations/2025_11_26_144109_add_location_fields_to_businesses_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // Campos de geolocalización
            $table->decimal('latitude', 10, 8)->nullable()->after('postal_code');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->text('location_description')->nullable()->after('longitude');
            $table->boolean('is_location_public')->default(false)->after('location_description');

            // Índice para optimizar búsquedas geográficas
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn([
                'latitude',
                'longitude',
                'location_description',
                'is_location_public'
            ]);
        });
    }
};
```

**Ejecutar:**
```bash
php artisan migrate
```

---

### 2. **Modelo Business** ✅

**Archivo:** `app/Models/Business.php`

**Agregar a `$fillable`:**
```php
protected $fillable = [
    // ... campos existentes ...
    'latitude',
    'longitude',
    'location_description',
    'is_location_public',
];
```

**Agregar a `$casts`:**
```php
protected $casts = [
    // ... casts existentes ...
    'is_location_public' => 'boolean',
    'latitude' => 'decimal:8',
    'longitude' => 'decimal:8',
];
```

**Agregar Scopes (métodos de búsqueda):**
```php
/**
 * Scope to get businesses with public location
 */
public function scopeWithPublicLocation($query)
{
    return $query->where('is_location_public', true)
                 ->whereNotNull('latitude')
                 ->whereNotNull('longitude');
}

/**
 * Scope to find businesses near a specific location
 * Uses Haversine formula to calculate distance
 *
 * @param \Illuminate\Database\Eloquent\Builder $query
 * @param float $latitude User latitude
 * @param float $longitude User longitude
 * @param int $radius Search radius in kilometers (default: 10)
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeNearby($query, $latitude, $longitude, $radius = 10)
{
    return $query->select('*')
        ->selectRaw(
            '( 6371 * acos( cos( radians(?) ) *
            cos( radians( latitude ) ) *
            cos( radians( longitude ) - radians(?) ) +
            sin( radians(?) ) *
            sin( radians( latitude ) ) ) ) AS distance_km',
            [$latitude, $longitude, $latitude]
        )
        ->withPublicLocation()
        ->having('distance_km', '<=', $radius)
        ->orderBy('distance_km', 'asc');
}

/**
 * Calculate distance to a specific point
 *
 * @param float $latitude
 * @param float $longitude
 * @return float Distance in kilometers
 */
public function distanceTo($latitude, $longitude)
{
    if (!$this->latitude || !$this->longitude) {
        return null;
    }

    $earthRadius = 6371; // km

    $latFrom = deg2rad($this->latitude);
    $lonFrom = deg2rad($this->longitude);
    $latTo = deg2rad($latitude);
    $lonTo = deg2rad($longitude);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos($latFrom) * cos($latTo) *
         sin($lonDelta / 2) * sin($lonDelta / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return round($earthRadius * $c, 2);
}
```

---

### 3. **Controlador BusinessLocationController** ✅

**Archivo:** `app/Http/Controllers/Api/V1/BusinessLocationController.php`

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BusinessLocationController extends Controller
{
    /**
     * Obtener todos los negocios con paginación
     *
     * @endpoint GET /api/v1/businesses
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);

            if ($perPage > 500) {
                $perPage = 500;
            }

            $query = Business::active();

            // Filtros opcionales
            if ($request->filled('city')) {
                $query->where('city', $request->input('city'));
            }

            if ($request->filled('state')) {
                $query->where('state', $request->input('state'));
            }

            if ($request->filled('with_location') && $request->input('with_location') == '1') {
                $query->withPublicLocation();
            }

            $businesses = $query->orderBy('created_at', 'desc')->paginate($perPage);

            $formattedBusinesses = $businesses->map(function ($business) {
                return [
                    'business_id' => $business->business_id,
                    'business_name' => $business->business_name,
                    'phone' => $business->phone,
                    'email' => $business->email,
                    'address' => $business->address,
                    'address_details' => $business->address_details ?? null,
                    'city' => $business->city,
                    'state' => $business->state,
                    'postal_code' => $business->postal_code,
                    'latitude' => $business->latitude ? (float) $business->latitude : null,
                    'longitude' => $business->longitude ? (float) $business->longitude : null,
                    'is_open' => true,
                    'rating' => null,
                    'total_reviews' => null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Negocios obtenidos exitosamente',
                'data' => [
                    'businesses' => $formattedBusinesses,
                    'pagination' => [
                        'current_page' => $businesses->currentPage(),
                        'per_page' => $businesses->perPage(),
                        'total' => $businesses->total(),
                        'total_pages' => $businesses->lastPage(),
                        'from' => $businesses->firstItem(),
                        'to' => $businesses->lastItem(),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los negocios',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener negocios cercanos a la ubicación del usuario
     *
     * @endpoint GET /api/v1/businesses/nearby
     */
    public function nearby(Request $request): JsonResponse
    {
        // IMPORTANTE: Cambiar 'integer' a 'numeric' para aceptar decimales desde Flutter
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'radius' => 'nullable|numeric|min:0.1|max:100',  // 👈 numeric, no integer
            'limit' => 'nullable|integer|min:1|max:50',
            'page' => 'nullable|integer|min:1',
        ], [
            'latitude.required' => 'La latitud es requerida',
            'latitude.numeric' => 'La latitud debe ser un número',
            'longitude.required' => 'La longitud es requerida',
            'longitude.numeric' => 'La longitud debe ser un número',
            'radius.numeric' => 'El radio debe ser un número',
            'radius.max' => 'El radio máximo permitido es 100 km',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de ubicación inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $userLat = $request->input('latitude');
            $userLng = $request->input('longitude');
            $radius = $request->input('radius', 10); // default 10km
            $limit = $request->input('limit', 20);
            $page = $request->input('page', 1);

            // Obtener negocios cercanos usando el scope
            $query = Business::nearby($userLat, $userLng, $radius)->active();

            // Aplicar paginación
            $businesses = $query->paginate($limit, ['*'], 'page', $page);

            if ($businesses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron negocios cercanos',
                    'data' => [
                        'businesses' => [],
                        'search_radius_km' => $radius,
                        'user_location' => [
                            'latitude' => (float) $userLat,
                            'longitude' => (float) $userLng,
                        ]
                    ]
                ], 404);
            }

            $formattedBusinesses = $businesses->map(function ($business) {
                return [
                    'business_id' => $business->business_id,
                    'business_name' => $business->business_name,
                    'phone' => $business->phone,
                    'address' => $business->address,
                    'address_details' => $business->address_details,
                    'city' => $business->city,
                    'state' => $business->state,
                    'postal_code' => $business->postal_code,
                    'latitude' => (float) $business->latitude,
                    'longitude' => (float) $business->longitude,
                    'distance_km' => (float) $business->distance_km,
                    'is_open' => true,
                    'rating' => null,
                    'total_reviews' => null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'businesses' => $formattedBusinesses,
                    'pagination' => [
                        'current_page' => $businesses->currentPage(),
                        'per_page' => $businesses->perPage(),
                        'total' => $businesses->total(),
                        'last_page' => $businesses->lastPage(),
                        'from' => $businesses->firstItem(),
                        'to' => $businesses->lastItem(),
                    ],
                    'user_location' => [
                        'latitude' => (float) $userLat,
                        'longitude' => (float) $userLng,
                    ],
                    'search_radius_km' => $radius,
                ],
                'message' => 'Negocios cercanos obtenidos exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener negocios cercanos',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener detalle de un negocio con distancia opcional
     *
     * @endpoint GET /api/v1/businesses/{business_id}
     */
    public function show(Request $request, int $businessId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_latitude' => 'nullable|numeric|min:-90|max:90',
            'user_longitude' => 'nullable|numeric|min:-180|max:180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de ubicación inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $business = Business::where('business_id', $businessId)->active()->first();

            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Negocio no encontrado'
                ], 404);
            }

            $distanceKm = null;
            if ($request->has(['user_latitude', 'user_longitude'])) {
                $distanceKm = $business->distanceTo(
                    $request->input('user_latitude'),
                    $request->input('user_longitude')
                );
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'business_id' => $business->business_id,
                    'business_name' => $business->business_name,
                    'phone' => $business->phone,
                    'address' => $business->address,
                    'city' => $business->city,
                    'state' => $business->state,
                    'postal_code' => $business->postal_code,
                    'latitude' => (float) $business->latitude,
                    'longitude' => (float) $business->longitude,
                    'distance_km' => $distanceKm,
                    'is_open' => true,
                    'rating' => null,
                    'total_reviews' => null,
                ],
                'message' => 'Detalle del negocio obtenido exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el negocio',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Buscar negocios por ciudad, estado o código postal
     *
     * @endpoint GET /api/v1/businesses/search
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de búsqueda inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $query = Business::active();

            $hasSearchFilters = $request->filled(['query', 'city', 'state', 'postal_code']);

            if ($hasSearchFilters) {
                $query->withPublicLocation();
            }

            if ($request->filled('query')) {
                $searchTerm = $request->input('query');
                $query->where('business_name', 'LIKE', "%{$searchTerm}%");
            }

            if ($request->filled('city')) {
                $query->where('city', $request->input('city'));
            }

            if ($request->filled('state')) {
                $query->where('state', $request->input('state'));
            }

            if ($request->filled('postal_code')) {
                $query->where('postal_code', $request->input('postal_code'));
            }

            if ($request->has(['latitude', 'longitude'])) {
                $userLat = $request->input('latitude');
                $userLng = $request->input('longitude');

                $query->selectRaw(
                    '*, ( 6371 * acos( cos( radians(?) ) *
                    cos( radians( latitude ) ) *
                    cos( radians( longitude ) - radians(?) ) +
                    sin( radians(?) ) *
                    sin( radians( latitude ) ) ) ) AS distance_km',
                    [$userLat, $userLng, $userLat]
                )->orderBy('distance_km', 'asc');
            }

            $perPage = $request->input('per_page', 20);
            $businesses = $query->paginate($perPage);

            $formattedBusinesses = $businesses->map(function ($business) {
                return [
                    'business_id' => $business->business_id,
                    'business_name' => $business->business_name,
                    'phone' => $business->phone,
                    'address' => $business->address,
                    'city' => $business->city,
                    'state' => $business->state,
                    'postal_code' => $business->postal_code,
                    'latitude' => $business->latitude ? (float) $business->latitude : null,
                    'longitude' => $business->longitude ? (float) $business->longitude : null,
                    'distance_km' => isset($business->distance_km) ? (float) $business->distance_km : null,
                    'is_open' => true,
                    'rating' => null,
                    'total_reviews' => null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Búsqueda completada exitosamente',
                'data' => [
                    'businesses' => $formattedBusinesses,
                    'pagination' => [
                        'current_page' => $businesses->currentPage(),
                        'per_page' => $businesses->perPage(),
                        'total' => $businesses->total(),
                        'total_pages' => $businesses->lastPage(),
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar la búsqueda',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
```

---

### 4. **Rutas API** ✅

**Archivo:** `routes/api.php`

```php
use App\Http\Controllers\Api\V1\BusinessLocationController;

Route::prefix('v1')->group(function () {

    // Business Location API (Public - para app móvil)
    Route::prefix('businesses')->middleware('throttle:60,1')->group(function () {
        // Obtener todos los negocios con paginación
        Route::get('/', [BusinessLocationController::class, 'index']);

        // Buscar negocios cercanos basado en geolocalización
        Route::get('/nearby', [BusinessLocationController::class, 'nearby']);

        // Buscar negocios por ciudad, estado o código postal
        Route::get('/search', [BusinessLocationController::class, 'search']);

        // Obtener detalles de un negocio específico
        Route::get('/{businessId}', [BusinessLocationController::class, 'show']);
    });

});
```

---

## 🚨 PROBLEMA CRÍTICO RESUELTO

### Error inicial:
```
400 Bad Request - "Parámetros de ubicación inválidos"
```

### Causa:
Flutter enviaba el parámetro `radius` como **decimal** (`10.0`), pero Laravel esperaba un **entero** (`10`).

**Validación incorrecta:**
```php
'radius' => 'integer|min:1|max:100',  // ❌ Rechaza 10.0
```

### Solución:
Cambiar la validación a `numeric` para aceptar tanto enteros como decimales:

```php
'radius' => 'nullable|numeric|min:0.1|max:100',  // ✅ Acepta 10 y 10.0
```

**Línea modificada:** `app/Http/Controllers/Api/V1/BusinessLocationController.php:143`

---

## 📊 Datos de prueba (opcional)

Si necesitas agregar ubicaciones de prueba a negocios existentes:

```sql
-- Ciudad de México (Centro)
UPDATE businesses SET
    latitude = 19.432608,
    longitude = -99.133209,
    is_location_public = 1
WHERE business_id = 1;

-- Guadalajara
UPDATE businesses SET
    latitude = 20.659698,
    longitude = -103.349609,
    is_location_public = 1
WHERE business_id = 2;

-- Monterrey
UPDATE businesses SET
    latitude = 25.686613,
    longitude = -100.316116,
    is_location_public = 1
WHERE business_id = 3;
```

---

## 🧪 Pruebas con curl

### Listar todos los negocios:
```bash
curl -X GET "http://localhost:8000/api/v1/businesses" \
  -H "Accept: application/json"
```

### Buscar negocios cercanos:
```bash
curl -X GET "http://localhost:8000/api/v1/businesses/nearby?latitude=19.432847&longitude=-99.133208&radius=10" \
  -H "Accept: application/json"
```

### Buscar con radio decimal (lo que envía Flutter):
```bash
curl -X GET "http://localhost:8000/api/v1/businesses/nearby?latitude=19.432847&longitude=-99.133208&radius=10.0" \
  -H "Accept: application/json"
```

---

## 📱 Desde Flutter

Flutter ya está configurado correctamente. No requiere cambios:

```dart
static Future<List<Business>> getNearbyBusinesses({
  required double latitude,
  required double longitude,
  double radius = 10.0,  // ✅ Ahora funciona con decimales
}) async {
  final response = await _dio.get(
    ApiConfig.getNearbyBusinesses,
    queryParameters: {
      'latitude': latitude,
      'longitude': longitude,
      'radius': radius,
    },
  );
  // ...
}
```

---

## ✅ Checklist de implementación

- [x] Crear migración para agregar campos de ubicación
- [x] Ejecutar migración (`php artisan migrate`)
- [x] Actualizar modelo `Business` (fillable, casts, scopes)
- [x] Crear controlador `BusinessLocationController`
- [x] Registrar rutas en `routes/api.php`
- [x] **FIX CRÍTICO:** Cambiar validación de `integer` a `numeric` para `radius`
- [x] Agregar throttle para limitar peticiones (60 req/min)
- [x] Probar endpoints con datos reales
- [x] Verificar compatibilidad con Flutter

---

## 📝 Notas importantes

1. **Fórmula Haversine**: Se usa para calcular distancias en superficie esférica (Tierra)
2. **Radio de la Tierra**: 6371 km
3. **Precisión de coordenadas**:
   - Latitud: `DECIMAL(10, 8)` = 8 decimales (~1.1 mm de precisión)
   - Longitud: `DECIMAL(11, 8)` = 8 decimales (~1.1 mm de precisión)
4. **Índice de base de datos**: Se agregó índice compuesto `(latitude, longitude)` para mejorar rendimiento
5. **Privacidad**: Solo se muestran negocios con `is_location_public = true`
6. **Rate limiting**: 60 peticiones por minuto por IP

---

## 🔄 Para migrar a nueva versión del proyecto

1. Copiar la migración a `database/migrations/`
2. Copiar los cambios del modelo `Business.php`
3. Copiar el controlador completo `BusinessLocationController.php`
4. Agregar las rutas a `routes/api.php`
5. Ejecutar `php artisan migrate`
6. Actualizar datos de prueba si es necesario

---

**Autor:** CETAM Dev Team
**Fecha:** 2025-11-28
**Versión:** 1.0.0
