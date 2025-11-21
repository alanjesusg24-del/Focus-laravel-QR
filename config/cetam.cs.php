<?php

/**
 * ============================================
 * CETAM - Centro de Desarrollo Tecnológico Aplicado de México
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        cetam.cs.php
 * @description Configuración específica del proyecto CS - Order QR System
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

return [
    // Identificación del proyecto
    'code' => 'CS',
    'slug' => 'cs',
    'name' => 'Centro de Servicios - Order QR System',
    'version' => '1.0.0',

    // Características habilitadas
    'features' => [
        'invoicing' => false,
        'reporting' => true,
        'notifications' => true,
        'chat' => true,
        'qr_scanner' => true,
        'payments' => true,
        'mercadopago' => env('MERCADOPAGO_PUBLIC_KEY') !== null,
    ],

    // Configuración de base de datos
    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'volt_dashboard'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
    ],

    // Paginación
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
    ],

    // Rutas
    'routes' => [
        'prefix' => env('CETAM_PROJ_SLUG', 'cs'),
        'middleware' => ['web', 'auth'],
        'api_prefix' => 'api/v1',
    ],

    // Mobile App
    'mobile' => [
        'app_url' => env('MOBILE_APP_URL', env('APP_URL')),
    ],

    // Payment Configuration
    'payments' => [
        'mercadopago' => [
            'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
            'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
            'mode' => env('MERCADOPAGO_MODE', 'sandbox'),
        ],
    ],
];
