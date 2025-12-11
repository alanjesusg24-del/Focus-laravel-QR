<?php

/**
 * ============================================
 * CETAM - Business Model
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        Business.php
 * @description Modelo de negocio con autenticación y suscripciones
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

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
        'address_details',
        'city',
        'state',
        'postal_code',
        'photo',
        'latitude',
        'longitude',
        'location_description',
        'is_location_public',
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
        'is_location_public' => 'boolean',
        'data_retention_months' => 'integer',
        'monthly_price' => 'decimal:2',
        'latitude' => 'float',
        'longitude' => 'float',
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

    /**
     * Scope to get businesses with public location
     */
    public function scopeWithPublicLocation($query)
    {
        return $query->where('is_location_public', true)
                     ->whereNotNull('latitude')
                     ->whereNotNull('longitude');
    }

    /**
     * Scope to find businesses near a specific location
     * Uses Haversine formula to calculate distance
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $latitude User latitude
     * @param float $longitude User longitude
     * @param int $radius Search radius in kilometers (default: 10)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNearby($query, $latitude, $longitude, $radius = 10)
    {
        return $query->select('*')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) *
                cos( radians( latitude ) ) *
                cos( radians( longitude ) - radians(?) ) +
                sin( radians(?) ) *
                sin( radians( latitude ) ) ) ) AS distance_km',
                [$latitude, $longitude, $latitude]
            )
            ->withPublicLocation()
            ->having('distance_km', '<=', $radius)
            ->orderBy('distance_km', 'asc');
    }

    /**
     * Calculate distance to a specific point
     *
     * @param float $latitude
     * @param float $longitude
     * @return float|null Distance in kilometers
     */
    public function distanceTo($latitude, $longitude)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
