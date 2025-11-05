# MÓDULO 10: COMANDOS ARTISAN Y CRON JOBS - COMPLETADO

## Sistema de Gestión de Órdenes con QR - Order QR System CETAM

**Fecha de completado:** 2025-11-04
**Versión:** 1.0

---

## RESUMEN DE IMPLEMENTACIÓN

El Módulo 10 implementa comandos Artisan personalizados y tareas programadas para automatizar operaciones críticas del sistema. Incluye:

- ✅ 4 comandos Artisan personalizados
- ✅ Task Scheduler configurado en Kernel.php
- ✅ Logging automático de tareas programadas
- ✅ Soporte para dry-run y opciones configurables
- ✅ Reportes exportables en múltiples formatos
- ✅ Integración con cron jobs

---

## COMANDOS ARTISAN IMPLEMENTADOS

### 1. CleanExpiredOrders

**Comando:** `php artisan orders:clean-expired`

**Descripción:** Limpia órdenes expiradas basándose en los días de retención del plan de cada negocio.

**Opciones:**
```bash
--dry-run              # Ejecuta sin eliminar (solo muestra qué se eliminaría)
--business=<ID>        # Limpia solo un negocio específico
--days=<número>        # Sobrescribe los días de retención del plan
```

**Ejemplos de uso:**
```bash
# Vista previa de qué órdenes se eliminarían
php artisan orders:clean-expired --dry-run

# Limpiar todas las órdenes expiradas
php artisan orders:clean-expired

# Limpiar solo un negocio específico
php artisan orders:clean-expired --business=5

# Sobrescribir días de retención (eliminar todo > 90 días)
php artisan orders:clean-expired --days=90
```

**Características:**
- Solo elimina órdenes con status `delivered` o `cancelled`
- Respeta el `retention_days` de cada plan
- Muestra progreso con barra de progreso
- Tabla resumen con estadísticas
- Elimina solo órdenes activas de negocios activos

**Salida esperada:**
```
🗑️  Starting order cleanup process...

  📦 Taquería El Buen Sabor (ID: 1)
     Retention: 30 days | Cutoff: 2025-10-05
     Orders deleted: 45

✅ Cleanup process completed!
┌────────────────────────┬───────┐
│ Metric                 │ Value │
├────────────────────────┼───────┤
│ Businesses processed   │ 12    │
│ Orders deleted         │ 178   │
│ Mode                   │ LIVE  │
└────────────────────────┴───────┘
```

---

### 2. CheckExpiredPayments

**Comando:** `php artisan payments:check-expired`

**Descripción:** Verifica pagos expirados y opcionalmente desactiva negocios.

**Opciones:**
```bash
--deactivate           # Desactiva negocios con pagos expirados
--notify               # Envía notificaciones por email
--grace-days=<número>  # Días de gracia antes de desactivar (default: 3)
```

**Ejemplos de uso:**
```bash
# Solo verificar sin acciones
php artisan payments:check-expired

# Verificar y desactivar negocios
php artisan payments:check-expired --deactivate

# Verificar, desactivar y notificar
php artisan payments:check-expired --deactivate --notify

# Cambiar periodo de gracia a 7 días
php artisan payments:check-expired --deactivate --grace-days=7
```

**Características:**
- Identifica negocios con pagos expirados
- Periodo de gracia configurable antes de desactivar
- Opción de enviar notificaciones por email
- Registro detallado en logs
- Tabla con todos los negocios afectados

**Salida esperada:**
```
💳 Checking for expired payments...

┌────┬──────────────────────┬────────────┬────────────┬──────┬──────────────┐
│ ID │ Business             │ Plan       │ Expired On │ Days │ Action       │
├────┼──────────────────────┼────────────┼────────────┼──────┼──────────────┤
│ 3  │ Café La Esquina      │ Monthly    │ 2025-10-28 │ 7    │ Deactivated  │
│ 7  │ Panadería Don Pan    │ Monthly    │ 2025-11-01 │ 3    │ Grace Period │
└────┴──────────────────────┴────────────┴────────────┴──────┴──────────────┘

✅ Payment check completed!
┌────────────────────────┬───────┐
│ Metric                 │ Count │
├────────────────────────┼───────┤
│ Total businesses       │ 25    │
│ Expired payments       │ 2     │
│ In grace period        │ 1     │
│ Deactivated            │ 1     │
│ Notifications sent     │ 1     │
└────────────────────────┴───────┘
```

