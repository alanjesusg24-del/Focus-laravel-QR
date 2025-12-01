# Requisitos de Laravel para Funcionalidad de Ubicaciones de Negocios

> Documentación para implementar endpoints de geolocalización en el backend Laravel para la app móvil Order QR

---

## 📋 Tabla de Contenidos

- [Resumen Ejecutivo](#-resumen-ejecutivo)
- [Modelo de Datos](#-modelo-de-datos)
- [Endpoints Requeridos](#-endpoints-requeridos)
- [Formato de Respuestas](#-formato-de-respuestas)
- [Validaciones](#-validaciones)
- [Consideraciones de Seguridad](#-consideraciones-de-seguridad)
- [Ejemplos de Uso](#-ejemplos-de-uso)

---

## 🎯 Resumen Ejecutivo

La aplicación móvil Order QR necesita mostrar a los usuarios una lista de negocios cercanos basándose en su ubicación geográfica. Para esto, el backend Laravel debe:

1. Almacenar coordenadas geográficas (latitud/longitud) de cada negocio
2. Proporcionar un endpoint para buscar negocios cercanos
3. Calcular distancias entre la ubicación del usuario y los negocios
4. Retornar datos completos del negocio incluyendo su ubicación

---

## 🗄️ Modelo de Datos

### Tabla `businesses` (modificaciones necesarias)

La tabla de negocios debe incluir los siguientes campos adicionales:

```sql
-- Migración para agregar campos de ubicación
ALTER TABLE businesses ADD COLUMN latitude DECIMAL(10, 8) NULL;
ALTER TABLE businesses ADD COLUMN longitude DECIMAL(11, 8) NULL;
ALTER TABLE businesses ADD COLUMN address_details TEXT NULL;
ALTER TABLE businesses ADD COLUMN city VARCHAR(100) NULL;
ALTER TABLE businesses ADD COLUMN state VARCHAR(100) NULL;
ALTER TABLE businesses ADD COLUMN postal_code VARCHAR(20) NULL;
ALTER TABLE businesses ADD COLUMN is_location_public BOOLEAN DEFAULT true;
```

#### Descripción de campos:

| Campo | Tipo | Descripción | Ejemplo |
|-------|------|-------------|---------|
| `latitude` | DECIMAL(10,8) | Latitud del negocio | 19.43260000 |
| `longitude` | DECIMAL(11,8) | Longitud del negocio | -99.13320000 |
| `address_details` | TEXT | Dirección completa del negocio | "Av. Insurgentes Sur 123, Col. Roma" |
| `city` | VARCHAR(100) | Ciudad | "Ciudad de México" |
| `state` | VARCHAR(100) | Estado/Provincia | "CDMX" |
| `postal_code` | VARCHAR(20) | Código postal | "06700" |
| `is_location_public` | BOOLEAN | Si la ubicación es visible para usuarios | true/false |

### Índices recomendados:

```sql
-- Índice espacial para mejorar búsquedas geográficas
CREATE INDEX idx_businesses_location ON businesses(latitude, longitude);
CREATE INDEX idx_businesses_city_state ON businesses(city, state);
```

---

## 🔌 Endpoints Requeridos

### 1. Obtener Negocios Cercanos

**Endpoint:** `GET /api/v1/businesses/nearby`

**Descripción:** Retorna una lista de negocios cercanos a la ubicación del usuario, ordenados por distancia.

**Parámetros Query:**

| Parámetro | Tipo | Requerido | Descripción | Ejemplo |
|-----------|------|-----------|-------------|---------|
| `latitude` | float | ✅ Sí | Latitud del usuario | 19.432847 |
| `longitude` | float | ✅ Sí | Longitud del usuario | -99.133208 |
| `radius` | integer | ❌ No | Radio de búsqueda en km (default: 10) | 5 |
| `limit` | integer | ❌ No | Cantidad máxima de resultados (default: 20) | 10 |
| `page` | integer | ❌ No | Número de página para paginación | 1 |

**Headers:**
```
Authorization: Bearer {token}  (OPCIONAL - si hay usuario autenticado)
X-Device-ID: {device_id}       (OPCIONAL)
Content-Type: application/json
Accept: application/json
```

**Respuesta Exitosa (200):**

```json
{
  "success": true,
  "data": {
    "businesses": [
      {
        "business_id": 1,
        "business_name": "Café La Esquina",
        "phone": "+52 55 1234 5678",
        "address": "Av. Insurgentes Sur 123, Col. Roma",
        "address_details": "Entre Álvaro Obregón y Durango",
        "city": "Ciudad de México",
        "state": "CDMX",
        "postal_code": "06700",
        "latitude": 19.432847,
        "longitude": -99.133208,
        "distance_km": 0.5,
        "is_open": true,
        "rating": 4.5,
        "total_reviews": 150
      },
      {
        "business_id": 2,
        "business_name": "Pizzería Don Giuseppe",
        "phone": "+52 55 8765 4321",
        "address": "Calle Orizaba 45, Col. Roma Norte",
        "address_details": "Esquina con Jalapa",
        "city": "Ciudad de México",
        "state": "CDMX",
        "postal_code": "06700",
        "latitude": 19.418456,
        "longitude": -99.157234,
        "distance_km": 1.2,
        "is_open": true,
        "rating": 4.8,
        "total_reviews": 320
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 45,
      "last_page": 3,
      "from": 1,
      "to": 20
    },
    "user_location": {
      "latitude": 19.432847,
      "longitude": -99.133208
    },
    "search_radius_km": 10
  },
  "message": "Negocios cercanos obtenidos exitosamente"
}
```

**Errores Posibles:**

```json
// 400 Bad Request - Parámetros inválidos
{
  "success": false,
  "message": "Parámetros de ubicación inválidos",
  "errors": {
    "latitude": ["La latitud debe estar entre -90 y 90"],
    "longitude": ["La longitud debe estar entre -180 y 180"]
  }
}

// 404 Not Found - Sin resultados
{
  "success": false,
  "message": "No se encontraron negocios cercanos",
  "data": {
    "businesses": [],
    "search_radius_km": 10
  }
}
```

---

### 2. Obtener Detalle de Negocio con Ubicación

**Endpoint:** `GET /api/v1/businesses/{business_id}`

**Descripción:** Retorna información detallada de un negocio específico, incluyendo su ubicación.

**Parámetros Path:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `business_id` | integer | ID del negocio |

**Parámetros Query (opcionales):**

| Parámetro | Tipo | Descripción | Ejemplo |
|-----------|------|-------------|---------|
| `user_latitude` | float | Latitud del usuario para calcular distancia | 19.432847 |
| `user_longitude` | float | Longitud del usuario | -99.133208 |

**Respuesta Exitosa (200):**

```json
{
  "success": true,
  "data": {
    "business_id": 1,
    "business_name": "Café La Esquina",
    "phone": "+52 55 1234 5678",
    "address": "Av. Insurgentes Sur 123, Col. Roma",
    "address_details": "Entre Álvaro Obregón y Durango",
    "city": "Ciudad de México",
    "state": "CDMX",
    "postal_code": "06700",
    "latitude": 19.432847,
    "longitude": -99.133208,
    "distance_km": 0.5,
    "is_open": true,
    "opening_hours": {
      "monday": "08:00-20:00",
      "tuesday": "08:00-20:00",
      "wednesday": "08:00-20:00",
      "thursday": "08:00-20:00",
      "friday": "08:00-22:00",
      "saturday": "09:00-22:00",
      "sunday": "09:00-18:00"
    },
    "rating": 4.5,
    "total_reviews": 150,
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-11-25T15:45:00.000000Z"
  },
  "message": "Detalle del negocio obtenido exitosamente"
}
```

---

### 3. Buscar Negocios por Ciudad/Estado

**Endpoint:** `GET /api/v1/businesses/search`

**Descripción:** Buscar negocios por nombre, ciudad, estado o código postal.

**Parámetros Query:**

| Parámetro | Tipo | Requerido | Descripción | Ejemplo |
|-----------|------|-----------|-------------|---------|
| `query` | string | ❌ No | Búsqueda por nombre del negocio | "Café" |
| `city` | string | ❌ No | Filtrar por ciudad | "Ciudad de México" |
| `state` | string | ❌ No | Filtrar por estado | "CDMX" |
| `postal_code` | string | ❌ No | Filtrar por código postal | "06700" |
| `latitude` | float | ❌ No | Latitud para ordenar por distancia | 19.432847 |
| `longitude` | float | ❌ No | Longitud para ordenar por distancia | -99.133208 |
| `page` | integer | ❌ No | Número de página | 1 |
| `per_page` | integer | ❌ No | Resultados por página (max: 50) | 20 |

**Respuesta Exitosa (200):**

```json
{
  "success": true,
  "data": {
    "businesses": [
      {
        "business_id": 1,
        "business_name": "Café La Esquina",
        "phone": "+52 55 1234 5678",
        "address": "Av. Insurgentes Sur 123, Col. Roma",
        "city": "Ciudad de México",
        "state": "CDMX",
        "postal_code": "06700",
        "latitude": 19.432847,
        "longitude": -99.133208,
        "distance_km": 0.5,
        "rating": 4.5
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 5,
      "last_page": 1
    }
  },
  "message": "Búsqueda completada exitosamente"
}
```

---

## 📄 Formato de Respuestas

### Estructura Estándar

Todas las respuestas deben seguir este formato consistente:

```json
{
  "success": true|false,
  "data": {
    // Datos de la respuesta
  },
  "message": "Mensaje descriptivo",
  "errors": {
    // Solo en caso de error de validación
  }
}
```

### Modelo Business (estructura completa)

```json
{
  "business_id": 1,
  "business_name": "Nombre del Negocio",
  "phone": "+52 55 1234 5678",
  "address": "Dirección corta",
  "address_details": "Detalles adicionales de la dirección",
  "city": "Ciudad",
  "state": "Estado",
  "postal_code": "Código Postal",
  "latitude": 19.432847,
  "longitude": -99.133208,
  "distance_km": 1.5,
  "is_open": true,
  "rating": 4.5,
  "total_reviews": 150,
  "created_at": "2024-01-15T10:30:00.000000Z",
  "updated_at": "2024-11-25T15:45:00.000000Z"
}
```

---

## ✅ Validaciones

### Validación de Coordenadas Geográficas

```php
// Reglas de validación Laravel
$rules = [
    'latitude' => 'required|numeric|min:-90|max:90',
    'longitude' => 'required|numeric|min:-180|max:180',
    'radius' => 'integer|min:1|max:100', // máximo 100 km
    'limit' => 'integer|min:1|max:50',
    'page' => 'integer|min:1',
];
```

### Validación de Datos del Negocio

```php
$rules = [
    'latitude' => 'nullable|numeric|min:-90|max:90',
    'longitude' => 'nullable|numeric|min:-180|max:180',
    'address_details' => 'nullable|string|max:500',
    'city' => 'nullable|string|max:100',
    'state' => 'nullable|string|max:100',
    'postal_code' => 'nullable|string|max:20',
    'is_location_public' => 'boolean',
];
```

---

## 🔒 Consideraciones de Seguridad

### 1. Rate Limiting

```php
// Limitar peticiones por IP/usuario
// Recomendado: 60 peticiones por minuto
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/businesses/nearby', 'BusinessController@nearby');
});
```

### 2. Validación de Entrada

- Sanitizar todas las entradas de usuario
- Validar rangos de latitud/longitud
- Prevenir inyección SQL usando Eloquent ORM
- Validar que el radio de búsqueda sea razonable (max 100km)

### 3. Privacidad de Ubicación

- Respetar el flag `is_location_public`
- Solo mostrar ubicaciones de negocios que hayan consentido
- No almacenar la ubicación del usuario sin permiso explícito
- Cumplir con regulaciones de privacidad (GDPR, LFPDPPP)

### 4. Autenticación (Opcional)

```php
// Si se requiere autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/businesses/nearby', 'BusinessController@nearby');
});
```

---

## 💡 Ejemplos de Uso

### Ejemplo 1: Buscar negocios cercanos (JavaScript/Fetch)

```javascript
// Desde la app Flutter (generado automáticamente)
const latitude = 19.432847;
const longitude = -99.133208;
const radius = 5;

const response = await fetch(
  `https://api.tudominio.com/api/v1/businesses/nearby?latitude=${latitude}&longitude=${longitude}&radius=${radius}&limit=10`,
  {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer YOUR_TOKEN_HERE' // Si se requiere
    }
  }
);

const data = await response.json();
console.log(data.data.businesses);
```

### Ejemplo 2: Calcular distancia (PHP/Laravel)

```php
// Controller method example
public function nearby(Request $request)
{
    $validated = $request->validate([
        'latitude' => 'required|numeric|min:-90|max:90',
        'longitude' => 'required|numeric|min:-180|max:180',
        'radius' => 'integer|min:1|max:100',
        'limit' => 'integer|min:1|max:50',
    ]);

    $userLat = $validated['latitude'];
    $userLng = $validated['longitude'];
    $radius = $validated['radius'] ?? 10; // default 10km
    $limit = $validated['limit'] ?? 20;

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
    ]);
}
```

### Ejemplo 3: cURL desde terminal

```bash
# Buscar negocios cercanos
curl -X GET "https://api.tudominio.com/api/v1/businesses/nearby?latitude=19.432847&longitude=-99.133208&radius=5&limit=10" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"

# Obtener detalle de negocio con distancia
curl -X GET "https://api.tudominio.com/api/v1/businesses/1?user_latitude=19.432847&user_longitude=-99.133208" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

---

## 🗺️ Algoritmo de Cálculo de Distancia (Haversine)

### Fórmula Matemática

La fórmula Haversine calcula la distancia del círculo máximo entre dos puntos en una esfera:

```
a = sin²(Δφ/2) + cos(φ1) × cos(φ2) × sin²(Δλ/2)
c = 2 × atan2(√a, √(1−a))
d = R × c

donde:
- φ = latitud
- λ = longitud
- R = radio de la Tierra (6371 km)
- Δφ = diferencia de latitudes
- Δλ = diferencia de longitudes
```

### Implementación en SQL (MySQL/PostgreSQL)

```sql
-- Query completo con cálculo de distancia
SELECT
    *,
    ( 6371 * acos(
        cos( radians(:user_lat) ) *
        cos( radians( latitude ) ) *
        cos( radians( longitude ) - radians(:user_lng) ) +
        sin( radians(:user_lat) ) *
        sin( radians( latitude ) )
    ) ) AS distance_km
FROM businesses
WHERE is_location_public = 1
    AND latitude IS NOT NULL
    AND longitude IS NOT NULL
HAVING distance_km <= :radius
ORDER BY distance_km ASC
LIMIT :limit;
```

---

## 📊 Base de Datos - Script Completo de Migración

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
            // Coordenadas geográficas
            $table->decimal('latitude', 10, 8)->nullable()->after('phone');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');

            // Información de dirección
            $table->text('address_details')->nullable()->after('address');
            $table->string('city', 100)->nullable()->after('address_details');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');

            // Control de visibilidad
            $table->boolean('is_location_public')->default(true)->after('postal_code');

            // Índices para optimizar búsquedas
            $table->index(['latitude', 'longitude'], 'idx_location');
            $table->index(['city', 'state'], 'idx_city_state');
        });
    }

    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex('idx_location');
            $table->dropIndex('idx_city_state');

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

