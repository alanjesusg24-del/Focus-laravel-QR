<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $table = 'plans';
    protected $primaryKey = 'plan_id';

    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'retention_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'retention_days' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get all businesses with this plan
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class, 'plan_id', 'plan_id');
    }

    /**
     * Get all payments for this plan
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'plan_id', 'plan_id');
    }

    /**
     * Scope to get only active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
