# Nuevas Funcionalidades de Planes

## 📋 Resumen

Se han agregado dos nuevas funcionalidades importantes al sistema de planes de suscripción:

1. **Módulo de Chat**: Control para habilitar/deshabilitar el módulo de chat por plan
2. **Sistema de Re-Alertas**: Notificaciones automáticas periódicas para órdenes listas no recogidas

---

## 🎨 Implementación Visual (Volt Dashboard)

### Pantallas Actualizadas

#### `/superadmin/plans/create` - Crear Plan
- ✅ Card "Módulo de Chat" con switch para habilitar/deshabilitar
- ✅ Card "Re-Alertas" con configuración dinámica:
  - Switch para habilitar/deshabilitar
  - Campo "Intervalo (minutos)": De 1 a 1440 minutos
  - Campo "Máximo de alertas": De 1 a 20 alertas
  - Campos se ocultan/muestran dinámicamente con JavaScript

#### `/superadmin/plans/edit/{id}` - Editar Plan
- ✅ Misma funcionalidad que la pantalla de creación
- ✅ Valores precargados del plan existente
- ✅ Al cambiar `has_chat_module`, actualiza automáticamente todos los negocios asociados

### Componentes Volt Utilizados

```html
<!-- Cards con shadow -->
<div class="card border-0 shadow mb-4">
    <div class="card-header border-bottom">
        <h2 class="fs-5 fw-bold mb-0">
            <svg class="icon icon-xs text-primary me-2">...</svg>
            Módulo de Chat
        </h2>
    </div>
    <div class="card-body">
        <!-- Form switches de Bootstrap 5 -->
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" ...>
            <label class="form-check-label">...</label>
        </div>
    </div>
</div>

<!-- Alerts informativos -->
<div class="alert alert-info d-flex align-items-center py-2 px-3 mb-0">
    <svg class="icon icon-xs text-info me-2">...</svg>
    <small>Mensaje informativo...</small>
</div>
```

---

## 🗄️ Cambios en Base de Datos

### Nueva Migración: `2025_11_19_082110_add_chat_and_realerts_to_plans_table`

```php
Schema::table('plans', function (Blueprint $table) {
    // Módulo de chat
    $table->boolean('has_chat_module')->default(false);

    // Sistema de re-alertas
    $table->boolean('has_realerts')->default(false);
    $table->integer('realert_interval_minutes')->nullable();
    $table->integer('realert_max_count')->nullable();
});
```

### Nueva Tabla: `order_realerts`

```php
Schema::create('order_realerts', function (Blueprint $table) {
    $table->id('realert_id');
    $table->unsignedBigInteger('order_id');
    $table->integer('alert_number'); // 1, 2, 3, etc.
    $table->timestamp('sent_at');
    $table->string('notification_type')->default('ready_reminder');
    $table->boolean('was_delivered')->default(true);
    $table->text('response_message')->nullable();
    $table->timestamps();

    $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
    $table->index(['order_id', 'sent_at']);
});
```

---

## 💻 Cambios en el Código

### 1. Modelo `Plan.php`

**Nuevos campos fillable:**
```php
protected $fillable = [
    // ... campos existentes
    'has_chat_module',
    'has_realerts',
    'realert_interval_minutes',
    'realert_max_count',
];

protected $casts = [
    // ... casts existentes
    'has_chat_module' => 'boolean',
    'has_realerts' => 'boolean',
    'realert_interval_minutes' => 'integer',
    'realert_max_count' => 'integer',
];
```

### 2. Nuevo Modelo `OrderRealert.php`

```php
class OrderRealert extends Model
{
    protected $table = 'order_realerts';
    protected $primaryKey = 'realert_id';

    protected $fillable = [
        'order_id',
        'alert_number',
        'sent_at',
        'notification_type',
        'was_delivered',
        'response_message',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
```

### 3. Modelo `Order.php` - Nueva Relación

```php
public function realerts(): HasMany
{
    return $this->hasMany(OrderRealert::class, 'order_id', 'order_id');
}
```

### 4. Controlador `PlanManagementController.php`

