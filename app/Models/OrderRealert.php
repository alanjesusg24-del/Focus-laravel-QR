<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $casts = [
        'sent_at' => 'datetime',
        'was_delivered' => 'boolean',
        'alert_number' => 'integer',
    ];

    /**
     * Get the order that owns this re-alert
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