---

## 🧪 Testing

### Casos de Prueba Recomendados

1. **Búsqueda con ubicación válida**
   - Input: `lat=19.432847, lng=-99.133208, radius=5`
   - Esperado: Lista de negocios dentro de 5km

2. **Búsqueda sin resultados**
   - Input: Coordenadas en medio del océano
   - Esperado: Array vacío con mensaje apropiado

3. **Validación de coordenadas inválidas**
   - Input: `lat=200, lng=300`
   - Esperado: Error 400 con mensaje de validación

4. **Paginación**
   - Input: `page=2, per_page=10`
   - Esperado: Segunda página de resultados

5. **Radio excesivo**
   - Input: `radius=1000`
   - Esperado: Error de validación o limitado a máximo permitido

### Test Unitario Ejemplo (PHPUnit)

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusinessLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_nearby_businesses_returns_correct_results()
    {
        // Arrange: Crear negocios de prueba
        $business = Business::factory()->create([
            'latitude' => 19.432847,
            'longitude' => -99.133208,
            'is_location_public' => true,
        ]);

        // Act: Hacer petición al endpoint
        $response = $this->getJson('/api/v1/businesses/nearby?' . http_build_query([
            'latitude' => 19.432847,
            'longitude' => -99.133208,
            'radius' => 10,
        ]));

        // Assert: Verificar respuesta
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'businesses' => [
                        '*' => [
                            'business_id',
                            'business_name',
                            'latitude',
                            'longitude',
                            'distance_km',
                        ]
                    ],
                    'user_location',
                    'search_radius_km',
                ],
                'message',
            ]);
    }

    public function test_nearby_businesses_validates_coordinates()
    {
        $response = $this->getJson('/api/v1/businesses/nearby?' . http_build_query([
            'latitude' => 200, // Inválido
            'longitude' => 300, // Inválido
        ]));

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['latitude', 'longitude']);
    }
}
```

---

## 📚 Referencias y Recursos

### Documentación Recomendada

- **Fórmula Haversine:** https://en.wikipedia.org/wiki/Haversine_formula
- **Laravel Validation:** https://laravel.com/docs/validation
- **MySQL Spatial Extensions:** https://dev.mysql.com/doc/refman/8.0/en/spatial-extensions.html
- **Google Maps API (referencia):** https://developers.google.com/maps/documentation

### Librerías PHP Útiles

```bash
# Cálculos geográficos avanzados
composer require mjaschen/phpgeo

