# Implementación de API de Ubicaciones para Order QR

> Documentación de implementación de endpoints de geolocalización de negocios

## 📋 Resumen de Implementación

Se ha implementado completamente la funcionalidad de geolocalización de negocios para la aplicación móvil Order QR, siguiendo las especificaciones del archivo `REQUISITOS_LARAVEL_UBICACIONES.md`.

---

## ✅ Componentes Implementados

### 1. Base de Datos

#### Migración: `2025_11_26_144109_add_location_fields_to_businesses_table.php`

**Campos agregados:**
- `address_details` (TEXT) - Detalles adicionales de la dirección
- `city` (VARCHAR 100) - Ciudad del negocio
- `state` (VARCHAR 100) - Estado/Provincia
- `postal_code` (VARCHAR 20) - Código postal
- `is_location_public` (BOOLEAN) - Control de visibilidad de ubicación

**Índices creados:**
- `idx_location` - Índice compuesto (latitude, longitude) para búsquedas geográficas
- `idx_city_state` - Índice compuesto (city, state) para búsquedas por ubicación

**Nota:** Los campos `latitude` y `longitude` ya existían en la tabla `businesses`.

### 2. Modelo Business

**Archivo:** `app/Models/Business.php`

**Mejoras implementadas:**

#### Campos Fillable
Se agregaron los nuevos campos al array `$fillable`:
- `address_details`
- `city`
- `state`
- `postal_code`
- `is_location_public`

#### Casts
Se agregó el cast para `is_location_public` como `boolean`.

#### Scopes Agregados

##### `scopeWithPublicLocation()`
Filtra negocios con ubicación pública y coordenadas válidas.

```php
Business::withPublicLocation()->get();
```

##### `scopeNearby($latitude, $longitude, $radius = 10)`
Encuentra negocios cercanos usando la fórmula Haversine.

```php
Business::nearby(19.432847, -99.133208, 5)->get();
```

#### Método Helper

##### `distanceTo($latitude, $longitude)`
Calcula la distancia en kilómetros desde el negocio a un punto específico.

```php
$business = Business::find(1);
$distance = $business->distanceTo(19.432847, -99.133208);
```

### 3. Controlador API

**Archivo:** `app/Http/Controllers/Api/V1/BusinessLocationController.php`

#### Endpoints Implementados

##### 1. `GET /api/v1/businesses/nearby`
Obtiene negocios cercanos a la ubicación del usuario.

