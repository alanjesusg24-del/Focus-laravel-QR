# PROMPT 1 - ANÁLISIS INICIAL

Copia todo el contenido de abajo y pégalo en Claude Code:

---

Hola Claude Code, necesito tu ayuda para reestructurar mi proyecto Laravel llamado "Focus QR System" para cumplir con el estándar CETAM.

CONTEXTO DEL PROYECTO:
- Nombre: Focus QR System
- Código: FQR
- Slug: focus-qr
- Descripción: Sistema de gestión de órdenes con notificaciones mediante códigos QR
- Tecnologías: Laravel (backend) + Flutter (app móvil)

FLUJO DEL SISTEMA:
1. Se genera orden en Laravel y código QR único
2. Cliente escanea QR con app móvil Flutter y orden se asocia al dispositivo
3. Cuando orden está lista, se marca en Laravel
4. Se envía notificación push al dispositivo móvil del cliente
5. Cliente llega al punto de entrega y muestra QR generado en su app
6. Se escanea QR con lector físico en computadora
7. Orden se marca como entregada

IMPORTANTE - TENGO 3 GUÍAS:
He generado 3 guías de migración que están en la carpeta raíz del proyecto:
1. Guia-Analisis-Completo-Pre-Restructuracion.md
2. Guia-Reestructuracion-Carpetas-Focus-QR.md
3. Guia-Migracion-Estandar-Laravel-CETAM.md

FASE 1 - ANÁLISIS (HAZLO AHORA):
1. LEE COMPLETAMENTE la guía "Guia-Analisis-Completo-Pre-Restructuracion.md"
2. EJECUTA TODOS los scripts bash de análisis que aparecen en esa guía
3. ANALIZA EXHAUSTIVAMENTE todo el proyecto actual:
   - TODOS los Controllers (app/Http/Controllers/)
   - TODOS los Models (app/Models/ o app/)
   - TODAS las Vistas (resources/views/)
   - TODAS las Rutas (routes/web.php, routes/api.php)
   - TODAS las Migrations (database/migrations/)
   - Middleware personalizado (app/Http/Middleware/)
   - Jobs y Queues (app/Jobs/)
   - Services existentes (si hay)
   - Archivos de configuración (config/)
   - composer.json y package.json
   - Variables de entorno (.env.example)

4. GENERA todos estos archivos de inventario:
   - project-analysis.txt
   - controllers-inventory.txt
   - models-inventory.txt
   - views-inventory.txt
   - routes-analysis.txt
   - middleware-analysis.txt
   - database-analysis.txt
   - dependencies.txt
   - external-services.txt
   - api-analysis.txt

5. CREA el documento "RESTRUCTURE-PLAN.md" COMPLETO Y DETALLADO con:
   - Inventario completo de archivos
   - Funcionalidades identificadas por módulo
   - Servicios externos detectados
   - Dependencias entre archivos
   - Plan de movimiento de archivos (tabla completa)
   - Referencias a actualizar
   - Orden de ejecución paso a paso
   - Riesgos identificados
   - Tiempo estimado

REGLAS CRÍTICAS:
- NO MUEVAS NINGÚN ARCHIVO TODAVÍA
- NO MODIFIQUES NINGÚN CÓDIGO TODAVÍA
- SOLO ANALIZA Y GENERA EL PLAN
- Si encuentras algo que no entiendas, PREGÚNTAME
- Si necesitas más contexto sobre alguna funcionalidad, PREGÚNTAME
- NO ASUMAS nada, ANALIZA todo

Por favor, comienza confirmando que:
1. Leíste y entendiste la guía de análisis
2. Tienes acceso a todos los archivos del proyecto
3. Estás listo para comenzar el análisis exhaustivo

Luego procede con el análisis completo.
