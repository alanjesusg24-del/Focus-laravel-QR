# PROMPT 4 - VERIFICACIÓN FINAL

⚠️ **USA ESTE PROMPT SOLO DESPUÉS DE:**
- Que se hayan aplicado todos los estándares de código
- Que hayas revisado los cambios finales

Copia todo el contenido de abajo y pégalo en Claude Code:

---

Perfecto Claude Code, todos los estándares están aplicados.

FASE 4 - VERIFICACIÓN FINAL Y CHECKLIST:

1. EJECUTA estas verificaciones:

   a) Verificación de estructura:
   ```bash
   tree app/Http/Controllers/ -L 2
   tree resources/views/focus-qr/ -L 3
   ls -la app/Services/
   ls -la app/Repositories/
   ```

   b) Verificación de rutas:
   ```bash
   php artisan route:list | grep focus-qr
   ```

   c) Limpiar cachés:
   ```bash
   php artisan route:clear
   php artisan view:clear
   php artisan config:clear
   php artisan cache:clear
   composer dump-autoload
   ```

2. GENERA un reporte de verificación con:
   - ✅ Checklist completo del estándar CETAM
   - ✅ Lista de todos los archivos modificados
   - ✅ Lista de todos los archivos creados
   - ✅ Lista de todos los archivos movidos
   - ✅ Resumen de cambios por categoría

3. IDENTIFICA:
   - Archivos que puedan tener errores
   - Funcionalidades que necesiten prueba manual
   - Configuraciones que necesiten ajuste

4. CREA un documento "MIGRATION-SUMMARY.md" con:
   - Resumen ejecutivo de todos los cambios
   - Antes y después de la estructura
   - Guía rápida para probar funcionalidad
   - Lista de posibles puntos de fallo
   - Siguiente pasos recomendados

Por favor procede con las verificaciones y genera el reporte completo.
