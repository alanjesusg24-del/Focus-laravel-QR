<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use SoftDeletes;

    protected $table = 'chat_messages';
    protected $primaryKey = 'message_id';

    protected $fillable = [
        'order_id',
        'sender_type',
        'sender_id',
        'message',
        'attachment_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Relación con Business (cuando sender_type = 'business')
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'sender_id', 'business_id')
            ->where('sender_type', 'business');
    }

    /**
     * Relación con MobileUser (cuando sender_type = 'customer')
     */
    public function mobileUser()
    {
        return $this->belongsTo(MobileUser::class, 'sender_id', 'id')
            ->where('sender_type', 'customer');
    }

    /**
     * Scope para filtrar mensajes no leídos
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope para filtrar por orden
     */
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope para filtrar por tipo de remitente
     */
    public function scopeBySenderType($query, $type)
    {
        return $query->where('sender_type', $type);
    }

    /**
     * Marcar mensaje como leído
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