**Método `store()` actualizado:**
```php
public function store(Request $request)
{
    $validated = $request->validate([
        // ... validaciones existentes
        'has_chat_module' => 'boolean',
        'has_realerts' => 'boolean',
        'realert_interval_minutes' => 'required_if:has_realerts,1|nullable|integer|min:1|max:1440',
        'realert_max_count' => 'required_if:has_realerts,1|nullable|integer|min:1|max:20',
    ]);

    // Convert checkbox values
    $validated['is_active'] = $request->has('is_active');
    $validated['has_chat_module'] = $request->has('has_chat_module');
    $validated['has_realerts'] = $request->has('has_realerts');

    // Clear realert fields if disabled
    if (!$validated['has_realerts']) {
        $validated['realert_interval_minutes'] = null;
        $validated['realert_max_count'] = null;
    }

    Plan::create($validated);
}
```

**Método `update()` - Lógica Especial:**
```php
public function update(Request $request, $id)
{
    // ... validación similar a store()

    // Track if chat module changed
    $chatModuleChanged = $plan->has_chat_module !== $validated['has_chat_module'];

    $plan->update($validated);

    // Update all businesses with this plan if chat module changed
    if ($chatModuleChanged) {
        $plan->businesses()->update(['has_chat_module' => $validated['has_chat_module']]);
    }
}
```

### 5. Nuevo Servicio: `PushNotificationService::sendReadyReminder()`

```php
public static function sendReadyReminder($fcmToken, $order, $alertNumber = 1)
{
    $title = $alertNumber === 1
        ? '⏰ Recordatorio: Tu orden está lista'
        : "⏰ Recordatorio #{$alertNumber}: Tu orden sigue esperando";

    $body = "La orden {$order->order_number} está lista para recoger. ¡No olvides pasar por ella!";

    $message = CloudMessage::withTarget('token', $fcmToken)
        ->withNotification(Notification::create($title, $body))
        ->withData([
            'type' => 'order_ready_reminder',
            'order_id' => (string) $order->order_id,
            'alert_number' => (string) $alertNumber,
            // ...
        ]);

    return $messaging->send($message);
}
```

---

## 🤖 Comando Artisan: `orders:send-realerts`

### Ubicación
`app/Console/Commands/SendOrderRealerts.php`

### Funcionalidad

El comando busca todas las órdenes con estado `ready` y:

1. ✅ Verifica que la orden tenga un `mobile_user_id` asociado
2. ✅ Verifica que el plan del negocio tenga `has_realerts = true`
3. ✅ Cuenta cuántas re-alertas ya se han enviado para esa orden
4. ✅ Verifica que no se haya alcanzado el máximo de alertas (`realert_max_count`)
5. ✅ Calcula el tiempo desde la última alerta (o desde `ready_at`)
6. ✅ Si ha pasado el intervalo configurado (`realert_interval_minutes`), envía la notificación
7. ✅ Registra el envío en la tabla `order_realerts`

### Uso Manual

```bash
php artisan orders:send-realerts
```

### Salida Ejemplo

```
Starting re-alerts process...
Found 9 ready orders
✓ Sent realert #1 for order ORD-001
✓ Sent realert #2 for order ORD-003
⊘ Skipped: 7

Re-alerts summary:
✓ Sent: 2
⊘ Skipped: 7
Done!
```

---

## ⏰ Tarea Programada (Scheduler)

### Configuración en `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Send re-alerts for ready orders every 5 minutes
    $schedule->command('orders:send-realerts')
        ->everyFiveMinutes()
        ->timezone('America/Mexico_City')
        ->appendOutputTo(storage_path('logs/scheduled-realerts.log'));
}
```

### Activar el Scheduler (Producción)

En **Linux/macOS** (Crontab):
```bash
crontab -e

# Agregar:
* * * * * cd /ruta/del/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

En **Windows** (Task Scheduler):
```bash
# Crear tarea que ejecute cada minuto:
php C:\ruta\del\proyecto\artisan schedule:run
```

### Verificar Logs

```bash
tail -f storage/logs/scheduled-realerts.log
```

---

## 📊 Flujo Completo del Sistema de Re-Alertas

