<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Authenticatable
{
    protected $table = 'businesses';
    protected $primaryKey = 'business_id';

    protected $fillable = [
        'business_name',
        'rfc',
        'email',
        'password',
        'phone',
        'address',
        'latitude',
        'longitude',
        'plan_id',
        'registration_date',
        'last_payment_date',
        'is_active',
        'theme',
        'logo_url',
        'has_chat_module',
        'data_retention_months',
        'monthly_price',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_chat_module' => 'boolean',
        'data_retention_months' => 'integer',
        'monthly_price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'registration_date' => 'datetime',
        'last_payment_date' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the plan associated with this business
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'plan_id');
    }

    /**
     * Get all orders for this business
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'business_id', 'business_id');
    }

    /**
     * Get all payments for this business
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'business_id', 'business_id');
    }

    /**
     * Get all support tickets for this business
     */
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'business_id', 'business_id');
    }

    /**
     * Scope to get only active businesses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get businesses with expired payment
     */
    public function scopeWithExpiredPayment($query)
    {
        return $query->whereRaw(
            'DATE_ADD(last_payment_date, INTERVAL (SELECT duration_days FROM plans WHERE plan_id = businesses.plan_id) DAY) < NOW()'
        );
    }
}
