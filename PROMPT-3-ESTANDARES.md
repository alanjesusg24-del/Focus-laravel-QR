# PROMPT 3 - ESTÁNDARES DE CÓDIGO

⚠️ **USA ESTE PROMPT SOLO DESPUÉS DE:**
- Que la reestructuración de carpetas esté completa
- Que hayas verificado que todo funciona
- Que hayas hecho pruebas de funcionalidad

Copia todo el contenido de abajo y pégalo en Claude Code:

---

Excelente Claude Code, la reestructuración de carpetas está completa y todo funciona.

FASE 3 - APLICAR ESTÁNDARES DE CÓDIGO:

1. LEE la guía "Guia-Migracion-Estandar-Laravel-CETAM.md"

2. APLICA estos estándares a TODO el código:
   a) Agregar cabecera CETAM a todos los archivos (Controllers, Services, Repositories, Models, Views)
   b) Verificar y corregir nomenclatura (camelCase, PascalCase, kebab-case)
   c) Verificar líneas de máximo 120 caracteres
   d) Agregar PHPDoc completo en todos los métodos públicos
   e) Verificar uso correcto de llaves según PSR-12
   f) Implementar early return donde sea necesario
   g) Verificar que no haya lógica de negocio en Controllers
   h) Mover lógica de negocio a Services donde corresponda

3. HAZLO MÓDULO POR MÓDULO:
   - Primero: Controllers/Orders/
   - Segundo: Controllers/QrCodes/
   - Tercero: Controllers/Devices/
   - Cuarto: Controllers/Notifications/
   - Quinto: Controllers/Deliveries/
   - Sexto: Services/
   - Séptimo: Repositories/
   - Octavo: Models/
   - Noveno: Vistas

4. PARA CADA MÓDULO:
   - Muéstrame un resumen de los cambios importantes
   - Espera mi aprobación antes de continuar al siguiente
   - Si algo requiere decisión de negocio, PREGÚNTAME

5. FORMATO DE CABECERA CETAM:
```php
<?php
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Archivo: [NombreArchivo.php]
 * Proyecto: Focus QR System (FQR)
 * Descripción: [Descripción del archivo]
 * Autor: [Tu Nombre]
 * Fecha de creación: [Fecha]
 * Versión: 1.0.0
 */
```

Para vistas Blade:
```blade
{{--
/**
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * 
 * Vista: [nombre-vista.blade.php]
 * Proyecto: Focus QR System (FQR)
 * Descripción: [Descripción]
 * Autor: [Tu Nombre]
 * Fecha de creación: [Fecha]
 * Versión: 1.0.0
 */
--}}
```

REGLAS:
- Hazlo archivo por archivo
- Muéstrame cambios significativos
- No cambies funcionalidad, solo formato y estructura
- Si tienes duda sobre algún estándar, PREGÚNTAME

Comienza con Controllers/Orders/ - Muéstrame qué archivos hay y comenzaremos con el primero.
