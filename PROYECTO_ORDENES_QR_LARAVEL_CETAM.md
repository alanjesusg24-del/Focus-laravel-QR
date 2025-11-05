# Sistema de Gestión de Órdenes con QR - Especificación Laravel + CETAM

## INFORMACIÓN DEL PROYECTO

**Código del proyecto:** OQR  
**Slug del proyecto:** order-qr  
**Framework:** Laravel 12.0.0  
**Base de datos:** MySQL 8.0+ (Laragon + HeidiSQL)  
**Frontend:** Blade + TailwindCSS 4.1.0  
**Idioma de código:** Inglés (obligatorio según estándares CETAM)

---

## FASE 1: PLATAFORMA WEB (PRIORIDAD ACTUAL)

### Stack Tecnológico (Estándares CETAM Obligatorios)

**Backend:**
- Laravel Framework: 12.0.0
- PHP: 8.2.12
- Composer: 2.8.11
- MySQL: 8.0+ (via Laragon)

**Frontend:**
- Blade (incluido en Laravel)
- TailwindCSS: 4.1.0
- Node.js: 22.19.0
- NPM: 10.9.3
- AlpineJS para interactividad simple

**Herramientas:**
- Laragon (entorno local)
- HeidiSQL (administración BD)
- Visual Studio Code 1.90+
- Laravel Debugbar (desarrollo)

---

## ESTRUCTURA DEL PROYECTO LARAVEL

```
/order-qr-system
├── /app
│   ├── /Http
│   │   ├── /Controllers
│   │   │   ├── Auth/
│   │   │   ├── SuperAdmin/
│   │   │   └── Business/
│   │   ├── /Requests
│   │   │   ├── RegisterBusinessRequest.php
│   │   │   ├── CreateOrderRequest.php
│   │   │   └── ...
│   │   └── /Middleware
│   ├── /Models
│   │   ├── User.php
│   │   ├── Business.php
│   │   ├── Order.php
│   │   ├── Plan.php
│   │   └── ...
│   ├── /Services
│   │   ├── QrCodeService.php
│   │   ├── NotificationService.php
│   │   ├── PaymentService.php
│   │   └── ...
│   ├── /Repositories
│   │   ├── OrderRepository.php
│   │   ├── BusinessRepository.php
│   │   └── ...
│   └── /Exceptions
├── /database
│   ├── /migrations
│   ├── /seeders
│   └── /factories
├── /resources
│   ├── /views
│   │   ├── /layouts
│   │   │   └── app.blade.php
│   │   ├── /components
│   │   │   ├── alert.blade.php
│   │   │   ├── modal.blade.php
│   │   │   └── ...
│   │   ├── /partials
│   │   │   ├── header.blade.php
│   │   │   ├── sidebar.blade.php
│   │   │   └── footer.blade.php
│   │   └── /modules
│   │       ├── /orders
│   │       ├── /dashboard
│   │       └── /auth
│   ├── /css
│   │   └── app.css
│   └── /js
│       └── app.js
├── /routes
│   ├── web.php
│   ├── api.php
│   └── console.php
├── /public
│   ├── index.php
│   └── /storage (links)
├── /config
├── /tests
├── .env
└── tailwind.config.js
```

---

## MÓDULO 1: ESQUEMA DE BASE DE DATOS (MYSQL)

### 1.1 Estándares de Nomenclatura CETAM (OBLIGATORIO)

**Convenciones generales:**
- Idioma: Inglés (obligatorio)
- Formato: snake_case para todo
- Tablas: plural (users, orders, businesses)
- Claves primarias: singular de tabla + _id (user_id, order_id)
- Claves foráneas: igual a la PK referenciada (user_id, business_id)
- Booleanos: prefijo is_ o has_ (is_active, has_paid)
- Vistas: prefijo vw_ (vw_active_orders)
- Procedimientos: prefijo sp_ (sp_generate_report)
- Funciones: prefijo fn_ (fn_calculate_discount)
- Triggers: prefijo trg_ (trg_orders_before_insert)

