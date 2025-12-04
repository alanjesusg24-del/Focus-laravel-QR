# PROMPT 2 - REESTRUCTURACIÓN DE CARPETAS

⚠️ **USA ESTE PROMPT SOLO DESPUÉS DE:**
- Que Claude Code haya terminado el análisis
- Que hayas revisado el archivo RESTRUCTURE-PLAN.md
- Que hayas aprobado el plan

Copia todo el contenido de abajo y pégalo en Claude Code:

---

Perfecto Claude Code, he revisado el plan y está completo.

FASE 2 - REESTRUCTURACIÓN DE CARPETAS:

1. LEE COMPLETAMENTE la guía "Guia-Reestructuracion-Carpetas-Focus-QR.md"

2. SIGUE EXACTAMENTE el plan que generaste en "RESTRUCTURE-PLAN.md"

3. EJECUTA la reestructuración EN ESTE ORDEN:
   a) Crear todas las carpetas necesarias
   b) Crear Services y Repositories vacíos (con cabeceras CETAM)
   c) Mover Controllers uno por uno y actualizar namespaces
   d) Mover Vistas y actualizar estructura
   e) Actualizar routes/web.php con nuevos namespaces y prefijos
   f) Actualizar routes/api.php
   g) Actualizar referencias a vistas en Controllers
   h) Actualizar route() en todas las vistas
   i) Actualizar redirect()->route() en Controllers

4. HAZLO PASO POR PASO:
   - Después de mover cada Controller, muéstrame el cambio
   - Después de completar cada fase (a, b, c, etc.), dame un resumen
   - Espera mi aprobación antes de continuar a la siguiente fase
   - Si algo falla o no funciona, DETENTE y dime

5. DESPUÉS DE CADA FASE:
   - Ejecuta: composer dump-autoload
   - Ejecuta: php artisan route:clear
   - Ejecuta: php artisan view:clear

REGLAS:
- NO hagas todo de golpe, FASE POR FASE
- MUÉSTRAME los cambios importantes
- Si encuentras algo no planeado, PREGÚNTAME
- MANTÉN la funcionalidad, NO rompas nada

Comienza con la FASE 2a: Crear estructura de carpetas
