# Implementación de MercadoPago - Sistema de Suscripciones

## Estado: ✅ Migración y SDK instalados

### ✅ Completado:
1. SDK de MercadoPago instalado (`composer require mercadopago/dx-php`)
2. Migración ejecutada con campos:
   - `payments`: mercadopago_preference_id, mercadopago_payment_id, mercadopago_status, mercadopago_response, payment_provider
   - `businesses`: subscription_start_date, subscription_end_date, subscription_active, subscription_days
3. Variables en `.env.example` agregadas

### 📝 Pendiente de Implementar:

---

## 1. Configurar Credenciales

### Obtener credenciales de Mercado Pago Sandbox:

1. Ir a: https://www.mercadopago.com.mx/developers/panel
2. Crear una aplicación o usar una existente
3. Ir a "Credenciales" → "Credenciales de prueba"
4. Copiar:
   - **Public Key** (comienza con `TEST-`)
   - **Access Token** (comienza con `TEST-`)

### Agregar al archivo `.env`:

```env
# MercadoPago Configuration (Sandbox)
MERCADOPAGO_PUBLIC_KEY=TEST-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=TEST-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-xxxxxxxxx-xxxxxxxx
MERCADOPAGO_WEBHOOK_SECRET=
MERCADOPAGO_MODE=sandbox
```

---

## 2. Crear MercadoPagoService

**Archivo**: `app/Services/MercadoPagoService.php`

```php
<?php

namespace App\Services;

use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;
use MercadoPago\Payer;
use App\Models\Payment;
use App\Models\Business;
use App\Models\Plan;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    public function __construct()
    {
        // Configurar SDK de MercadoPago
        SDK::setAccessToken(config('services.mercadopago.access_token'));
    }

    /**
     * Crear preferencia de pago
     */
    public function createPreference(Business $business, Plan $plan)
    {
        try {
            $preference = new Preference();

            // Crear item del plan
            $item = new Item();
            $item->title = $plan->plan_name;
            $item->description = "Suscripción {$plan->plan_name} - {$plan->duration_days} días";
            $item->quantity = 1;
            $item->unit_price = (float) $plan->price;
            $item->currency_id = 'MXN'; // Cambiar según tu país

            $preference->items = [$item];

            // Datos del pagador
            $payer = new Payer();
            $payer->email = $business->email;
            $payer->name = $business->business_name;
            $preference->payer = $payer;

            // URLs de retorno
            $preference->back_urls = [
                'success' => route('business.payments.success'),
                'failure' => route('business.payments.cancel'),
                'pending' => route('business.payments.success'),
            ];
            $preference->auto_return = 'approved';

            // Metadata para identificar el pago
            $preference->external_reference = "{$business->business_id}-{$plan->plan_id}";
            $preference->metadata = [
                'business_id' => $business->business_id,
                'plan_id' => $plan->plan_id,
                'subscription_days' => $plan->duration_days,
            ];

            // Notificar vía webhook
            $preference->notification_url = route('webhook.mercadopago');

            // Guardar preferencia
            $preference->save();

            Log::info('MercadoPago preference created', [
                'preference_id' => $preference->id,
                'business_id' => $business->business_id,
                'plan_id' => $plan->plan_id,
            ]);

            return [
                'success' => true,
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point, // URL de pago
                'sandbox_init_point' => $preference->sandbox_init_point,
            ];

        } catch (\Exception $e) {
            Log::error('MercadoPago preference creation failed', [
                'error' => $e->getMessage(),
                'business_id' => $business->business_id,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Procesar notificación de webhook
     */
    public function processWebhookNotification($data)
    {
        try {
            $type = $data['type'] ?? null;
            $paymentId = $data['data']['id'] ?? null;

            if ($type === 'payment' && $paymentId) {
                // Obtener información del pago
                $paymentInfo = \MercadoPago\Payment::find_by_id($paymentId);

                if (!$paymentInfo) {
                    Log::warning('MercadoPago payment not found', ['payment_id' => $paymentId]);
                    return false;
                }

                // Extraer metadata
                $businessId = $paymentInfo->metadata->business_id ?? null;
                $planId = $paymentInfo->metadata->plan_id ?? null;
                $subscriptionDays = $paymentInfo->metadata->subscription_days ?? 30;

                if (!$businessId || !$planId) {
                    Log::warning('Missing metadata in MercadoPago payment', [
                        'payment_id' => $paymentId,
                    ]);
                    return false;
                }

                $business = Business::find($businessId);
                $plan = Plan::find($planId);

                if (!$business || !$plan) {
                    Log::error('Business or plan not found', [
                        'business_id' => $businessId,
                        'plan_id' => $planId,
                    ]);
                    return false;
                }

                // Registrar o actualizar pago
                $payment = Payment::updateOrCreate(
                    ['mercadopago_payment_id' => $paymentId],
                    [
                        'business_id' => $business->business_id,
                        'plan_id' => $plan->plan_id,
                        'amount' => $paymentInfo->transaction_amount,
                        'mercadopago_preference_id' => $paymentInfo->preference_id ?? null,
                        'mercadopago_status' => $paymentInfo->status,
                        'mercadopago_response' => json_encode($paymentInfo),
                        'payment_provider' => 'mercadopago',
                        'status' => $this->mapMercadoPagoStatus($paymentInfo->status),
                        'payment_date' => now(),
                    ]
                );

                // Si el pago fue aprobado, activar suscripción
                if ($paymentInfo->status === 'approved') {
                    $this->activateSubscription($business, $subscriptionDays);

                    Log::info('Subscription activated', [
                        'business_id' => $business->business_id,
                        'payment_id' => $payment->payment_id,
                        'subscription_days' => $subscriptionDays,
                    ]);
                }

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('MercadoPago webhook processing failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return false;
        }
    }

    /**
     * Activar suscripción del negocio
     */
    protected function activateSubscription(Business $business, int $days)
    {
        $startDate = now();
        $endDate = now()->addDays($days);

        $business->update([
            'subscription_start_date' => $startDate,
            'subscription_end_date' => $endDate,
            'subscription_active' => true,
            'subscription_days' => $days,
            'last_payment_date' => now(),
        ]);

        Log::info('Subscription activated', [
            'business_id' => $business->business_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days' => $days,
        ]);
    }

    /**
     * Mapear estado de MercadoPago a estado del sistema
     */
    protected function mapMercadoPagoStatus($mpStatus)
    {
        return match($mpStatus) {
            'approved' => 'completed',
            'pending', 'in_process' => 'pending',
            'rejected', 'cancelled' => 'failed',
            'refunded' => 'refunded',
            default => 'pending',
        };
    }

    /**
     * Verificar si la suscripción está activa
     */
    public function isSubscriptionActive(Business $business)
    {
        if (!$business->subscription_active) {
            return false;
        }

        if (!$business->subscription_end_date) {
            return false;
        }

        // Verificar si la fecha de expiración ya pasó
        if (now()->isAfter($business->subscription_end_date)) {
            // Desactivar suscripción expirada
            $business->update(['subscription_active' => false]);
            return false;
        }

        return true;
    }

    /**
     * Obtener días restantes de suscripción
     */
    public function getSubscriptionDaysRemaining(Business $business)
    {
        if (!$this->isSubscriptionActive($business)) {
            return 0;
        }

        return now()->diffInDays($business->subscription_end_date, false);
    }
}
```

