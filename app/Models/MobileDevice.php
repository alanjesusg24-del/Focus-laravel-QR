<?php

/**
 * ============================================
 * CETAM - Mobile Device Model
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        MobileDevice.php
 * @description Modelo de dispositivos móviles registrados
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * @table       mobile_devices
 * @primaryKey  mobile_device_id
 *
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileDevice extends Model
{
    protected $table = 'mobile_devices';
    protected $primaryKey = 'mobile_device_id';

    protected $fillable = [
        'mobile_user_id',
        'fcm_token',
        'platform',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only active devices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('mobile_user_id', $userId);
    }
}
