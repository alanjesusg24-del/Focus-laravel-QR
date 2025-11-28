# Guía Rápida - API de Ubicaciones de Negocios

## 🚀 Inicio Rápido

### 1. Ejecutar Migración

```bash
php artisan migrate
```

### 2. Endpoints Disponibles

Base URL: `http://tu-dominio.com/api/v1/businesses`

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/nearby` | Buscar negocios cercanos |
| GET | `/search` | Buscar por ciudad/estado |
| GET | `/{id}` | Detalle de negocio |

---

## 📍 Ejemplos Rápidos

### Buscar Negocios Cercanos

```bash
curl "http://localhost/api/v1/businesses/nearby?latitude=19.432847&longitude=-99.133208&radius=5"
```

### Buscar por Ciudad

```bash
curl "http://localhost/api/v1/businesses/search?city=Ciudad%20de%20México"
```

### Detalle con Distancia

```bash
curl "http://localhost/api/v1/businesses/1?user_latitude=19.432847&user_longitude=-99.133208"
```

---

## 📋 Parámetros Principales

### `/nearby` (Negocios Cercanos)
- `latitude` ✅ **Requerido** - Latitud (-90 a 90)
- `longitude` ✅ **Requerido** - Longitud (-180 a 180)
- `radius` ⭕ Opcional - Radio en km (default: 10, max: 100)
- `limit` ⭕ Opcional - Resultados (default: 20, max: 50)
- `page` ⭕ Opcional - Página (default: 1)

### `/search` (Búsqueda)
- `query` ⭕ Opcional - Buscar por nombre
- `city` ⭕ Opcional - Filtrar por ciudad
- `state` ⭕ Opcional - Filtrar por estado
- `postal_code` ⭕ Opcional - Filtrar por CP
- `latitude` ⭕ Opcional - Para ordenar por distancia
- `longitude` ⭕ Opcional - Para ordenar por distancia

### `/{id}` (Detalle)
- `user_latitude` ⭕ Opcional - Para calcular distancia
- `user_longitude` ⭕ Opcional - Para calcular distancia

---

## 🔐 Seguridad

- **Rate Limit:** 60 requests/minuto
- **Validación:** Automática en todos los endpoints
- **Privacidad:** Solo negocios con ubicación pública

---

## 📊 Estructura de Respuesta

```json
{
  "success": true,
  "data": {
    "businesses": [...],
    "pagination": {...},
    "user_location": {...}
  },
  "message": "Mensaje descriptivo"
}
```

---

## 🛠️ Uso Programático (Laravel)

```php
// Buscar cercanos
$businesses = Business::nearby($lat, $lng, $radius)->get();

// Calcular distancia
$distance = $business->distanceTo($lat, $lng);

// Solo públicos
$public = Business::withPublicLocation()->get();
```

---

## ❗ Errores Comunes

### 400 - Parámetros Inválidos
```json
{
  "success": false,
  "message": "Parámetros de ubicación inválidos",
  "errors": {
    "latitude": ["La latitud es requerida"]
  }
}
```

### 404 - Sin Resultados
```json
{
  "success": false,
  "message": "No se encontraron negocios cercanos",
  "data": {
    "businesses": [],
    "search_radius_km": 10
  }
}
```

### 429 - Rate Limit Excedido
```json
{
  "message": "Too Many Requests"
}
```

---

## 📝 Agregar Ubicación a un Negocio

```php
$business = Business::find(1);
$business->update([
    'latitude' => 19.432847,
    'longitude' => -99.133208,
    'address_details' => 'Entre Álvaro Obregón y Durango',
    'city' => 'Ciudad de México',
    'state' => 'CDMX',
    'postal_code' => '06700',
    'is_location_public' => true,
]);
```

---

## 🧪 Prueba Rápida

```bash
# 1. Verificar que el servidor esté corriendo
curl http://localhost/api/v1/health

# 2. Probar endpoint de negocios cercanos
curl "http://localhost/api/v1/businesses/nearby?latitude=19.4326&longitude=-99.1332&radius=10"

# 3. Verificar respuesta exitosa (status 200)
```

---

## 📚 Documentación Completa

Ver `IMPLEMENTACION_UBICACIONES_API.md` para detalles completos de implementación.

Ver `REQUISITOS_LARAVEL_UBICACIONES.md` para especificaciones originales.

---

**Versión:** 1.0.0
**Última Actualización:** 2025-11-26