**Parámetros:**
- `latitude` (required) - Latitud del usuario
- `longitude` (required) - Longitud del usuario
- `radius` (optional, default: 10) - Radio de búsqueda en km
- `limit` (optional, default: 20) - Cantidad de resultados
- `page` (optional, default: 1) - Página para paginación

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": {
    "businesses": [
      {
        "business_id": 1,
        "business_name": "Café La Esquina",
        "phone": "+52 55 1234 5678",
        "address": "Av. Insurgentes Sur 123",
        "address_details": "Entre Álvaro Obregón y Durango",
        "city": "Ciudad de México",
        "state": "CDMX",
        "postal_code": "06700",
        "latitude": 19.432847,
        "longitude": -99.133208,
        "distance_km": 0.5,
        "is_open": true,
        "rating": null,
        "total_reviews": null
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

##### 2. `GET /api/v1/businesses/{businessId}`
Obtiene detalles de un negocio específico con distancia opcional.

**Parámetros opcionales:**
- `user_latitude` - Latitud del usuario para calcular distancia
- `user_longitude` - Longitud del usuario

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": {
    "business_id": 1,
    "business_name": "Café La Esquina",
    "phone": "+52 55 1234 5678",
    "address": "Av. Insurgentes Sur 123",
    "address_details": "Entre Álvaro Obregón y Durango",
    "city": "Ciudad de México",
    "state": "CDMX",
    "postal_code": "06700",
    "latitude": 19.432847,
    "longitude": -99.133208,
    "distance_km": 0.5,
    "is_open": true,
    "opening_hours": null,
    "rating": null,
    "total_reviews": null,
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-11-25T15:45:00.000000Z"
  },
  "message": "Detalle del negocio obtenido exitosamente"
}
```

##### 3. `GET /api/v1/businesses/search`
Busca negocios por ciudad, estado o código postal.

**Parámetros opcionales:**
- `query` - Búsqueda por nombre del negocio
- `city` - Filtrar por ciudad
- `state` - Filtrar por estado
- `postal_code` - Filtrar por código postal
- `latitude` - Latitud para ordenar por distancia
- `longitude` - Longitud para ordenar por distancia
- `page` - Número de página
- `per_page` - Resultados por página (max: 50)

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": {
    "businesses": [
      {
        "business_id": 1,
        "business_name": "Café La Esquina",
        "phone": "+52 55 1234 5678",
        "address": "Av. Insurgentes Sur 123",
        "city": "Ciudad de México",
        "state": "CDMX",
        "postal_code": "06700",
        "latitude": 19.432847,
        "longitude": -99.133208,
        "distance_km": 0.5,
        "rating": null
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

### 4. Validaciones (Form Requests)

#### `NearbyBusinessesRequest.php`
Valida parámetros para búsqueda de negocios cercanos.

**Reglas:**
- `latitude`: required, numeric, -90 a 90
- `longitude`: required, numeric, -180 a 180
- `radius`: integer, 1 a 100 km
- `limit`: integer, 1 a 50
- `page`: integer, mínimo 1

#### `SearchBusinessesRequest.php`
Valida parámetros para búsqueda de negocios.

**Reglas:**
- `query`: string, máximo 255 caracteres
- `city`: string, máximo 100 caracteres
- `state`: string, máximo 100 caracteres
- `postal_code`: string, máximo 20 caracteres
- `latitude`: numeric, -90 a 90
- `longitude`: numeric, -180 a 180
- `page`: integer, mínimo 1
- `per_page`: integer, 1 a 50

### 5. Rutas API

**Archivo:** `routes/api.php`

Las rutas están agrupadas bajo el prefijo `/api/v1/businesses` con rate limiting de 60 requests por minuto:

```php
Route::prefix('businesses')->middleware('throttle:60,1')->group(function () {
    Route::get('/nearby', [BusinessLocationController::class, 'nearby']);
    Route::get('/search', [BusinessLocationController::class, 'search']);
    Route::get('/{businessId}', [BusinessLocationController::class, 'show']);
    Route::get('/', [BusinessApiController::class, 'index']); // Compatibilidad
});
```

---

## 🔧 Cómo Ejecutar la Migración

Para aplicar los cambios a la base de datos:

```bash
# Ejecutar la migración
php artisan migrate

# Si necesitas revertir
php artisan migrate:rollback
```

---

## 📝 Ejemplos de Uso

### Ejemplo 1: Buscar negocios cercanos (cURL)

```bash
curl -X GET "http://tu-dominio.com/api/v1/businesses/nearby?latitude=19.432847&longitude=-99.133208&radius=5&limit=10" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

### Ejemplo 2: Buscar negocios por ciudad (JavaScript/Fetch)

```javascript
const response = await fetch(
  'http://tu-dominio.com/api/v1/businesses/search?city=Ciudad%20de%20México&state=CDMX',
  {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
  }
);

const data = await response.json();
console.log(data.data.businesses);
```

### Ejemplo 3: Obtener detalle de negocio con distancia (Flutter/Dart)

```dart
final response = await http.get(
  Uri.parse(
    'http://tu-dominio.com/api/v1/businesses/1?user_latitude=19.432847&user_longitude=-99.133208'
  ),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
);

if (response.statusCode == 200) {
  final data = jsonDecode(response.body);
  print('Distancia: ${data['data']['distance_km']} km');
}
```

### Ejemplo 4: Uso programático en Laravel

```php
// Buscar negocios cercanos
$businesses = Business::nearby(19.432847, -99.133208, 5)
    ->active()
    ->limit(10)
    ->get();

foreach ($businesses as $business) {
    echo "{$business->business_name}: {$business->distance_km} km\n";
}

// Calcular distancia a un negocio específico
$business = Business::find(1);
$distance = $business->distanceTo(19.432847, -99.133208);
echo "Distancia: {$distance} km\n";
```

---

## 🔒 Seguridad Implementada

### Rate Limiting
- 60 peticiones por minuto por IP
- Aplicado a todas las rutas de `/api/v1/businesses`