---

### 3. SendPaymentReminders

**Comando:** `php artisan payments:send-reminders`

**Descripción:** Envía recordatorios de renovación a negocios con pagos próximos a expirar.

**Opciones:**
```bash
--days-before=<número>  # Días antes de expiración (default: 7)
--dry-run               # Vista previa sin enviar emails
```

**Ejemplos de uso:**
```bash
# Vista previa de recordatorios
php artisan payments:send-reminders --dry-run

# Enviar recordatorios 7 días antes
php artisan payments:send-reminders --days-before=7

# Enviar recordatorios 3 días antes
php artisan payments:send-reminders --days-before=3

# Enviar recordatorios 1 día antes
php artisan payments:send-reminders --days-before=1
```

**Características:**
- Detecta automáticamente pagos próximos a expirar
- Ventana de ±1 día para encontrar negocios
- Modo dry-run para testing
- Logs detallados de cada email enviado
- Tabla con información de cada recordatorio

**Salida esperada:**
```
📧 Sending payment reminders...

Found 5 businesses requiring reminders

┌────┬──────────────────┬─────────────────────┬─────────┬────────────┬──────┬─────────┐
│ ID │ Business         │ Email               │ Plan    │ Expires    │ Days │ Amount  │
├────┼──────────────────┼─────────────────────┼─────────┼────────────┼──────┼─────────┤
│ 2  │ Restaurante XYZ  │ contact@xyz.com     │ Monthly │ 2025-11-11 │ 7    │ $299.00 │
│ 8  │ Taller ABC       │ info@abc.com        │ Monthly │ 2025-11-12 │ 8    │ $299.00 │
└────┴──────────────────┴─────────────────────┴─────────┴────────────┴──────┴─────────┘

✅ Reminder process completed!
┌────────────────────┬───────┐
│ Metric             │ Value │
├────────────────────┼───────┤
│ Reminders to send  │ 5     │
│ Emails sent        │ 5     │
│ Mode               │ LIVE  │
└────────────────────┴───────┘
```

---

### 4. GenerateSystemReport

**Comando:** `php artisan system:report`

**Descripción:** Genera reportes completos del sistema con estadísticas de uso.

**Opciones:**
```bash
--period=<días>     # Periodo del reporte en días (default: 30)
--export=<formato>  # Exportar a archivo (json, csv, txt)
```

**Ejemplos de uso:**
```bash
# Reporte de 30 días (default)
php artisan system:report

# Reporte semanal
php artisan system:report --period=7

# Reporte mensual exportado a JSON
php artisan system:report --period=30 --export=json

# Reporte anual exportado a CSV
php artisan system:report --period=365 --export=csv

# Reporte exportado a TXT
php artisan system:report --export=txt
```

**Características:**
- Estadísticas de negocios, órdenes y pagos
- Ingresos totales y por periodo
- Distribución por planes
- Estados de órdenes y pagos
- Tiempo promedio de entrega
- Tamaño de base de datos
- Exportación a múltiples formatos
- Informes guardados en `storage/app/reports/`

**Salida esperada:**
```
📊 Generating System Report...

📅 Report Period: 2025-10-05 to 2025-11-04 (30 days)

👥 BUSINESSES
┌─────────────────────┬───────┐
│ Metric              │ Value │
├─────────────────────┼───────┤
│ Total Businesses    │ 45    │
│ Active              │ 42    │
│ Inactive            │ 3     │
│ New in Period       │ 8     │
└─────────────────────┴───────┘

Plans Distribution:
  • Monthly Plan: 35
  • Annual Plan: 10

📦 ORDERS
┌─────────────────────┬────────┐
│ Metric              │ Value  │
├─────────────────────┼────────┤
│ Total Orders        │ 3,248  │
│ Orders in Period    │ 892    │
│ Avg Delivery Time   │ 45 min │
└─────────────────────┴────────┘

Orders by Status:
  • Pending: 234
  • Ready: 156
  • Delivered: 2,654
  • Cancelled: 204

💰 PAYMENTS
┌─────────────────────┬──────────────┐
│ Metric              │ Value        │
├─────────────────────┼──────────────┤
│ Total Payments      │ 125          │
│ Payments in Period  │ 42           │
│ Total Revenue       │ $37,375.00   │
│ Period Revenue      │ $12,558.00   │
└─────────────────────┴──────────────┘

Payments by Status:
  • Completed: 118
  • Pending: 5
  • Failed: 2

⚙️  SYSTEM
┌─────────────────┬─────────┐
│ Metric          │ Value   │
├─────────────────┼─────────┤
│ Database Size   │ 47.8 MB │
└─────────────────┴─────────┘

✅ Report generation completed!

📄 Report exported to: storage/app/reports/system_report_2025-11-04_103045.json
```