```
┌─────────────────────────────────────────────────────────────┐
│ 1. SuperAdmin crea/edita plan con re-alertas habilitadas   │
│    - has_realerts = true                                     │
│    - realert_interval_minutes = 15 (ejemplo)                │
│    - realert_max_count = 4 (ejemplo)                        │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Negocio marca orden como "ready"                         │
│    - ready_at = ahora                                        │
│    - status = 'ready'                                        │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Scheduler ejecuta comando cada 5 minutos                 │
│    php artisan orders:send-realerts                         │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Comando busca órdenes ready + plan con realerts          │
│    - WHERE status = 'ready'                                  │
│    - AND mobile_user_id IS NOT NULL                         │
│    - AND plan.has_realerts = true                           │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Para cada orden elegible:                                │
│                                                              │
│    a. Cuenta realerts enviadas: count(order_realerts)       │
│    b. ¿Alcanzó máximo? → Skip                               │
│    c. Calcula próxima alerta:                               │
│       - Primera: ready_at + interval_minutes                │
│       - Siguientes: last_realert + interval_minutes         │
│    d. ¿Es tiempo? → Envía notificación FCM                  │
│    e. Registra en order_realerts                            │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Usuario móvil recibe notificación push                   │
│    Título: "⏰ Recordatorio #2: Tu orden sigue esperando"   │
│    Cuerpo: "La orden ORD-001 está lista para recoger..."   │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧪 Casos de Prueba

### Test 1: Crear Plan con Re-Alertas

1. Ir a `/superadmin/plans/create`
2. Llenar datos del plan
3. Activar switch "Habilitar Re-Alertas"
4. Configurar:
   - Intervalo: 15 minutos
   - Máximo: 4 alertas
5. Guardar
6. ✅ Verificar que se guardó correctamente en la BD

### Test 2: Editar Plan y Actualizar Módulo de Chat

1. Ir a `/superadmin/plans/edit/1`
2. Activar "Habilitar Chat"
3. Guardar
4. ✅ Verificar que todos los negocios con `plan_id = 1` ahora tienen `has_chat_module = true`

### Test 3: Sistema de Re-Alertas End-to-End

1. Crear orden y marcarla como "ready"
2. Asociar con usuario móvil (QR scan)
3. Esperar 15 minutos (o ajustar el intervalo a 1 minuto para prueba)
4. Ejecutar: `php artisan orders:send-realerts`
5. ✅ Verificar que se envió la notificación
6. ✅ Verificar registro en tabla `order_realerts`
7. Esperar otro intervalo y ejecutar comando nuevamente
8. ✅ Verificar que se envió re-alerta #2
9. Repetir hasta alcanzar el máximo
10. ✅ Verificar que ya no envía más alertas después del máximo

### Test 4: Validación de Formulario

1. Ir a `/superadmin/plans/create`
2. Activar "Habilitar Re-Alertas"
3. Intentar guardar sin llenar los campos
4. ✅ Verificar que aparecen mensajes de error en español
5. Intentar valores fuera de rango (ej. 2000 minutos)
6. ✅ Verificar validación correcta

---

## 🔍 Consultas SQL Útiles

### Ver planes con re-alertas activas
```sql
SELECT plan_id, name, has_realerts, realert_interval_minutes, realert_max_count
FROM plans
WHERE has_realerts = 1;
```

### Ver historial de re-alertas de una orden
```sql
SELECT
    or.alert_number,
    or.sent_at,
    or.was_delivered,
    or.response_message
FROM order_realerts or
WHERE or.order_id = 123
ORDER BY or.sent_at;
```

### Órdenes ready con contador de re-alertas enviadas
```sql
SELECT
    o.order_id,
    o.folio_number,
    o.ready_at,
    COUNT(or.realert_id) as realerts_sent,
    p.realert_max_count as max_allowed
FROM orders o
INNER JOIN businesses b ON o.business_id = b.business_id
INNER JOIN plans p ON b.plan_id = p.plan_id
LEFT JOIN order_realerts or ON o.order_id = or.order_id
WHERE o.status = 'ready'
  AND p.has_realerts = 1