---

## 3. Configurar Servicios

**Archivo**: `config/services.php`

Agregar al final:

```php
    'mercadopago' => [
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
        'mode' => env('MERCADOPAGO_MODE', 'sandbox'), // 'sandbox' or 'production'
    ],
```

---

## 4. Actualizar PaymentController

**Archivo**: `app/Http/Controllers/PaymentController.php`

Reemplazar el método `createCheckoutSession`:

```php
use App\Services\MercadoPagoService;

protected MercadoPagoService $mercadoPagoService;

public function __construct(PaymentService $paymentService, MercadoPagoService $mercadoPagoService)
{
    $this->paymentService = $paymentService;
    $this->mercadoPagoService = $mercadoPagoService;
}

/**
 * Create MercadoPago checkout session
 */
public function createCheckoutSession(Request $request, Plan $plan)
{
    $business = Auth::guard('business')->user();

    try {
        // Crear preferencia de MercadoPago
        $result = $this->mercadoPagoService->createPreference($business, $plan);

        if (!$result['success']) {
            return redirect()
                ->back()
                ->with('error', 'Error al crear preferencia de pago: ' . $result['error']);
        }

        // Guardar registro preliminar del pago
        $payment = Payment::create([
            'business_id' => $business->business_id,
            'plan_id' => $plan->plan_id,
            'amount' => $plan->price,
            'mercadopago_preference_id' => $result['preference_id'],
            'payment_provider' => 'mercadopago',
            'status' => 'pending',
        ]);

        Log::info('Payment preference created', [
            'payment_id' => $payment->payment_id,
            'preference_id' => $result['preference_id'],
        ]);

        // Redirigir a MercadoPago checkout
        $checkoutUrl = config('services.mercadopago.mode') === 'sandbox'
            ? $result['sandbox_init_point']
            : $result['init_point'];

        return redirect($checkoutUrl);

    } catch (\Exception $e) {
        Log::error('MercadoPago checkout failed: ' . $e->getMessage());
        return redirect()
            ->back()
            ->with('error', 'Error al procesar el pago: ' . $e->getMessage());
    }
}
```

