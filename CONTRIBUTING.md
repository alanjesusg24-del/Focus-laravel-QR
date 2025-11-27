# 🤝 Guía de Contribución - Order QR System

¡Gracias por tu interés en contribuir al proyecto Order QR System! Esta guía te ayudará a contribuir de manera efectiva.

---

## 📋 Tabla de Contenidos

- [Código de Conducta](#código-de-conducta)
- [¿Cómo Puedo Contribuir?](#cómo-puedo-contribuir)
- [Configuración del Entorno de Desarrollo](#configuración-del-entorno-de-desarrollo)
- [Proceso de Pull Request](#proceso-de-pull-request)
- [Estándares de Código](#estándares-de-código)
- [Estándares de Commits](#estándares-de-commits)
- [Reportar Bugs](#reportar-bugs)
- [Sugerir Mejoras](#sugerir-mejoras)

---

## 📜 Código de Conducta

### Nuestro Compromiso

Nos comprometemos a hacer de la participación en nuestro proyecto una experiencia libre de acoso para todos, independientemente de:

- Edad
- Tamaño corporal
- Discapacidad
- Etnia
- Identidad y expresión de género
- Nivel de experiencia
- Nacionalidad
- Apariencia personal
- Raza
- Religión
- Identidad u orientación sexual

### Nuestros Estándares

**Comportamientos que contribuyen a crear un ambiente positivo:**

✅ Uso de lenguaje acogedor e inclusivo
✅ Respeto a diferentes puntos de vista y experiencias
✅ Aceptar críticas constructivas con gracia
✅ Enfocarse en lo que es mejor para la comunidad
✅ Mostrar empatía hacia otros miembros de la comunidad

**Comportamientos inaceptables:**

❌ Uso de lenguaje o imágenes sexualizadas
❌ Trolling, comentarios insultantes o ataques personales
❌ Acoso público o privado
❌ Publicar información privada de otros sin permiso
❌ Conducta que podría considerarse inapropiada en un entorno profesional

---

## 🎯 ¿Cómo Puedo Contribuir?

### 1. Reportar Bugs

Si encuentras un bug, por favor:

1. **Busca** si el issue ya existe
2. Si no existe, **crea uno nuevo** incluyendo:
   - Descripción clara del problema
   - Pasos para reproducir
   - Comportamiento esperado vs actual
   - Screenshots (si aplica)
   - Versión de PHP, Laravel, navegador
   - Logs de error relevantes

### 2. Sugerir Mejoras

Para sugerir nuevas características:

1. **Verifica** que no exista una sugerencia similar
2. **Crea un issue** describiendo:
   - El problema que resuelve
   - La solución propuesta
   - Alternativas consideradas
   - Impacto en usuarios existentes

### 3. Contribuir con Código

Para contribuir código:

1. **Fork** el repositorio
2. **Crea** una rama desde `main`
3. **Desarrolla** tu feature/fix
4. **Escribe** tests
5. **Documenta** tus cambios
6. **Envía** un Pull Request

### 4. Mejorar Documentación

La documentación siempre puede mejorar:

- Corregir typos o errores
- Clarificar instrucciones confusas
- Agregar ejemplos
- Traducir documentación

---

## 💻 Configuración del Entorno de Desarrollo

### Requisitos Previos

- PHP 8.2+
- Composer 2.7+
- Node.js 22.x
- MySQL 8.0+
- Git 2.44+

### Setup Inicial

```bash
# 1. Fork y clonar
git clone https://github.com/TU-USUARIO/order-qr-system.git
cd order-qr-system

# 2. Agregar repositorio original como upstream
git remote add upstream https://github.com/ORIGINAL-USUARIO/order-qr-system.git

# 3. Instalar dependencias
composer install
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar base de datos en .env
# Luego ejecutar:
php artisan migrate
php artisan db:seed

# 6. Enlazar storage
php artisan storage:link

# 7. Compilar assets
npm run dev
```

### Mantener tu Fork Actualizado

```bash
# Obtener cambios del repositorio original
git fetch upstream

# Mergear cambios a tu rama main local
git checkout main
git merge upstream/main

# Push a tu fork
git push origin main
```

---

## 🔄 Proceso de Pull Request

### 1. Crear una Rama

```bash
git checkout -b feature/nombre-descriptivo
# o
git checkout -b fix/descripcion-del-bug
```

**Convenciones de nombres de ramas:**

- `feature/` - Nuevas características
- `fix/` - Corrección de bugs
- `docs/` - Cambios en documentación
- `refactor/` - Refactorización de código
- `test/` - Agregar o corregir tests
- `chore/` - Cambios en build o dependencias

### 2. Hacer Commits

```bash
git add .
git commit -m "feat: Descripción clara del cambio"
```

Ver [Estándares de Commits](#estándares-de-commits) abajo.

### 3. Push a tu Fork

```bash
git push origin feature/nombre-descriptivo
```

### 4. Crear Pull Request

1. Ve a GitHub y navega a tu fork
2. Haz clic en "Compare & pull request"
3. **Completa la plantilla:**
   - Descripción del cambio
   - Tipo de cambio (feature/fix/docs/etc)
   - ¿Rompe compatibilidad? (breaking change)
   - Tests agregados/actualizados
   - Screenshots (si aplica)
   - Issues relacionados

### 5. Revisión de Código

- **Responde** a comentarios de revisores
- **Actualiza** tu PR según feedback
- **Resuelve** conflictos si los hay
- **Espera** aprobación de al menos 1 revisor

### 6. Merge

Una vez aprobado, un mantenedor hará merge de tu PR.

---

## 📝 Estándares de Código

### PHP (PSR-12)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Usar camelCase para variables
        $allItems = Item::all();

        // Usar type hints
        return view('items.index', [
            'items' => $allItems,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validar datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ]);

        // Crear recurso
        Item::create($validated);

        // Retornar con mensaje flash
        return redirect()
            ->route('items.index')
            ->with('success', 'Item creado correctamente');
    }
}
```

### Blade Templates

```blade
{{-- resources/views/items/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Listado de Items')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Items</h5>
                    <a href="{{ route('items.create') }}" class="btn btn-primary">
                        <x-icon name="add" class="me-2" />
                        Nuevo Item
                    </a>
                </div>

                <div class="card-body">
                    @forelse($items as $item)
                        <div class="item-card">
                            {{ $item->name }}
                        </div>
                    @empty
                        <p class="text-muted text-center">No hay items</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### JavaScript (ES6+)

```javascript
// resources/js/components/example.js

/**
 * Inicializar componente de ejemplo
 */
function initExample() {
    const button = document.getElementById('example-btn');

    if (!button) return;

    button.addEventListener('click', handleClick);
}

/**
 * Manejar click del botón
 * @param {Event} event
 */
function handleClick(event) {
    event.preventDefault();

    // Usar fetch para llamadas API
    fetch('/api/example', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ data: 'value' }),
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initExample);
```

### Estándares CETAM (OBLIGATORIOS)

#### Sistema de Iconos

```blade
<!-- ✅ CORRECTO -->
<x-icon name="user" />
<x-icon name="save" class="me-2" />
<button class="btn btn-primary">
    <x-icon name="add" class="me-2" />
    Nuevo
</button>

<!-- ❌ INCORRECTO -->
<i class="fa-solid fa-user"></i>
<svg>...</svg>
<span class="icon">👤</span>
```

#### Paleta de Colores

```scss
// ✅ CORRECTO - Usar variables CETAM
.btn-custom {
    background-color: $primary;  // #1F2937
    color: white;
}

// ❌ INCORRECTO - Colores hardcoded
.btn-custom {
    background-color: #123456;
    color: #ffffff;
}
```

#### Notificaciones

```javascript
// ✅ CORRECTO - Usar Notyf/SweetAlert2
notyf.success('Guardado correctamente');

Swal.fire({
    title: '¿Confirmar?',
    icon: 'warning',
    iconColor: '#FBA918',
    confirmButtonColor: '#EF4444',
});

// ❌ INCORRECTO - alert() nativo
alert('Guardado correctamente');
```

---

## 📋 Estándares de Commits

Seguimos [Conventional Commits](https://www.conventionalcommits.org/):

### Formato

```
<tipo>(<scope>): <descripción>

[cuerpo opcional]

[footer(s) opcional(es)]
```

### Tipos de Commits

| Tipo | Descripción | Ejemplo |
|------|-------------|---------|
| `feat` | Nueva funcionalidad | `feat: agregar módulo de reportes` |
| `fix` | Corrección de bug | `fix: corregir cálculo de totales` |
| `docs` | Cambios en documentación | `docs: actualizar README con setup` |
| `style` | Formato (no afecta código) | `style: formatear código con PSR-12` |
| `refactor` | Refactorización | `refactor: simplificar lógica de pagos` |
| `test` | Agregar/corregir tests | `test: agregar tests para OrderController` |
| `chore` | Cambios en build/deps | `chore: actualizar Laravel a 12.37` |
| `perf` | Mejora de performance | `perf: optimizar query de órdenes` |

### Ejemplos

```bash
# Feature simple
git commit -m "feat: agregar exportación de órdenes a Excel"

# Fix con descripción detallada
git commit -m "fix: corregir validación de RFC en registro

El validador permitía RFCs de 11 caracteres cuando el mínimo
son 12. Se actualiza la regla de validación.

Fixes #123"

# Breaking change
git commit -m "feat!: cambiar estructura de API de órdenes

BREAKING CHANGE: El endpoint /api/orders ahora retorna un
objeto paginado en lugar de array directo. Ver docs de migración."

# Con scope
git commit -m "feat(payments): integrar Stripe como método de pago"
git commit -m "fix(auth): corregir redirección después de login"
```

### Reglas

✅ **DO:**
- Usar imperativo ("agregar" no "agregado")
- Primera letra minúscula
- No punto final en el subject
- Descripción clara y concisa
- Referenciar issues (#123)

❌ **DON'T:**
- Commits vagos ("fix", "update")
- Commits masivos (separar en commits lógicos)
- Commits de merge manual
- Mezclar múltiples cambios no relacionados

---

## 🐛 Reportar Bugs

### Antes de Reportar

1. **Actualiza** a la última versión
2. **Busca** en issues existentes
3. **Verifica** que no sea un problema de configuración

### Plantilla de Reporte

```markdown
**Descripción del Bug**
Descripción clara y concisa de qué es el bug.

**Pasos para Reproducir**
1. Ve a '...'
2. Haz clic en '....'
3. Desplázate hasta '....'
4. Ver error

**Comportamiento Esperado**
Descripción clara de qué esperabas que sucediera.

**Comportamiento Actual**
Descripción de qué sucedió realmente.

**Screenshots**
Si aplica, agregar screenshots.

**Entorno:**
 - OS: [e.g. Windows 11, Ubuntu 22.04]
 - PHP: [e.g. 8.2.10]
 - Laravel: [e.g. 12.36.1]
 - Navegador: [e.g. Chrome 120, Firefox 121]
 - Base de Datos: [e.g. MySQL 8.0.35]

**Logs de Error**
```
[Pega logs relevantes de storage/logs/laravel.log]
```

**Contexto Adicional**
Cualquier otro contexto sobre el problema.
```

---

## 💡 Sugerir Mejoras

### Plantilla de Feature Request

```markdown
**¿Tu feature request está relacionada con un problema?**
Descripción clara del problema. Ej. "Siempre me frustra cuando [...]"

**Describe la solución que te gustaría**
Descripción clara de qué quieres que suceda.

**Describe alternativas que consideraste**
Descripción de soluciones o features alternativas.

**Contexto Adicional**
Agrega contexto, screenshots o mockups.

**¿Estarías dispuesto a implementarlo?**
- [ ] Sí, puedo implementarlo y crear un PR
- [ ] No, pero puedo ayudar con testing
- [ ] No, solo reporto la idea
```

---

## ✅ Checklist Pre-PR

Antes de enviar tu Pull Request, verifica:

### Código
- [ ] El código sigue los estándares CETAM
- [ ] Usas `<x-icon>` para todos los iconos
- [ ] Usas paleta de colores institucional
- [ ] No hay código comentado o debug statements
- [ ] Variables y funciones tienen nombres descriptivos

### Tests
- [ ] Tests existentes pasan
- [ ] Agregaste tests para nueva funcionalidad
- [ ] Coverage no disminuyó

### Documentación
- [ ] Comentarios actualizados
- [ ] README actualizado (si aplica)
- [ ] CHANGELOG actualizado
- [ ] DocBlocks completos

### Git
- [ ] Commits siguen Conventional Commits
- [ ] Branch está actualizado con `main`
- [ ] No hay conflictos
- [ ] Historia de commits es limpia

### Frontend
- [ ] Diseño responsive (mobile, tablet, desktop)
- [ ] Funciona en Chrome, Firefox, Safari, Edge
- [ ] Assets compilados (`npm run production`)
- [ ] No hay errores de consola

### Backend
- [ ] Migraciones funcionan correctamente
- [ ] Seeders actualizados (si aplica)
- [ ] No hay queries N+1
- [ ] Validaciones implementadas

---

## 🎓 Recursos Útiles

### Documentación
- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Estándares CETAM](estandares-frontend-laravel.md)
- [PSR-12](https://www.php-fig.org/psr/psr-12/)
- [Conventional Commits](https://www.conventionalcommits.org/)

### Herramientas
- **PHP CodeSniffer** - Linting PHP
- **ESLint** - Linting JavaScript
- **Laravel Pint** - Formateo automático
- **PHPStan** - Análisis estático

### Instalar Herramientas

```bash
# Laravel Pint (ya incluido)
./vendor/bin/pint

# PHP CodeSniffer
composer require --dev squizlabs/php_codesniffer
./vendor/bin/phpcs

# PHPStan
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse
```

---

## 🙏 Agradecimientos

¡Gracias por contribuir a Order QR System! Cada contribución, grande o pequeña, es valiosa y apreciada.

### Contributors

Ver la lista completa de contribuidores en [GitHub Contributors](https://github.com/tu-usuario/order-qr-system/graphs/contributors).

---

## 📞 Contacto

¿Tienes preguntas sobre cómo contribuir?

- **GitHub Discussions:** [Discusiones](https://github.com/tu-usuario/order-qr-system/discussions)
- **Issues:** [Reportar un issue](https://github.com/tu-usuario/order-qr-system/issues)
- **Email:** dev@cetam.mx

---

<div align="center">

**© 2025 CETAM - Guía de Contribución**

[Código de Conducta](#código-de-conducta) · [Reportar Bug](#reportar-bugs) · [Solicitar Feature](#sugerir-mejoras)

</div>
