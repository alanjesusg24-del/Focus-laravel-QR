<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\PushNotificationService;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'business_id',
        'order_number',
        'folio_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'description',
        'qr_code_url',
        'qr_token',
        'pickup_token',
        'status',
        'mobile_user_id',
        'total_amount',
        'associated_at',
        'ready_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'associated_at' => 'datetime',
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the business that owns this order
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }

    /**
     * Get all notifications for this order
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'order_id', 'order_id');
    }

    /**
     * Scope to get active orders (pending or ready)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'ready']);
    }

    /**
     * Scope to get pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get ready orders
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope to get delivered orders
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope to get cancelled orders
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to filter orders by business
     */
    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Get the mobile user that owns this order
     */
    public function mobileUser(): BelongsTo
    {
        return $this->belongsTo(MobileUser::class);
    }

    /**
     * Get the items for the order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Get the status history for the order
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'order_id');
    }

    /**
     * Generate QR token when creating order and listen for status changes
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->qr_token) {
                $order->qr_token = \Illuminate\Support\Str::random(32);
            }
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . date('Y') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        // Escuchar cambios en el modelo después de actualizar
        static::updated(function ($order) {
            // Verificar si cambió el status
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;

                // Obtener el token FCM del usuario móvil
                $mobileUser = $order->mobileUser;

                if ($mobileUser && $mobileUser->fcm_token) {
                    // Enviar notificación push
                    PushNotificationService::sendOrderStatusChange(
                        $mobileUser->fcm_token,
                        $order,
                        $oldStatus,
                        $newStatus
                    );
                }
            }
        });
    }
}
