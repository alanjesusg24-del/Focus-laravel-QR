<?php

/**
 * ============================================
 * CETAM - Broadcast Channels
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        channels.php
 * @description Canales de broadcasting en tiempo real
 * @author      CETAM Dev Team
 * @created     2025-11-24
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