**Formatos de Exportación:**

**JSON:**
```json
{
  "generated_at": "2025-11-04 10:30:45",
  "period_days": 30,
  "businesses": {
    "total": 45,
    "active": 42
  },
  ...
}
```

**CSV:**
```csv
Order QR System - Report
Generated,2025-11-04 10:30:45
Period,2025-10-05 to 2025-11-04

BUSINESSES
Total businesses,45
Active,42
...
```

**TXT:**
```
ORDER QR SYSTEM - SYSTEM REPORT
==================================================

Generated: 2025-11-04 10:30:45
Period: 2025-10-05 to 2025-11-04

BUSINESSES
--------------------------------------------------
Total businesses: 45
Active: 42
...
```

---

## TASK SCHEDULER (CRON JOBS)

### Configuración en Kernel.php

Todas las tareas están configuradas en `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Limpieza de órdenes - Diaria a las 2:00 AM
    $schedule->command('orders:clean-expired')
        ->dailyAt('02:00')
        ->timezone('America/Mexico_City');

    // Verificar pagos expirados - Diaria a las 8:00 AM
    $schedule->command('payments:check-expired --deactivate --notify')
        ->dailyAt('08:00')
        ->timezone('America/Mexico_City');

    // Recordatorios 7 días antes - Diaria a las 9:00 AM
    $schedule->command('payments:send-reminders --days-before=7')
        ->dailyAt('09:00')
        ->timezone('America/Mexico_City');

    // Recordatorios 3 días antes - Diaria a las 9:00 AM
    $schedule->command('payments:send-reminders --days-before=3')
        ->dailyAt('09:00')
        ->timezone('America/Mexico_City');

    // Recordatorios 1 día antes - Diaria a las 9:00 AM
    $schedule->command('payments:send-reminders --days-before=1')
        ->dailyAt('09:00')
        ->timezone('America/Mexico_City');

    // Reporte semanal - Lunes a las 10:00 AM
    $schedule->command('system:report --period=7 --export=json')
        ->weeklyOn(1, '10:00')
        ->timezone('America/Mexico_City');

    // Reporte mensual - Día 1 a las 10:00 AM
    $schedule->command('system:report --period=30 --export=json')
        ->monthlyOn(1, '10:00')
        ->timezone('America/Mexico_City');
}
```

### Configuración del Cron Job en el Servidor

Agregar esta línea al crontab del servidor:

```bash
# Editar crontab
crontab -e

# Agregar esta línea:
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

**Explicación:**
- `* * * * *` - Ejecutar cada minuto
- Laravel verifica internamente si hay tareas programadas
- Los horarios están configurados en Kernel.php
- La salida se guarda en logs individuales

### Verificar Tareas Programadas

```bash
# Ver lista de comandos programados
php artisan schedule:list

# Probar manualmente una tarea
php artisan schedule:work

# Ver próximas ejecuciones
php artisan schedule:test
```

### Logs de Tareas Programadas

Cada tarea guarda su output en logs separados:

```
storage/logs/
├── scheduled-orders-cleanup.log
├── scheduled-payment-check.log
├── scheduled-payment-reminders.log
├── scheduled-weekly-reports.log
└── scheduled-monthly-reports.log
```

---

## TESTING DE COMANDOS

### Prueba Manual Individual

```bash
# Probar limpieza de órdenes (dry-run)
php artisan orders:clean-expired --dry-run

# Probar verificación de pagos
php artisan payments:check-expired

# Probar recordatorios
php artisan payments:send-reminders --dry-run

# Generar reporte de prueba
php artisan system:report --period=7
```

### Prueba de Scheduler

```bash
# Ejecutar todas las tareas programadas manualmente
php artisan schedule:run

# Ver qué tareas están programadas
php artisan schedule:list

# Monitorear scheduler en tiempo real
php artisan schedule:work
```

### Validar Configuración de Cron

```bash
# Verificar que el cron esté activo
sudo systemctl status cron

