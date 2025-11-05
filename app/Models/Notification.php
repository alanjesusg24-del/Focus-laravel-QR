<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';

    public $timestamps = false; // Esta tabla solo tiene sent_at

    protected $fillable = [
        'order_id',
        'mobile_user_id',
        'type',
        'title',
        'message',
        'sent_successfully',
    ];

    protected $casts = [
        'sent_successfully' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the order associated with this notification
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Scope to get successfully sent notifications
     */
    public function scopeSent($query)
    {
        return $query->where('sent_successfully', true);
    }

    /**
     * Scope to get failed notifications
     */
    public function scopeFailed($query)
    {
        return $query->where('sent_successfully', false);
    }

    /**
     * Scope to filter by notification type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