### 1.2 Tablas del Sistema

**Tabla: super_admins**
```sql
CREATE TABLE super_admins (
    super_admin_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: plans**
```sql
CREATE TABLE plans (
    plan_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    duration_days INT NOT NULL,
    retention_days INT NOT NULL COMMENT 'Días que se guarda el historial',
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: businesses**
```sql
CREATE TABLE businesses (
    business_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(255) NOT NULL COMMENT 'Razón Social',
    rfc VARCHAR(13) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    plan_id INT UNSIGNED NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_payment_date TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    theme VARCHAR(50) DEFAULT 'professional' COMMENT 'professional o food',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (plan_id) REFERENCES plans(plan_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_rfc (rfc),
    INDEX idx_email (email),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: orders**
```sql
CREATE TABLE orders (
    order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    folio_number VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    qr_code_url TEXT COMMENT 'URL del QR para escanear',
    qr_token VARCHAR(100) UNIQUE NOT NULL COMMENT 'Token único para vinculación',
    pickup_token VARCHAR(100) UNIQUE NOT NULL COMMENT 'Token separado para recogida',
    status ENUM('pending', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
    mobile_user_id INT UNSIGNED COMMENT 'NULL hasta que se escanee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ready_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(business_id) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_business_status (business_id, status),
    INDEX idx_folio (folio_number),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: mobile_devices**
```sql
CREATE TABLE mobile_devices (
    mobile_device_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mobile_user_id INT UNSIGNED NOT NULL,
    fcm_token TEXT NOT NULL UNIQUE,
    platform ENUM('ios', 'android') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (mobile_user_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: notifications**
```sql
CREATE TABLE notifications (
    notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    mobile_user_id INT UNSIGNED NOT NULL,
    type ENUM('order_ready', 'order_cancelled', 'reminder') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    sent_successfully BOOLEAN DEFAULT FALSE,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: payments**
```sql
CREATE TABLE payments (
    payment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    plan_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    stripe_payment_id VARCHAR(255),
    stripe_subscription_id VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    next_payment_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(business_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(plan_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_business (business_id),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: support_tickets**
```sql
CREATE TABLE support_tickets (
    support_ticket_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id INT UNSIGNED NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open', 'in_progress', 'closed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    closed_at TIMESTAMP NULL,
    response TEXT NULL,
    FOREIGN KEY (business_id) REFERENCES businesses(business_id) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_business_status (business_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.3 Seeders de Datos Iniciales

**Planes de suscripción:**
```php
// database/seeders/PlanSeeder.php
Plan::insert([
    [
        'name' => 'Monthly Plan',
        'price' => 299.00,
        'duration_days' => 30,
        'retention_days' => 30,
        'description' => 'Basic monthly plan',
        'is_active' => true
    ],
    [
        'name' => 'Annual Plan',
        'price' => 2999.00,
        'duration_days' => 365,
        'retention_days' => 365,
        'description' => 'Annual plan with fiscal year retention',
        'is_active' => true
    ]
]);
```

---

## MÓDULO 2: MODELOS ELOQUENT (ESTÁNDARES CETAM)

### 2.1 Convenciones de Nomenclatura

- **Archivos:** PascalCase, singular (User.php, Business.php)
- **Namespace:** `App\Models`
- **Relaciones:** singular para hasOne/belongsTo, plural para hasMany/belongsToMany
- **Fillable:** definir todos los campos asignables masivamente
- **Casts:** especificar tipos de datos

### 2.2 Modelo Business

```php
<?php
// app/Models/Business.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $table = 'businesses';
    protected $primaryKey = 'business_id';

    protected $fillable = [
        'business_name',
        'rfc',
        'email',
        'password_hash',
        'phone',
        'address',
        'latitude',
        'longitude',
        'plan_id',
        'last_payment_date',
        'is_active',
        'theme'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'registration_date' => 'datetime',
        'last_payment_date' => 'datetime'
    ];

    protected $hidden = [
        'password_hash'
    ];

    // Relationships
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'plan_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'business_id', 'business_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'business_id', 'business_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'business_id', 'business_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithExpiredPayment($query)
    {
        return $query->whereRaw('DATE_ADD(last_payment_date, INTERVAL (SELECT duration_days FROM plans WHERE plan_id = businesses.plan_id) DAY) < NOW()');
    }
}
```

### 2.3 Modelo Order

```php
<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'business_id',
        'folio_number',
        'description',
        'qr_code_url',
        'qr_token',
        'pickup_token',
        'status',
        'mobile_user_id',
        'ready_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason'
    ];

    protected $casts = [
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    // Relationships
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'order_id', 'order_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'ready']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}
```

---

## MÓDULO 3: MIGRACIONES (FORMATO CETAM)

### 3.1 Ejemplo de Migración

```php
<?php
// database/migrations/2025_01_01_000001_create_businesses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id('business_id');
            $table->string('business_name', 255);
            $table->string('rfc', 13)->unique();
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('phone', 10);
            $table->text('address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedBigInteger('plan_id');
            $table->timestamp('registration_date')->useCurrent();
            $table->timestamp('last_payment_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('theme', 50)->default('professional');
            $table->timestamps();

            // Foreign keys
            $table->foreign('plan_id')
                  ->references('plan_id')
                  ->on('plans')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Indexes
            $table->index('rfc');
            $table->index('email');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
```

---

## MÓDULO 4: CONTROLADORES RESTful (ESTÁNDARES CETAM)

### 4.1 Convenciones de Nomenclatura

- **Archivos:** PascalCase + Controller (OrderController.php)
- **Namespace:** `App\Http\Controllers` + subdirectorio por módulo
- **Métodos:** RESTful (index, create, store, show, edit, update, destroy)
- **Máximo 300 líneas** por controlador
- **Lógica de negocio:** debe estar en Services, no en controladores

### 4.2 OrderController

```php
<?php
// app/Http/Controllers/Business/OrderController.php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Services\QrCodeService;
use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;
    private QrCodeService $qrCodeService;

    public function __construct(OrderService $orderService, QrCodeService $qrCodeService)
    {
        $this->orderService = $orderService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display a listing of active orders
     */
    public function index(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $orders = $this->orderService->getActiveOrders($businessId);

        return view('modules.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        return view('modules.orders.create');
    }

    /**
     * Store a newly created order in storage
     */
    public function store(CreateOrderRequest $request)
    {
        $businessId = auth()->user()->business_id;
        
        $order = $this->orderService->createOrder([
            'business_id' => $businessId,
            'description' => $request->input('description')
        ]);

        return redirect()
            ->route('order-qr.orders.show', $order->order_id)
            ->with('success', 'Order created successfully');
    }

    /**
     * Display the specified order with QR code
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        return view('modules.orders.show', compact('order'));
    }

    /**
     * Update order status to ready
     */
    public function markAsReady(Order $order)
    {
        $this->authorize('update', $order);
        
        $this->orderService->markOrderAsReady($order);

        return redirect()
            ->back()
            ->with('success', 'Order marked as ready. Notification sent.');
    }

    /**
     * Cancel the specified order
     */
    public function cancel(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        $this->orderService->cancelOrder($order, $request->input('reason'));

        return redirect()
            ->back()
            ->with('success', 'Order cancelled. Notification sent.');
    }

    /**
     * Confirm order pickup
     */
    public function confirmPickup(Request $request)
    {
        $token = $request->input('pickup_token');
        
        $result = $this->orderService->confirmPickup($token);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Order delivered successfully',
                'order' => $result['order']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }
}
```

---

## MÓDULO 5: SERVICIOS (LÓGICA DE NEGOCIO)

### 5.1 QrCodeService

```php
<?php
// app/Services/QrCodeService.php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate a unique QR token
     */
    public function generateToken(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Generate QR code image
     */
    public function generateQrCode(string $token, string $type = 'link'): string
    {
        $url = $this->buildQrUrl($token, $type);
        
        return QrCode::format('png')
                     ->size(300)
                     ->generate($url);
    }

    /**
     * Generate QR code URL for mobile app
     */
    public function buildQrUrl(string $token, string $type): string
    {
        $baseUrl = config('app.mobile_app_url');
        
        return match($type) {
            'link' => "{$baseUrl}/order/{$token}",
            'pickup' => "{$baseUrl}/pickup/{$token}",
            default => "{$baseUrl}/order/{$token}"
        };
    }

    /**
     * Save QR code to storage
     */
    public function saveQrCode(string $qrImage, string $filename): string
    {
        $path = "qr-codes/{$filename}.png";
        Storage::put($path, $qrImage);
        
        return Storage::url($path);
    }
}
```

### 5.2 OrderService

```php
<?php
// app/Services/OrderService.php

namespace App\Services;

use App\Models\Order;
use App\Models\Business;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class OrderService
{
    private QrCodeService $qrCodeService;
    private NotificationService $notificationService;

    public function __construct(
        QrCodeService $qrCodeService,
        NotificationService $notificationService
    ) {
        $this->qrCodeService = $qrCodeService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get active orders for a business
     */
    public function getActiveOrders(int $businessId): Collection
    {
        return Order::forBusiness($businessId)
                    ->active()
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Create a new order with QR codes
     */
    public function createOrder(array $data): Order
    {
        $business = Business::findOrFail($data['business_id']);
        
        // Generate folio number
        $folioNumber = $this->generateFolioNumber($business);
        
        // Generate tokens
        $qrToken = $this->qrCodeService->generateToken();
        $pickupToken = $this->qrCodeService->generateToken();
        
        // Create order
        $order = Order::create([
            'business_id' => $data['business_id'],
            'folio_number' => $folioNumber,
            'description' => $data['description'] ?? null,
            'qr_token' => $qrToken,
            'pickup_token' => $pickupToken,
            'status' => 'pending'
        ]);
        
        // Generate and save QR code
        $qrImage = $this->qrCodeService->generateQrCode($qrToken, 'link');
        $qrUrl = $this->qrCodeService->saveQrCode($qrImage, $folioNumber);
        
        $order->update(['qr_code_url' => $qrUrl]);
        
        return $order;
    }

    /**
     * Generate unique folio number
     */
    private function generateFolioNumber(Business $business): string
    {
        $prefix = strtoupper(substr($business->business_name, 0, 3));
        $date = Carbon::now()->format('Ymd');
        $sequence = Order::forBusiness($business->business_id)
                         ->whereDate('created_at', Carbon::today())
                         ->count() + 1;
        
        return sprintf('%s-%04d-%s', $prefix, $sequence, $date);
    }

    /**
     * Mark order as ready and send notification
     */
    public function markOrderAsReady(Order $order): void
    {
        $order->update([
            'status' => 'ready',
            'ready_at' => Carbon::now()
        ]);

        if ($order->mobile_user_id) {
            $this->notificationService->sendOrderReadyNotification($order);
        }
    }

    /**
     * Cancel order and send notification
     */
    public function cancelOrder(Order $order, ?string $reason): void
    {
        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
            'cancellation_reason' => $reason
        ]);

        if ($order->mobile_user_id) {
            $this->notificationService->sendOrderCancelledNotification($order);
        }
    }

    /**
     * Confirm order pickup with token validation
     */
    public function confirmPickup(string $pickupToken): array
    {
        $order = Order::where('pickup_token', $pickupToken)->first();

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Invalid pickup token'
            ];
        }

        if ($order->status !== 'ready') {
            return [
                'success' => false,
                'message' => 'Order is not ready for pickup'
            ];
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => Carbon::now()
        ]);

        return [
            'success' => true,
            'order' => $order
        ];
    }
}
```

---

## MÓDULO 6: FORM REQUESTS (VALIDACIÓN)

### 6.1 CreateOrderRequest

```php
<?php
// app/Http/Requests/CreateOrderRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'description' => 'nullable|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'description.max' => 'The description cannot exceed 500 characters.'
        ];
    }
}
```

### 6.2 RegisterBusinessRequest

```php
<?php
// app/Http/Requests/RegisterBusinessRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => 'required|string|min:3|max:255',
            'rfc' => 'required|string|size:13|unique:businesses,rfc|regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/',
            'email' => 'required|email|unique:businesses,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|size:10|regex:/^\d{10}$/',
            'address' => 'required|string|min:10|max:500',
            'plan_id' => 'required|exists:plans,plan_id',
            'theme' => 'required|in:professional,food',
            'terms_accepted' => 'required|accepted'
        ];
    }

    public function messages(): array
    {
        return [
            'rfc.regex' => 'The RFC format is invalid.',
            'phone.regex' => 'The phone must be 10 digits.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.'
        ];
    }
}
```

---

## MÓDULO 7: VISTAS BLADE (ESTÁNDARES CETAM)

### 7.1 Configuración TailwindCSS (tailwind.config.js)

```javascript
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                'institutional-blue': '#1d4976',
                'institutional-orange': '#de5629',
                'institutional-gray': '#7b96ab',
            },
            fontFamily: {
                sans: ['system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
```

### 7.2 Layout Principal (app.blade.php)

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Order QR System</title>
    @vite('resources/css/app.css')
</head>
<body class="font-sans antialiased bg-white">
    {{-- Navigation Bar --}}
    <nav class="bg-institutional-gray text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-xl font-bold">Logotipo</div>
            <div class="space-x-4">
                <a href="{{ route('order-qr.dashboard') }}" class="hover:bg-institutional-blue/80 px-4 py-2 rounded">Dashboard</a>
                <a href="{{ route('order-qr.orders.index') }}" class="hover:bg-institutional-blue/80 px-4 py-2 rounded">Orders</a>
                <form action="{{ route('order-qr.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="hover:bg-institutional-blue/80 px-4 py-2 rounded">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Main Layout with Sidebar --}}
    <div class="grid grid-cols-12 gap-2 p-4">
        {{-- Sidebar --}}
        <aside class="col-span-3 bg-institutional-blue text-white rounded-lg p-4">
            @include('partials.sidebar')
        </aside>

        {{-- Main Content --}}
        <main class="col-span-9 bg-white border-2 border-institutional-blue rounded-lg p-6">
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @if(session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
```

### 7.3 Vista de Órdenes Activas

```blade
{{-- resources/views/modules/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Active Orders')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-institutional-blue">Active Orders</h1>
        <a href="{{ route('order-qr.orders.create') }}" 
           class="flex items-center gap-2 px-4 py-2 text-base font-medium text-white bg-institutional-blue hover:bg-institutional-blue/80 rounded-full transition-transform duration-150 active:scale-95 focus:outline-none">
            + New Order
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-institutional-gray/20">
                    <th class="p-3 text-left">Folio</th>
                    <th class="p-3 text-left">Description</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Created</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-semibold">{{ $order->folio_number }}</td>
                        <td class="p-3">{{ Str::limit($order->description, 50) }}</td>
                        <td class="p-3">
                            <span class="px-3 py-1 rounded-full text-sm
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'ready') bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="p-3">{{ $order->created_at->diffForHumans() }}</td>
                        <td class="p-3 text-center space-x-2">
                            <a href="{{ route('order-qr.orders.show', $order) }}" 
                               class="text-institutional-blue hover:underline">View</a>
                            @if($order->status === 'pending')
                                <form action="{{ route('order-qr.orders.mark-ready', $order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-green-600 hover:underline">Mark Ready</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">No active orders</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
```

### 7.4 Componente de Alerta

```blade
{{-- resources/views/components/alert.blade.php --}}
@props(['type' => 'success', 'message'])

@php
    $colors = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700'
    ];
    $colorClass = $colors[$type] ?? $colors['info'];
@endphp

<div class="border-2 {{ $colorClass }} px-4 py-3 rounded relative mb-4" role="alert">
    <span class="block sm:inline">{{ $message }}</span>
</div>
```

---

## MÓDULO 8: RUTAS (ESTÁNDARES CETAM)

### 8.1 web.php

```php
<?php
// routes/web.php

use App\Http\Controllers\Business\OrderController;
use App\Http\Controllers\Business\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Prefijo del proyecto según estándares CETAM
Route::prefix('/p/order-qr')->name('order-qr.')->group(function () {
    
    // Public routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Protected routes
    Route::middleware(['auth:business'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Orders resource
        Route::resource('orders', OrderController::class);
        Route::put('/orders/{order}/mark-ready', [OrderController::class, 'markAsReady'])
             ->name('orders.mark-ready');
        Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])
             ->name('orders.cancel');
    });
});

// API for mobile app
Route::prefix('/api/v1/order-qr')->name('api.order-qr.')->group(function () {
    Route::post('/pickup/confirm', [OrderController::class, 'confirmPickup'])
         ->name('pickup.confirm');
});
```

---

## MÓDULO 9: INTEGRACIÓN CON STRIPE

### 9.1 PaymentService

```php
<?php
// app/Services/PaymentService.php

namespace App\Services;

use App\Models\Business;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create Stripe checkout session
     */
    public function createCheckoutSession(Business $business, int $planId): string
    {
        $plan = Plan::findOrFail($planId);
        
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mxn',
                    'product_data' => [
                        'name' => $plan->name,
                    ],
                    'unit_amount' => $plan->price * 100, // Convert to cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('order-qr.payment.success'),
            'cancel_url' => route('order-qr.payment.cancel'),
            'client_reference_id' => $business->business_id,
        ]);

        return $session->url;
    }

    /**
     * Handle successful payment
     */
    public function handleSuccessfulPayment(array $data): void
    {
        Payment::create([
            'business_id' => $data['business_id'],
            'plan_id' => $data['plan_id'],
            'amount' => $data['amount'],
            'stripe_payment_id' => $data['stripe_payment_id'],
            'status' => 'completed',
            'payment_date' => now(),
            'next_payment_date' => now()->addDays($data['duration_days'])
        ]);

        // Update business payment date
        Business::where('business_id', $data['business_id'])
                ->update(['last_payment_date' => now()]);
    }
}
```

---

## MÓDULO 10: COMANDOS ARTISAN Y CRON JOBS

### 10.1 Comando para limpieza de historial

```php
<?php
// app/Console/Commands/CleanExpiredOrders.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Models\Order;
use Carbon\Carbon;

class CleanExpiredOrders extends Command
{
    protected $signature = 'orders:clean-expired';
    protected $description = 'Clean expired orders based on business plan';

    public function handle()
    {
        $businesses = Business::with('plan')->get();

        foreach ($businesses as $business) {
            $retentionDays = $business->plan->retention_days;
            $cutoffDate = Carbon::now()->subDays($retentionDays);

            $deleted = Order::forBusiness($business->business_id)
                           ->where('created_at', '<', $cutoffDate)
                           ->delete();

            $this->info("Business {$business->business_name}: {$deleted} orders cleaned");
        }

        return Command::SUCCESS;
    }
}
```

### 10.2 Programación en Kernel

```php
<?php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Clean expired orders daily at 2:00 AM
    $schedule->command('orders:clean-expired')
             ->dailyAt('02:00');
    
    // Check for expired payments daily
    $schedule->command('businesses:check-payments')
             ->dailyAt('08:00');
}
```

---

## INSTRUCCIONES DE INSTALACIÓN Y CONFIGURACIÓN

### 1. Instalación de Laravel

```bash
# Crear nuevo proyecto Laravel 12
composer create-project laravel/laravel order-qr-system "12.0.*"
cd order-qr-system

# Instalar dependencias adicionales
composer require simplesoftwareio/simple-qrcode
composer require stripe/stripe-php
composer require laravel/sanctum
```

### 2. Configuración de Base de Datos (.env)

```env
# Database (MySQL via Laragon)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=order_qr_system
DB_USERNAME=root
DB_PASSWORD=

# App
APP_NAME="Order QR System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://order-qr.test

# Stripe
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...

# Mobile App URL
MOBILE_APP_URL=https://app.example.com

# Firebase
FIREBASE_SERVER_KEY=...
```

### 3. Configuración de TailwindCSS

```bash
# Instalar TailwindCSS
npm install -D tailwindcss@4.1.0 postcss autoprefixer
npx tailwindcss init -p

# Compilar assets
npm run dev
```

### 4. Crear Base de Datos en HeidiSQL

1. Abrir HeidiSQL
2. Conectar a MySQL (Laragon)
3. Crear nueva base de datos: `order_qr_system`
4. Establecer charset: `utf8mb4_unicode_ci`

### 5. Ejecutar Migraciones y Seeders

```bash
php artisan migrate
php artisan db:seed --class=PlanSeeder
```

### 6. Generar Key y Storage Link

```bash
php artisan key:generate
php artisan storage:link
```

---

## ESTÁNDARES DE CÓDIGO A SEGUIR

### ✅ Nomenclatura Obligatoria

**Base de Datos:**
- Inglés, snake_case
- Tablas en plural
- PK: `table_id`
- FK: igual a PK referenciada
- Booleanos: `is_`, `has_`

**PHP/Laravel:**
- Clases: PascalCase
- Métodos/funciones: camelCase
- Variables: camelCase
- Constantes: UPPER_SNAKE_CASE
- Colecciones: plural

**Blade:**
- Archivos: kebab-case
- Componentes: `<x-order-qr-component-name />`

**Rutas:**
- URL: `/p/order-qr/...`
- Nombres: `order-qr.module.action`

### ✅ Límites de Código

- **Líneas por archivo:** máximo 120 caracteres
- **Controladores:** máximo 300 líneas
- **Funciones:** máximo 40 líneas
- **Parámetros:** máximo 3 (usar DTO si se necesitan más)
- **Anidamiento:** máximo 3 niveles

### ✅ Estructura de Archivos

- Controladores en `/app/Http/Controllers`
- Servicios en `/app/Services`
- Repositorios en `/app/Repositories`
- Requests en `/app/Http/Requests`
- Modelos en `/app/Models`

### ✅ Paleta de Colores CETAM

- **Azul institucional:** `#1d4976` → `bg-institutional-blue`
- **Naranja:** `#de5629` → `bg-institutional-orange`
- **Gris:** `#7b96ab` → `bg-institutional-gray`
- **Fondo:** `#ffffff` (blanco)

### ✅ Componentes de Botones

```html
<!-- Botón primario -->
<button class="px-4 py-2 text-base font-medium text-white bg-institutional-blue hover:bg-institutional-blue/80 rounded-full transition-transform duration-150 active:scale-95 focus:outline-none">
    Primary Button
</button>

<!-- Botón secundario -->
<button class="px-4 py-2 text-base font-medium text-white bg-institutional-gray hover:bg-institutional-gray/80 rounded-full transition-transform duration-150 active:scale-95 focus:outline-none">
    Secondary Button
</button>

<!-- Botón destructivo -->
<button class="px-4 py-2 text-base font-medium text-institutional-orange bg-transparent border-2 border-institutional-orange hover:bg-institutional-orange/20 rounded-full transition-transform duration-150 active:scale-95 focus:outline-none">
    Delete
</button>
```

---

## ORDEN DE IMPLEMENTACIÓN SUGERIDO

### Sprint 1 (Semana 1-2): Fundación
1. ✅ Configurar proyecto Laravel
2. ✅ Crear esquema completo de BD con migraciones
3. ✅ Crear todos los modelos Eloquent
4. ✅ Configurar TailwindCSS con colores CETAM
5. ✅ Crear layout base y componentes

### Sprint 2 (Semana 3-4): Autenticación y Órdenes
1. ✅ Sistema de autenticación para negocios
2. ✅ CRUD de órdenes
3. ✅ Servicio de generación de QR
4. ✅ Panel de órdenes activas
5. ✅ Cambio de estados de órdenes

### Sprint 3 (Semana 5): Notificaciones
1. ✅ Integrar Firebase Cloud Messaging
2. ✅ NotificationService
3. ✅ Confirmar entrega con escaneo de QR

### Sprint 4 (Semana 6): Reportes y Admin
1. ✅ Dashboard con estadísticas
2. ✅ Reportes con filtros de fecha
3. ✅ Exportación a Excel/PDF
4. ✅ Panel de super administrador

### Sprint 5 (Semana 7): Pagos y Soporte
1. ✅ Integración con Stripe
2. ✅ Control de acceso por pagos
3. ✅ Sistema de tickets de soporte
4. ✅ Comando de limpieza de historial

### Sprint 6 (Semana 8): Testing y Deploy
1. ✅ Pruebas unitarias
2. ✅ Pruebas de integración
3. ✅ Optimización de rendimiento
4. ✅ Deploy a producción

---

## PARA CLAUDE CODE

**Instrucciones de inicio:**

```
Hola Claude Code, voy a desarrollar un sistema de gestión de órdenes con QR.
Tengo la especificación completa en PROYECTO_ORDENES_QR_LARAVEL_CETAM.md.

Este proyecto debe seguir ESTRICTAMENTE los estándares del Centro de Desarrollo 
Tecnológico Aplicado de México (CETAM) que están documentados en:
- Manual de Bases de Datos CETAM
- Manual de Programación Laravel CETAM

Por favor:
1. Lee el archivo PROYECTO_ORDENES_QR_LARAVEL_CETAM.md completo
2. Confirma que entiendes:
   - El stack: Laravel 12 + MySQL + Blade + TailwindCSS
   - Los estándares de nomenclatura (inglés, snake_case en BD)
   - La paleta de colores CETAM
   - La estructura MVC obligatoria
   - Los 10 módulos del proyecto

3. Comienza con Sprint 1:
   - Crear proyecto Laravel 12
   - Configurar .env para MySQL (Laragon)
   - Crear TODAS las migraciones según el esquema
   - Configurar TailwindCSS con colores CETAM
   - Crear estructura de carpetas

NO avances al siguiente paso sin mi confirmación.
Pregunta si tienes dudas sobre los estándares CETAM.
```

---

## NOTAS FINALES

1. **Todos los nombres de BD, tablas, columnas deben estar en INGLÉS**
2. **Seguir estrictamente los estándares CETAM** (snake_case, PascalCase según corresponda)
3. **Usar HeidiSQL** para visualizar y administrar la BD
4. **Usar Laragon** como entorno local
5. **Paleta CETAM** es obligatoria: azul (#1d4976), naranja (#de5629), gris (#7b96ab)
6. **Máximo 300 líneas** por controlador
7. **Lógica de negocio** debe estar en Services
8. **Form Requests** para toda validación
9. **Blade + TailwindCSS** para vistas (no usar otros frameworks)
10. **Documentar todo** con PHPDoc

---

**Elaborado por:** Sistema CETAM  
**Fecha:** 2025  
**Versión:** 1.0 - Laravel Edition