# Validación de coordenadas
composer require geocoder-php/geocoder
```

---

## 🔄 Versioning

| Versión | Fecha | Cambios |
|---------|-------|---------|
| 1.0.0 | 2024-11-26 | Documento inicial con todos los requisitos |

---

## 📞 Contacto y Soporte

Para dudas o aclaraciones sobre estos requisitos:

- **Equipo Mobile:** order-qr-mobile@tudominio.com
- **Equipo Backend:** order-qr-backend@tudominio.com
- **Documentación adicional:** https://docs.tudominio.com/order-qr

---

## ✅ Checklist de Implementación

### Backend Laravel

- [ ] Crear migración para agregar campos de ubicación a tabla `businesses`
- [ ] Ejecutar migración en base de datos
- [ ] Crear/actualizar modelo `Business` con campos de ubicación
- [ ] Implementar método `nearby()` en `BusinessController`
- [ ] Implementar método `show()` con cálculo de distancia opcional
- [ ] Implementar método `search()` para búsqueda por ciudad/estado
- [ ] Agregar validación de coordenadas geográficas
- [ ] Configurar rate limiting en rutas
- [ ] Crear tests unitarios para endpoints de ubicación
- [ ] Documentar endpoints en Postman/Swagger
- [ ] Probar con datos de producción reales

### Integración Mobile

- [ ] Actualizar modelo `Business` en Flutter con campos de ubicación
- [ ] Crear servicio para obtener ubicación del usuario
- [ ] Implementar pantalla de "Negocios Cercanos"
- [ ] Agregar mapa con marcadores de negocios (Google Maps/MapBox)
- [ ] Implementar filtros por distancia y búsqueda
- [ ] Solicitar permisos de ubicación al usuario
- [ ] Manejar casos sin permiso de ubicación
- [ ] Optimizar rendimiento con caché local
- [ ] Probar en dispositivos reales

---

**Generado para:** Order QR Mobile App
**Fecha:** 2024-11-26
**Versión App:** 1.0.0
**API Base URL:** `https://tu-backend.com/api/v1`

---

_Este documento es una guía completa para que el equipo de backend Laravel implemente todos los endpoints y funcionalidades necesarias para que la app móvil pueda mostrar negocios cercanos basándose en geolocalización._