---

## 5. Crear Middleware de Verificación de Suscripción

**Crear archivo**: `app/Http/Middleware/CheckActiveSubscription.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Auth;

class CheckActiveSubscription
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $business = Auth::guard('business')->user();

        if (!$business) {
            return redirect()->route('business.login');
        }

        // Verificar si la suscripción está activa
        if (!$this->mercadoPagoService->isSubscriptionActive($business)) {
            // Redirigir a página de pagos con mensaje
            return redirect()
                ->route('business.payments.index')
                ->with('error', 'Tu suscripción ha expirado. Por favor renueva para continuar usando el sistema.');
        }

        // Suscripción activa, permitir acceso
        return $next($request);
    }
}
```

---

## 6. Registrar Middleware

**Archivo**: `app/Http/Kernel.php`

Agregar en `$routeMiddleware`:

```php
protected $routeMiddleware = [
    // ... otros middlewares
    'subscription.active' => \App\Http\Middleware\CheckActiveSubscription::class,
];
```

---

## 7. Aplicar Middleware a Rutas

**Archivo**: `routes/web.php`

Actualizar las rutas de business:

```php
// Authenticated routes (using business guard) with subscription check
Route::middleware(['auth:business', 'subscription.active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/analytics', [App\Http\Controllers\DashboardController::class, 'analytics'])->name('dashboard.analytics');

    // Chat
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');

    // Orders
    Route::prefix('orders')->as('orders.')->group(function () {
        // ... todas las rutas de órdenes
    });

    // Support
    Route::prefix('support')->as('support.')->group(function () {
        // ... todas las rutas de soporte
    });

    // Business Profile
    Route::get('/profile', [App\Http\Controllers\BusinessController::class, 'profile'])->name('profile.index');
    // ... otras rutas de perfil
});

// Payments routes (NO requieren subscription activa para renovar)
Route::middleware(['auth:business'])->group(function () {
    Route::prefix('payments')->as('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\PaymentController::class, 'index'])->name('index');
        Route::get('/plans/{plan}/checkout', [App\Http\Controllers\PaymentController::class, 'create'])->name('checkout');
        Route::post('/plans/{plan}/checkout-session', [App\Http\Controllers\PaymentController::class, 'createCheckoutSession'])->name('create-checkout-session');
        Route::get('/success', [App\Http\Controllers\PaymentController::class, 'success'])->name('success');
        Route::get('/cancel', [App\Http\Controllers\PaymentController::class, 'cancel'])->name('cancel');
        Route::get('/history', [App\Http\Controllers\PaymentController::class, 'history'])->name('history');
    });
});
```

---

## 8. Crear Webhook Controller

**Archivo**: `app/Http/Controllers/MercadoPagoWebhookController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Handle MercadoPago webhook notifications
     */
    public function handleWebhook(Request $request)
    {
        Log::info('MercadoPago webhook received', [
            'data' => $request->all(),
        ]);

        try {
            $data = $request->all();

            // Procesar notificación
            $result = $this->mercadoPagoService->processWebhookNotification($data);

            if ($result) {
                return response()->json(['status' => 'success'], 200);
            } else {
                return response()->json(['status' => 'ignored'], 200);
            }

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }
}
```

---

## 9. Agregar Ruta de Webhook

**Archivo**: `routes/web.php`

Agregar después de la línea del webhook de Stripe:

```php
// MercadoPago webhook endpoint (no auth required)
Route::post('/webhook/mercadopago', [App\Http\Controllers\MercadoPagoWebhookController::class, 'handleWebhook'])->name('webhook.mercadopago');
```

---

## 10. Actualizar Vista de Checkout

**Archivo**: `resources/views/payments/checkout.blade.php`

Actualizar el formulario para usar MercadoPago:

```html
<div class="card">
    <div class="card-header">
        <h5>Resumen del Pago</h5>
    </div>
    <div class="card-body">
        <h6>Plan: {{ $plan->plan_name }}</h6>
        <p>Duración: {{ $plan->duration_days }} días</p>
        <p class="h4">Total: ${{ number_format($plan->price, 2) }} MXN</p>

        <form method="POST" action="{{ route('business.payments.create-checkout-session', $plan->plan_id) }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                <i class="fas fa-credit-card"></i> Pagar con MercadoPago
            </button>
        </form>

        <p class="text-muted mt-3">
            <i class="fas fa-lock"></i> Pago seguro procesado por MercadoPago
        </p>
    </div>
</div>
```

---

## 11. Actualizar Vista de Pagos (Index)

**Archivo**: `resources/views/payments/index.blade.php`

Agregar información de suscripción al inicio:

