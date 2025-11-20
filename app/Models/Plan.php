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
        'has_chat_module',
        'has_realerts',
        'realert_interval_minutes',
        'realert_max_count',
        'realert_days',
        'realert_hours',
        'realert_minutes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'retention_days' => 'integer',
        'is_active' => 'boolean',
        'has_chat_module' => 'boolean',
        'has_realerts' => 'boolean',
        'realert_interval_minutes' => 'integer',
        'realert_max_count' => 'integer',
        'realert_days' => 'integer',
        'realert_hours' => 'integer',
        'realert_minutes' => 'integer',
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
