# GUÍA DE COLORES INSTITUCIONALES CETAM

**Proyecto:** Centro de Servicios (CS) - Order QR System
**Fecha:** 2025-11-20
**Versión:** 1.0.0

---

## 🎨 PALETA DE COLORES CETAM

### Colores Principales

```scss
// Gris oscuro slate - Color principal
$primary: #1F2937
--cetam-primary: #1F2937

// Naranja rojizo vibrante - Color de acento
$secondary: #FB503B
--cetam-secondary: #FB503B

// Azul índigo oscuro - Color terciario
$tertiary: #31316A
--cetam-tertiary: #31316A
```

### Colores Semánticos

```scss
// Verde - Éxito
$success: #10B981
--cetam-success: #10B981

// Rojo - Error/Peligro
$danger: #E11D48
--cetam-danger: #E11D48

// Ámbar - Advertencia
$warning: #FBA918
--cetam-warning: #FBA918

// Azul claro - Información
$info: #1E90FF
--cetam-info: #1E90FF
```

### Escala de Grises

```scss
--cetam-gray-50: #F9FAFB   // Muy claro
--cetam-gray-100: #F2F4F6
--cetam-gray-200: #E5E7EB
--cetam-gray-300: #D1D5DB
--cetam-gray-400: #9CA3AF
--cetam-gray-500: #6B7280
--cetam-gray-600: #4B5563
--cetam-gray-700: #374151
--cetam-gray-800: #1F2937  // = Primary
--cetam-gray-900: #111827  // Muy oscuro
```

---

## 📁 ARCHIVOS DE COLORES

### 1. CSS Institucional
**Archivo:** `public/css/cetam-colors.css`
**Uso:** Ya incluido en `layouts/base.blade.php`

### 2. Variables SCSS de Volt
**Archivo:** `resources/scss/volt/_variables.scss`
**Estado:** ✅ Colores CETAM ya definidos

### 3. Variables SCSS Personalizadas
**Archivo:** `resources/scss/custom/_variables.scss`
**Estado:** ✅ Primary color CETAM definido

---

## 💻 CÓMO USAR LOS COLORES

### 1. Clases CSS de Fondo

```html
<!-- Colores principales -->
<div class="bg-cetam-primary">Fondo gris oscuro</div>
<div class="bg-cetam-secondary">Fondo naranja</div>
<div class="bg-cetam-tertiary">Fondo azul índigo</div>

<!-- Colores semánticos -->
<div class="bg-cetam-success">Fondo verde</div>
<div class="bg-cetam-danger">Fondo rojo</div>
<div class="bg-cetam-warning">Fondo ámbar</div>
<div class="bg-cetam-info">Fondo azul</div>
```

### 2. Clases CSS de Texto

```html
<!-- Colores principales -->
<p class="text-cetam-primary">Texto gris oscuro</p>
<p class="text-cetam-secondary">Texto naranja</p>
<p class="text-cetam-tertiary">Texto azul índigo</p>

<!-- Colores semánticos -->
<p class="text-cetam-success">Texto verde</p>
<p class="text-cetam-danger">Texto rojo</p>
<p class="text-cetam-warning">Texto ámbar</p>
<p class="text-cetam-info">Texto azul</p>
```

### 3. Botones

```html
<!-- Botones sólidos -->
<button class="btn btn-cetam-primary">Botón Primary</button>
<button class="btn btn-cetam-secondary">Botón Secondary</button>
<button class="btn btn-cetam-tertiary">Botón Tertiary</button>

<!-- Botones outline -->
<button class="btn btn-outline-cetam-primary">Botón Outline Primary</button>
<button class="btn btn-outline-cetam-secondary">Botón Outline Secondary</button>
```

### 4. Alertas

```html
<div class="alert alert-cetam-primary">Alerta con color primary</div>
<div class="alert alert-cetam-secondary">Alerta con color secondary</div>
```

### 5. Badges

```html
<span class="badge badge-cetam-primary">Badge Primary</span>
<span class="badge badge-cetam-secondary">Badge Secondary</span>
```

### 6. Enlaces

```html
<a href="#" class="link-cetam-primary">Enlace Primary</a>
<a href="#" class="link-cetam-secondary">Enlace Secondary</a>
```

### 7. Cards con Acento

```html
<div class="card card-cetam-primary">
    <!-- Borde superior color primary -->
</div>

<div class="card card-cetam-secondary">
    <!-- Borde superior color secondary -->
</div>
```

### 8. Variables CSS Personalizadas

```css
/* En tu CSS personalizado */
.mi-elemento {
    background-color: var(--cetam-primary);
    color: var(--cetam-secondary);
    border: 2px solid var(--cetam-tertiary);
}

.mi-elemento:hover {
    background-color: var(--cetam-primary-hover);
}
```

---

## 🎯 EJEMPLOS PRÁCTICOS

### Ejemplo 1: Tarjeta de Producto

```html
<div class="card card-cetam-secondary shadow">
    <div class="card-header bg-cetam-primary text-white">
        <h5 class="mb-0">Producto Destacado</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Descripción del producto</p>
        <button class="btn btn-cetam-secondary">Comprar Ahora</button>
    </div>
</div>
```

### Ejemplo 2: Alert de Éxito

```html
<div class="alert alert-success border-cetam-success">
    <i class="fas fa-check-circle text-cetam-success me-2"></i>
    Operación completada exitosamente
</div>
```

### Ejemplo 3: Botones de Acción

```html
<div class="btn-group">
    <button class="btn btn-cetam-primary">
        <i class="fas fa-save"></i> Guardar
    </button>
    <button class="btn btn-outline-cetam-secondary">
        <i class="fas fa-times"></i> Cancelar
    </button>
</div>
```