# Ver logs del cron
sudo tail -f /var/log/syslog | grep CRON

# Verificar ejecución de Laravel scheduler
tail -f storage/logs/laravel.log
```

---

## MEJORES PRÁCTICAS

### 1. Dry-Run Antes de Ejecutar

Siempre usar `--dry-run` primero para ver qué se hará:

```bash
php artisan orders:clean-expired --dry-run
php artisan payments:send-reminders --dry-run
```

### 2. Monitorear Logs

Revisar regularmente los logs de tareas programadas:

```bash
tail -f storage/logs/scheduled-orders-cleanup.log
tail -f storage/logs/scheduled-payment-check.log
```

### 3. Configurar Alertas

Implementar notificaciones cuando las tareas fallen:

```php
$schedule->command('orders:clean-expired')
    ->dailyAt('02:00')
    ->onFailure(function () {
        // Enviar alerta por email o Slack
    });
```

### 4. Backups Antes de Limpieza

Crear backup antes de ejecutar limpiezas masivas:

```bash
# Backup de base de datos
php artisan backup:run

# Luego ejecutar limpieza
php artisan orders:clean-expired
```

### 5. Testing en Staging

Probar todos los comandos en ambiente de staging antes de producción.

---

## COMANDOS ÚTILES

### Ver Comandos Disponibles

```bash
# Listar todos los comandos
php artisan list

# Ver comandos de órdenes
php artisan list orders

# Ver comandos de pagos
php artisan list payments

# Ver comandos del sistema
php artisan list system
```

### Obtener Ayuda

```bash
# Ayuda detallada de un comando
php artisan help orders:clean-expired
php artisan help payments:check-expired
php artisan help payments:send-reminders
php artisan help system:report
```

### Ejecutar en Background

```bash
# Ejecutar comando en segundo plano
nohup php artisan orders:clean-expired > /dev/null 2>&1 &

# Ver procesos en ejecución
ps aux | grep artisan
```

---

## TROUBLESHOOTING

### Problema: Cron no ejecuta tareas

**Solución:**
```bash
# Verificar permisos
ls -la /ruta/al/proyecto

# Verificar usuario del cron
whoami

# Verificar que artisan sea ejecutable
chmod +x artisan

# Revisar logs del cron
sudo grep CRON /var/log/syslog
```

### Problema: Error de timezone

**Solución:**
```php
// config/app.php
'timezone' => 'America/Mexico_City',
```

### Problema: Comando no encontrado

**Solución:**
```bash
# Limpiar cache de configuración
php artisan config:clear

# Verificar que el comando esté registrado
php artisan list
```

### Problema: Permisos de escritura en logs

**Solución:**
```bash
# Dar permisos a storage
chmod -R 775 storage
chown -R www-data:www-data storage

# Crear directorio de logs si no existe
mkdir -p storage/logs
chmod 775 storage/logs
```

---

## PRÓXIMOS PASOS

Con el Módulo 10 completado, la **Plataforma Web** está 100% funcional. Las siguientes fases serían:

### Fase 2: App Móvil (Flutter/React Native)
1. Escaneo de QR para vincular órdenes
2. Recepción de notificaciones push
3. Tracking de órdenes en tiempo real
4. Confirmación de recogida con QR

### Mejoras Opcionales:
1. Implementar Laravel Horizon para queue monitoring
2. Agregar Laravel Telescope para debugging
3. Implementar caché con Redis
4. Agregar tests unitarios y de integración
5. Configurar CI/CD pipeline
6. Implementar monitoreo con New Relic o Datadog

---

## RESUMEN DE COMANDOS

| Comando | Descripción | Frecuencia Recomendada |
|---------|-------------|------------------------|
| `orders:clean-expired` | Limpia órdenes antiguas | Diaria (2:00 AM) |
| `payments:check-expired` | Verifica pagos expirados | Diaria (8:00 AM) |
| `payments:send-reminders` | Envía recordatorios | Diaria (9:00 AM) |
| `system:report` | Genera reportes | Semanal/Mensual |

---

**Elaborado por:** Sistema CETAM
**Proyecto:** Order QR System - Laravel Edition
**Módulo:** 10 - Comandos Artisan y Cron Jobs
**Estado:** ✅ COMPLETADO

**¡FASE 1: PLATAFORMA WEB 100% COMPLETA!** 🎉
