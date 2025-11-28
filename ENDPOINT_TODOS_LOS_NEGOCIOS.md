# Implementar Endpoint para Obtener Todos los Negocios

## Problema
La aplicación móvil necesita un endpoint para obtener **TODOS** los negocios registrados en la base de datos (con paginación), sin requerir parámetros de búsqueda.

Actualmente, el endpoint `/businesses/search` requiere parámetros de búsqueda obligatorios, lo que genera un error 400 cuando se intenta obtener todos los negocios.

## Solución Requerida

### 1. Crear nuevo endpoint en `routes/api.php`

Agregar el siguiente endpoint **ANTES** de las rutas protegidas por autenticación:

```php
// Endpoint público para obtener todos los negocios (con paginación)
Route::get('/businesses', [BusinessController::class, 'index']);
```

**Ubicación sugerida**: Después de `/businesses/nearby` y antes de `/businesses/search`

### 2. Implementar método `index()` en `BusinessController`

Agregar el siguiente método en `app/Http/Controllers/Api/V1/BusinessController.php`:

```php
/**
 * Obtener todos los negocios (con paginación)
 *
 * @param Request $request
 * @return JsonResponse
 */
public function index(Request $request)
{
    try {
        // Parámetros de paginación
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        // Validar que per_page no sea excesivo
        if ($perPage > 500) {
            $perPage = 500;
        }

        \Log::info('📋 Obteniendo todos los negocios', [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        // Obtener todos los negocios con paginación
        $businesses = Business::with(['owner'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        \Log::info('✅ Negocios obtenidos', [
            'total' => $businesses->total(),
            'current_page' => $businesses->currentPage(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Negocios obtenidos exitosamente',
            'data' => [
                'businesses' => $businesses->items(),
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
        \Log::error('❌ Error al obtener todos los negocios', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los negocios',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

### 3. Verificar que existe el modelo Business

Asegúrate de que el modelo `Business` esté importado en el controlador:

```php
use App\Models\Business;
```

### 4. (Opcional) Agregar filtros adicionales

Si quieres permitir filtros opcionales en el mismo endpoint:

```php
public function index(Request $request)
{
    try {
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        if ($perPage > 500) {
            $perPage = 500;
        }

        // Query base
        $query = Business::with(['owner']);

        // Filtros opcionales
        if ($request->has('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->has('state')) {
            $query->where('state', $request->input('state'));
        }

        if ($request->has('with_location') && $request->input('with_location') == '1') {
            $query->whereNotNull('latitude')
                  ->whereNotNull('longitude');
        }

        // Ordenar y paginar
        $businesses = $query->orderBy('created_at', 'desc')
                           ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Negocios obtenidos exitosamente',
            'data' => [
                'businesses' => $businesses->items(),
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
        \Log::error('❌ Error al obtener todos los negocios', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los negocios',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

## Estructura esperada de la respuesta

```json
{
    "success": true,
    "message": "Negocios obtenidos exitosamente",
    "data": {
        "businesses": [
            {
                "business_id": 1,
                "business_name": "Restaurante El Buen Sabor",
                "address": "Calle Principal 123",
                "city": "Ciudad de México",
                "state": "CDMX",
                "postal_code": "01000",
                "phone": "5555555555",
                "latitude": 19.4326,
                "longitude": -99.1332,
                "is_open": true,
                "rating": 4.5,
                "total_reviews": 120,
                "created_at": "2024-01-15T10:00:00.000000Z",
                "updated_at": "2024-01-20T15:30:00.000000Z",
                "owner": {
                    "user_id": 1,
                    "name": "Juan Pérez",
                    "email": "juan@example.com"
                }
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total": 50,
            "total_pages": 3,
            "from": 1,
            "to": 20
        }
    }
}
```

## Parámetros de consulta (Query Parameters)

| Parámetro | Tipo | Requerido | Por defecto | Descripción |
|-----------|------|-----------|-------------|-------------|
| `page` | integer | No | 1 | Número de página |
| `per_page` | integer | No | 20 | Registros por página (máx: 500) |
| `city` | string | No | - | Filtrar por ciudad (opcional) |
| `state` | string | No | - | Filtrar por estado (opcional) |
| `with_location` | boolean | No | - | Solo negocios con coordenadas GPS |

## Ejemplos de uso

### Obtener todos los negocios (primera página)
```
GET /api/v1/businesses
```

### Obtener página 2 con 50 registros por página
```
GET /api/v1/businesses?page=2&per_page=50
```

### Obtener solo negocios de una ciudad
```
GET /api/v1/businesses?city=Ciudad de México
```

### Obtener solo negocios con ubicación GPS
```
GET /api/v1/businesses?with_location=1
```

## Testing

Después de implementar, prueba con:

```bash
# Sin autenticación (endpoint público)
curl -X GET "http://localhost:8000/api/v1/businesses" \
     -H "Accept: application/json"

# Con paginación
curl -X GET "http://localhost:8000/api/v1/businesses?page=1&per_page=10" \
     -H "Accept: application/json"
```

## Notas importantes

1. **Este endpoint debe ser PÚBLICO** (no requiere autenticación)
2. El límite máximo de `per_page` es 500 para evitar sobrecargas
3. Los negocios se ordenan por fecha de creación descendente (más recientes primero)
4. La relación `owner` se carga automáticamente con `with(['owner'])`
5. El endpoint retorna estructura compatible con la app móvil

## Actualizar en Flutter

Una vez implementado el endpoint, actualizar `lib/config/api_config.dart`:

```dart
static const String businessesAll = '/businesses';
```

Y usar en `api_service.dart`:
```dart
final response = await _dio.get(
  ApiConfig.businessesAll,
  queryParameters: {
    'page': page,
    'per_page': perPage,
  },
);
```

## Checklist de implementación

- [ ] Agregar ruta en `routes/api.php`
- [ ] Implementar método `index()` en `BusinessController`
- [ ] Verificar import del modelo `Business`
- [ ] Probar endpoint con Postman/cURL
- [ ] Verificar que retorna estructura JSON correcta
- [ ] Confirmar que la paginación funciona
- [ ] (Opcional) Agregar filtros adicionales
- [ ] Notificar al equipo de Flutter para actualizar configuración

## Prioridad
🔴 **ALTA** - La app móvil requiere este endpoint para la funcionalidad "Todos los Negocios"