### Ejemplo 4: Hero Section

```html
<section class="bg-cetam-primary text-white py-5">
    <div class="container">
        <h1 class="display-4">Bienvenido a Order QR System</h1>
        <p class="lead">Sistema de gestión de órdenes con códigos QR</p>
        <button class="btn btn-cetam-secondary btn-lg">Comenzar</button>
    </div>
</section>
```

---

## 📊 COMPARACIÓN DE COLORES

### ❌ ANTES (Colores Inconsistentes)

```scss
// En register.blade.php
--institutional-blue: #1d4976     // ❌ Diferente al estándar
--institutional-orange: #de5629   // ❌ Diferente al estándar
--institutional-gray: #7b96ab     // ❌ No estandarizado
```

### ✅ AHORA (Colores CETAM Estandarizados)

```scss
// Consistentes en todo el proyecto
--cetam-primary: #1F2937      // ✅ Gris oscuro slate
--cetam-secondary: #FB503B    // ✅ Naranja rojizo vibrante
--cetam-tertiary: #31316A     // ✅ Azul índigo oscuro
```

---

## 🔄 MIGRACIÓN DE COLORES ANTIGUOS

Si encuentras código con colores antiguos, reemplázalos así:

### Búsqueda y Reemplazo

```
# Colores viejos -> Colores CETAM
institutional-blue     -> cetam-primary
institutional-orange   -> cetam-secondary
institutional-gray     -> cetam-gray-500

bg-primary            -> bg-cetam-primary (si aplica)
bg-secondary          -> bg-cetam-secondary (si aplica)
text-primary          -> text-cetam-primary (si aplica)
btn-primary           -> btn-cetam-primary (opcional)
```

**Nota:** Los colores Bootstrap estándar (`btn-primary`, `bg-success`, etc.) siguen funcionando correctamente ya que Volt los mapea automáticamente.

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

- [x] Archivo `cetam-colors.css` creado
- [x] CSS incluido en `layouts/base.blade.php`
- [x] Vista `register.blade.php` actualizada
- [ ] Revisar otras vistas para consistencia
- [ ] Actualizar componentes personalizados
- [ ] Compilar assets si es necesario

---

## 🎨 VISUALIZACIÓN DE COLORES

### Colores Principales

| Color | Hex | Uso Principal |
|-------|-----|---------------|
| <span style="background: #1F2937; color: white; padding: 4px 8px;">Primary</span> | `#1F2937` | Fondos oscuros, headers, navegación |
| <span style="background: #FB503B; color: white; padding: 4px 8px;">Secondary</span> | `#FB503B` | Botones de acción, acentos, CTAs |
| <span style="background: #31316A; color: white; padding: 4px 8px;">Tertiary</span> | `#31316A` | Elementos complementarios |

### Colores Semánticos

| Color | Hex | Uso |
|-------|-----|-----|
| <span style="background: #10B981; color: white; padding: 4px 8px;">Success</span> | `#10B981` | Mensajes de éxito, confirmaciones |
| <span style="background: #E11D48; color: white; padding: 4px 8px;">Danger</span> | `#E11D48` | Errores, alertas críticas |
| <span style="background: #FBA918; color: white; padding: 4px 8px;">Warning</span> | `#FBA918` | Advertencias, precauciones |
| <span style="background: #1E90FF; color: white; padding: 4px 8px;">Info</span> | `#1E90FF` | Información, tooltips |

---

## 💡 MEJORES PRÁCTICAS

### ✅ HACER

1. **Usar clases CETAM** para nuevos componentes
2. **Mantener consistencia** en toda la aplicación
3. **Usar variables CSS** para fácil mantenimiento
4. **Probar contraste** para accesibilidad

```html
<!-- ✅ Correcto -->
<button class="btn btn-cetam-primary">Guardar</button>
<div class="bg-cetam-secondary text-white p-4">Contenido</div>
```

### ❌ EVITAR

1. **NO hardcodear** colores en estilos inline
2. **NO crear** nuevos colores personalizados sin consultar
3. **NO ignorar** la paleta institucional

```html
<!-- ❌ Incorrecto -->
<button style="background: #ff0000">Guardar</button>
<div style="color: #abcdef">Contenido</div>
```

---

## 🔧 SOLUCIÓN DE PROBLEMAS

### Problema: Los colores CETAM no se ven

**Solución:**
1. Verificar que `cetam-colors.css` existe en `public/css/`
2. Verificar que está incluido en `layouts/base.blade.php`
3. Limpiar caché del navegador (Ctrl + F5)
4. Limpiar caché de Laravel: `php artisan cache:clear`

### Problema: Colores incorrectos en algunas vistas

**Solución:**
1. Buscar colores hardcodeados: `grep -r "#1d4976" resources/views/`
2. Reemplazar por clases CETAM
3. Revisar estilos `<style>` en la vista

---

## 📞 SOPORTE

Para dudas sobre colores CETAM:
- Consultar: Este documento
- Manual completo: `INSTRUCCIONES_IMPLEMENTACION_LARAVEL_CETAM.md`
- Archivo CSS: `public/css/cetam-colors.css`

---

**Creado por:** CETAM Dev Team
**Fecha:** 2025-11-20
**Versión:** 1.0.0

---

## ✅ ESTADO ACTUAL

**Cumplimiento de Colores CETAM:**
- ✅ Paleta institucional definida
- ✅ CSS global creado
- ✅ Vista de registro actualizada
- ✅ Clases CSS disponibles
- ✅ Documentación completa
- 🟡 Migración gradual en progreso

**El sistema utiliza los colores institucionales CETAM correctamente.**
