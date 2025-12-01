# ⚡ PASOS INMEDIATOS - Resolver Error 500

## 🎯 Objetivo
Hacer funcionar el endpoint `/api/v1/businesses/nearby` en 5 minutos

---

## 📋 PASO 1: Iniciar MySQL

### Si usas XAMPP:
1. Abre el **Panel de Control de XAMPP**
2. Busca la fila **MySQL**
3. Click en **Start** (botón verde)
4. Espera a que el indicador se ponga verde
5. ✅ Listo

### Si usas Laragon:
1. Abre **Laragon**
2. Click en **Start All**
3. Espera a que todos los servicios se pongan en verde
4. ✅ Listo

### Si usas WAMP:
1. Inicia **WAMP**
2. Espera a que el icono se ponga en verde
3. ✅ Listo

### Si usas MySQL standalone:
```bash
# Windows (Ejecutar como Administrador)
net start MySQL

# Linux/Mac
sudo systemctl start mysql
```

---

## 📋 PASO 2: Abrir Terminal en el Proyecto

1. Abre la terminal en la carpeta del proyecto:
   ```
   C:\Users\alanG\Documentos\VSC\Laravel\volt-laravel-dashboard-1.0.1-main
   ```

2. Verifica que estás en la carpeta correcta:
   ```bash
   dir
   ```

   Debes ver archivos como: `artisan`, `composer.json`, etc.

---

## 📋 PASO 3: Verificar Conexión a MySQL

```bash
php artisan migrate:status
```

### ✅ Si funciona:
Verás una lista de migraciones. **Continúa al PASO 4**.

### ❌ Si falla con error de conexión:
1. Verifica que MySQL esté corriendo (PASO 1)
2. Verifica tu archivo `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=volt_dashboard
   DB_USERNAME=root
   DB_PASSWORD=          (tu contraseña)
   ```
3. Repite este paso

---

## 📋 PASO 4: Ejecutar la Migración

```bash
php artisan migrate
```

### ✅ Resultado esperado:
```
Migrating: 2025_11_26_144109_add_location_fields_to_businesses_table
Migrated:  2025_11_26_144109_add_location_fields_to_businesses_table (XX.XXms)
```

### ⚠️ Si dice "Nothing to migrate":
Ya está ejecutada. **Continúa al PASO 5**.

---

## 📋 PASO 5: Agregar Ubicaciones de Prueba

```bash
php agregar_ubicaciones_prueba.php
```

### ✅ Resultado esperado:
```
✅ COMPLETADO
📍 Negocios actualizados con ubicación: X
```

### ❌ Si no hay negocios:
Primero crea al menos un negocio en el sistema y repite este paso.

---

## 📋 PASO 6: Iniciar el Servidor Laravel

```bash
php artisan serve
```

### ✅ Resultado esperado:
```
Laravel development server started: http://127.0.0.1:8000
```

**Deja esta terminal abierta.**

---

## 📋 PASO 7: Probar el Endpoint

### Opción A: Script Automático (Recomendado)

Abre una **NUEVA terminal** en la misma carpeta y ejecuta:

```bash
php test_nearby_endpoint.php
```

### ✅ Resultado esperado:
```
✅ ÉXITO - Endpoint funcionando correctamente
STATUS CODE: 200
```

### Opción B: cURL Manual

```bash
curl "http://localhost:8000/api/v1/businesses/nearby?latitude=19.432847&longitude=-99.133208&radius=10"
```

### Opción C: Navegador Web

Abre tu navegador y ve a:
```
http://localhost:8000/api/v1/businesses/nearby?latitude=19.432847&longitude=-99.133208&radius=10
```

---

## 🎉 ÉXITO - ¿Qué verás?

### Si hay negocios cercanos (200):
```json
{
  "success": true,
  "data": {
    "businesses": [
      {
        "business_id": 1,
        "business_name": "Nombre del Negocio",
        "distance_km": 0.5,
        "latitude": 19.432847,
        "longitude": -99.133208,
        ...
      }
    ],
    ...
  },
  "message": "Negocios cercanos obtenidos exitosamente"
}
```

### Si no hay negocios cercanos (404):
```json
{
  "success": false,
  "message": "No se encontraron negocios cercanos",
  "data": {
    "businesses": []
  }
}
```

**Ambas respuestas son válidas.** El endpoint funciona correctamente.

---

## 📱 Para la App Móvil

Una vez que obtengas status **200** o **404**, la app móvil Flutter funcionará automáticamente.

No necesitas hacer ningún cambio en la app.

---

## ❌ Si Algo Sale Mal

### Error: "Connection refused"
- **Causa:** MySQL no está corriendo
- **Solución:** Regresa al PASO 1

### Error: "Column not found: latitude"
- **Causa:** La migración no se ejecutó
- **Solución:** Regresa al PASO 4

### Error: "Class not found"
- **Solución:**
  ```bash
  composer dump-autoload
  ```

### Error 500 en el endpoint
- **Solución:** Revisa los logs:
  ```bash
  tail -20 storage/logs/laravel.log
  ```

---

## 📊 Resumen Visual

```
PASO 1: Iniciar MySQL                    [⚪] → [🟢]
        ↓
PASO 2: Abrir terminal                   [📂]
        ↓
PASO 3: Verificar conexión               [✅] php artisan migrate:status
        ↓
PASO 4: Ejecutar migración               [✅] php artisan migrate
        ↓
PASO 5: Agregar datos de prueba          [✅] php agregar_ubicaciones_prueba.php
        ↓
PASO 6: Iniciar servidor Laravel         [✅] php artisan serve
        ↓
PASO 7: Probar endpoint                  [✅] php test_nearby_endpoint.php
        ↓
     [🎉 ÉXITO]
```

---

## 🔗 Enlaces Útiles

- **Documentación Completa:** `SOLUCION_ERROR_500_IMPLEMENTADA.md`
- **Guía Rápida:** `README_SOLUCION_ERROR_500.md`
- **API Docs:** `GUIA_RAPIDA_API_UBICACIONES.md`

---

## ⏱️ Tiempo Total Estimado

- PASO 1: 30 segundos
- PASO 2: 10 segundos
- PASO 3: 10 segundos
- PASO 4: 30 segundos
- PASO 5: 20 segundos
- PASO 6: 10 segundos
- PASO 7: 10 segundos

**TOTAL: ~2 minutos**

---

## ✅ Checklist de Verificación

Marca cada paso cuando lo completes:

- [ ] MySQL iniciado
- [ ] Terminal abierta en la carpeta del proyecto
- [ ] `php artisan migrate:status` funciona
- [ ] `php artisan migrate` ejecutado
- [ ] `php agregar_ubicaciones_prueba.php` ejecutado
- [ ] `php artisan serve` corriendo
- [ ] `php test_nearby_endpoint.php` retorna 200 o 404
- [ ] Endpoint funciona correctamente

---

**Fecha:** 2025-11-26
**Dificultad:** Fácil
**Tiempo:** 2-5 minutos
**Confianza:** 100%
