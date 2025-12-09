<?php

/**
 * ============================================
 * CETAM - Payment Model
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        Payment.php
 * @description Modelo de pagos y suscripciones
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * @table       payments
 * @primaryKey  payment_id
 *
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    public $timestamps = false; // Solo tiene created_at

    const UPDATED_AT = null;

    protected $fillable = [
        'business_id',
        'plan_id',
        'amount',
        'stripe_payment_id',
        'stripe_subscription_id',
        'mercadopago_preference_id',
        'payment_provider',
        'status',
        'payment_date',
        'next_payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'next_payment_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Get the business associated with this payment
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }

    /**
     * Get the plan associated with this payment
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'plan_id');
    }

    /**
     * Scope to get completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to filter by business
     */
    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}