GROUP BY o.order_id;
```

---

## 📝 Notas Importantes

### 1. Módulo de Chat
- ✅ Al cambiar `has_chat_module` en un plan, se actualiza automáticamente en todos los negocios asociados
- ✅ Esto asegura consistencia entre planes y negocios

### 2. Re-Alertas
- ⚠️ El scheduler debe estar activo para que funcione automáticamente
- ⚠️ Cada re-alerta se registra en la BD para auditoría
- ⚠️ Si una notificación falla, se registra con `was_delivered = false`
- ✅ El sistema respeta el máximo de alertas configurado
- ✅ El intervalo se calcula desde la **última alerta enviada**, no desde `ready_at`

### 3. Performance
- ✅ El comando usa `with()` para eager loading (evitar N+1 queries)
- ✅ Índices en `order_realerts(order_id, sent_at)` para consultas rápidas
- ✅ Se ejecuta cada 5 minutos, balanceando velocidad y carga del servidor

### 4. Logs
- ✅ Todos los envíos se registran en `storage/logs/laravel.log`
- ✅ Logs del scheduler en `storage/logs/scheduled-realerts.log`
- ✅ Usar `tail -f` para monitoreo en tiempo real

---

## 🎨 Características del Diseño Volt

### Componentes Utilizados
1. **Cards con Shadow**: `card border-0 shadow`
2. **Form Switches**: Bootstrap 5 form-check-input
3. **Iconos SVG**: Heroicons inline
4. **Alerts Info**: `alert alert-info` con iconos
5. **Input Groups**: Para campos numéricos con unidades
6. **Colores Volt**:
   - Primary: `#4f46e5` (Indigo) - Módulo de Chat
   - Warning: `#f59e0b` (Amber) - Re-Alertas
   - Info: `#3b82f6` (Blue) - Mensajes informativos

### JavaScript Dinámico
```javascript
function toggleRealertFields() {
    const checkbox = document.getElementById('has_realerts');
    const fields = document.getElementById('realert-fields');

    if (checkbox.checked) {
        fields.style.display = 'block';
        // Make fields required
    } else {
        fields.style.display = 'none';
        // Remove required
    }
}
```

---

## ✅ Checklist de Implementación Completada

- [x] Migración de nuevos campos en tabla `plans`
- [x] Migración de nueva tabla `order_realerts`
- [x] Modelo `OrderRealert` con relaciones
- [x] Actualización de modelo `Plan` con nuevos campos
- [x] Actualización de modelo `Order` con relación `realerts()`
- [x] Vista `plans/create.blade.php` con UI Volt
- [x] Vista `plans/edit.blade.php` con UI Volt
- [x] JavaScript para mostrar/ocultar campos dinámicamente
- [x] Controlador `PlanManagementController` actualizado
- [x] Validaciones con mensajes en español
- [x] Lógica de actualización automática de `has_chat_module` en negocios
- [x] Servicio `PushNotificationService::sendReadyReminder()`
- [x] Comando `orders:send-realerts`
- [x] Tarea programada en `Kernel.php`
- [x] Pruebas de ejecución del comando
- [x] Documentación completa

---

## 🚀 Próximos Pasos (Opcionales)

### Mejoras Futuras
1. **Panel de Analíticas**:
   - Dashboard con estadísticas de re-alertas enviadas
   - Tasa de efectividad (¿cuántas alertas llevaron a que recogieran la orden?)

2. **Configuración por Negocio**:
   - Permitir que cada negocio personalice su propio intervalo de re-alertas
   - Override de la configuración del plan

3. **Tipos de Re-Alertas**:
   - SMS además de push notifications
   - Email como backup si push falla

4. **A/B Testing**:
   - Probar diferentes mensajes de re-alerta
   - Optimizar frecuencia y número de alertas

5. **Smart Re-Alerts**:
   - Machine learning para determinar el mejor momento de envío
   - Basado en histórico de recogida por hora/día

---

## 📞 Soporte

Para preguntas o problemas:
- Revisar logs: `storage/logs/laravel.log`
- Ejecutar comando manualmente: `php artisan orders:send-realerts`
- Verificar que el scheduler esté activo: `php artisan schedule:list`

---

**Fecha de Implementación**: 2025-11-19
**Versión**: 1.0
**Desarrollado con**: Laravel 12 + Volt Dashboard Template
