<?php

/**
 * ============================================
 * CETAM - Super Admin Model
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        SuperAdmin.php
 * @description Modelo de superadministradores
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * @table       super_admins
 * @primaryKey  super_admin_id
 *
 * ============================================
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SuperAdmin extends Authenticatable
{
    use Notifiable;

    protected $table = 'super_admins';
    protected $primaryKey = 'super_admin_id';

    protected $fillable = [
        'email',
        'password',
        'full_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'super_admin_id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->super_admin_id;
    }

    /**
     * Get the password for authentication
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
}