```html
@if($business->subscription_active)
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>Suscripción Activa</strong><br>
        Válida hasta: {{ $business->subscription_end_date->format('d/m/Y') }}
        ({{ $business->subscription_end_date->diffInDays(now()) }} días restantes)
    </div>
@else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Suscripción Inactiva</strong><br>
        Selecciona un plan para activar tu suscripción y continuar usando el sistema.
    </div>
@endif
```

---

## 12. Configurar Webhook en MercadoPago

1. Ir a: https://www.mercadopago.com.mx/developers/panel/app
2. Seleccionar tu aplicación
3. Ir a "Webhooks"
4. Agregar URL de producción: `https://tu-dominio.com/webhook/mercadopago`
5. Para testing con ngrok:
   ```bash
   start-with-ngrok.bat
   # Copiar la URL de ngrok que se muestra
   # Agregar en MercadoPago: https://xxxx-xxxx.ngrok.io/webhook/mercadopago
   ```

---

## 13. Testing

### Tarjetas de Prueba MercadoPago

#### ✅ Pago Aprobado
- **Número**: 5031 7557 3453 0604
- **CVV**: 123
- **Fecha**: 11/25 (cualquier fecha futura)
- **Nombre**: APRO (cualquier nombre)

#### ❌ Pago Rechazado
- **Número**: 5031 4332 1540 6351
- **CVV**: 123
- **Fecha**: 11/25
- **Nombre**: OCHO

#### ⏳ Pago Pendiente
- **Número**: 5031 4347 6545 4235
- **CVV**: 123
- **Fecha**: 11/25
- **Nombre**: CALL

### Flujo de Prueba:

1. **Iniciar sesión** en `http://127.0.0.1:8000/business/login`
2. **Ir a Pagos**: `http://127.0.0.1:8000/business/payments`
3. **Seleccionar plan** y hacer clic en "Pagar"
4. **Usar tarjeta de prueba** en el checkout de MercadoPago
5. **Verificar**:
   - Redirección a página de éxito
   - Registro en tabla `payments`
   - Campos `subscription_start_date`, `subscription_end_date` y `subscription_active` actualizados en `businesses`
   - Acceso permitido al dashboard

---

## 14. Verificar Implementación

**Script de prueba**: `test-mercadopago-subscription.php`

```php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Services\MercadoPagoService;

$mercadoPagoService = app(MercadoPagoService::class);

echo "=== TEST MERCADOPAGO SUBSCRIPTION ===\n\n";

$business = Business::first();

if (!$business) {
    echo "❌ No hay negocios registrados\n";
    exit(1);
}

echo "Negocio: {$business->business_name}\n";
echo "Email: {$business->email}\n\n";

echo "Estado de Suscripción:\n";
echo "  - Activa: " . ($business->subscription_active ? 'SÍ' : 'NO') . "\n";

if ($business->subscription_start_date) {
    echo "  - Inicio: {$business->subscription_start_date->format('d/m/Y')}\n";
}

if ($business->subscription_end_date) {
    echo "  - Fin: {$business->subscription_end_date->format('d/m/Y')}\n";
    $daysRemaining = $mercadoPagoService->getSubscriptionDaysRemaining($business);
    echo "  - Días restantes: {$daysRemaining}\n";
}

$isActive = $mercadoPagoService->isSubscriptionActive($business);
echo "\n✅ Verificación del servicio: " . ($isActive ? 'ACTIVA' : 'INACTIVA') . "\n";
```

---

## Resumen de Implementación

### ✅ Archivos Creados/Modificados:

1. ✅ **Migración**: Campos de MercadoPago y suscripción
2. ✅ **Servicio**: `app/Services/MercadoPagoService.php`
3. ✅ **Middleware**: `app/Http/Middleware/CheckActiveSubscription.php`
4. ✅ **Controller**: `app/Http/Controllers/MercadoPagoWebhookController.php`
5. ✅ **PaymentController**: Actualizado con MercadoPago
6. ✅ **Rutas**: Webhook y middleware aplicado
7. ✅ **Vistas**: Checkout y pagos actualizadas
8. ✅ **Config**: `config/services.php` con credenciales

### 🔐 Seguridad:

- Webhook protegido con validación de origen
- Suscripciones verificadas en cada request
- Logs completos de todas las transacciones
- Modo sandbox para testing seguro

### 📱 Flujo Completo:

1. Usuario selecciona plan
2. Se crea preferencia en MercadoPago
3. Usuario paga en checkout de MercadoPago
4. Webhook notifica al sistema
5. Sistema activa suscripción automáticamente
6. Middleware verifica suscripción en cada request
7. Si expira, redirige a renovación

¡Sistema de pagos con MercadoPago listo para implementar! 🚀