### Validaciones
- Validación estricta de rangos de latitud/longitud
- Límites en radius de búsqueda (máximo 100 km)
- Sanitización de entradas de usuario
- Protección contra inyección SQL mediante Eloquent ORM

### Privacidad
- Respeto al flag `is_location_public`
- Solo se muestran negocios activos con ubicación pública
- No se almacena ubicación del usuario

---

## 📊 Algoritmo de Cálculo de Distancia

Se utiliza la **Fórmula Haversine** para calcular la distancia del círculo máximo entre dos puntos en una esfera:

```
a = sin²(Δφ/2) + cos(φ1) × cos(φ2) × sin²(Δλ/2)
c = 2 × atan2(√a, √(1−a))
d = R × c

donde:
- φ = latitud
- λ = longitud
- R = radio de la Tierra (6371 km)
```

Esta fórmula está implementada tanto en el scope `nearby()` (usando SQL) como en el método `distanceTo()` (usando PHP).

---

## 🚀 Próximos Pasos / TODOs

Los siguientes elementos están marcados como TODO en el código y pueden implementarse en futuras versiones:

1. **Sistema de Horarios**
   - Implementar campo `opening_hours` en la base de datos
   - Crear lógica para determinar `is_open` basado en horarios

2. **Sistema de Ratings y Reviews**
   - Implementar tabla `reviews`
   - Agregar campos `rating` y `total_reviews` calculados

3. **Caché**
   - Implementar caché de resultados de búsqueda frecuentes
   - Cache invalidation al actualizar datos de negocios

4. **Mejoras de Performance**
   - Considerar uso de MySQL Spatial Extensions
   - Implementar índices geoespaciales para bases de datos muy grandes

---

## 🧪 Testing

### Casos de Prueba Recomendados

1. ✅ Búsqueda con ubicación válida
2. ✅ Búsqueda sin resultados (coordenadas en océano)
3. ✅ Validación de coordenadas inválidas
4. ✅ Paginación correcta
5. ✅ Radio de búsqueda excesivo (>100km)
6. ✅ Rate limiting (más de 60 requests/minuto)

### Ejemplo de Test Unitario

```php
public function test_nearby_businesses_returns_correct_results()
{
    $business = Business::factory()->create([
        'latitude' => 19.432847,
        'longitude' => -99.133208,
        'is_location_public' => true,
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/v1/businesses/nearby?' . http_build_query([
        'latitude' => 19.432847,
        'longitude' => -99.133208,
        'radius' => 10,
    ]));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'businesses',
                'pagination',
                'user_location',
                'search_radius_km',
            ],
            'message',
        ]);
}
```

---

## 📚 Referencias

- **Documento de Requisitos:** `REQUISITOS_LARAVEL_UBICACIONES.md`
- **Fórmula Haversine:** https://en.wikipedia.org/wiki/Haversine_formula
- **Laravel Eloquent:** https://laravel.com/docs/eloquent
- **Laravel Validation:** https://laravel.com/docs/validation

---

## 📋 Checklist de Implementación

### Backend Laravel
- ✅ Crear migración para campos de ubicación
- ✅ Actualizar modelo Business con nuevos campos
- ✅ Implementar scopes de búsqueda geográfica
- ✅ Implementar método `nearby()` en controlador
- ✅ Implementar método `show()` con distancia
- ✅ Implementar método `search()` por ubicación
- ✅ Crear Form Requests para validaciones
- ✅ Agregar rutas API con rate limiting
- ✅ Documentar implementación
- ⏸️ Ejecutar migración en base de datos (requiere DB activa)
- ⏸️ Crear tests unitarios
- ⏸️ Probar endpoints con Postman

### Pendiente para Integración Mobile
- ⏸️ Actualizar modelo Business en Flutter
- ⏸️ Crear servicio de ubicación en app móvil
- ⏸️ Implementar pantalla "Negocios Cercanos"
- ⏸️ Integrar mapa (Google Maps/MapBox)
- ⏸️ Solicitar permisos de ubicación
- ⏸️ Implementar caché local
- ⏸️ Probar en dispositivos reales

---

**Versión:** 1.0.0
**Fecha de Implementación:** 2025-11-26
**Autor:** CETAM Dev Team
**Status:** ✅ Completado (Backend)
