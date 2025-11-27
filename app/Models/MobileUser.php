<?php

/**
 * ============================================
 * CETAM - Mobile User Model
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        MobileUser.php
 * @description Modelo de usuarios móviles y dispositivos
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * @table       mobile_users
 * @primaryKey  id
 *
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MobileUser extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'device_id',
        'fcm_token',
        'device_type',
        'device_model',
        'os_version',
        'app_version',
        'is_active',
        'last_seen_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the orders for the mobile user
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
