<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Order;

echo "=== PRUEBA DE ÓRDENES DUPLICADAS ===\n\n";

// Verificar usuarios
$user1 = User::where('email', 'test@test.com')->first();
$user2 = User::where('email', 'test2@test.com')->first();

if (!$user2) {
    echo "Creando segundo usuario de prueba...\n";
    $user2 = User::create([
        'name' => 'Test User 2',
        'email' => 'test2@test.com',
        'password' => bcrypt('12345678'),
        'device_id' => 'test-device-456',
    ]);
    echo "Usuario 2 creado: {$user2->email}\n\n";
} else {
    echo "Usuario 2 existe: {$user2->email}\n\n";
}

// Órdenes del usuario 1
$user1Orders = Order::where('user_id', $user1->id)->count();
echo "Órdenes asociadas al usuario 1 ({$user1->email}): {$user1Orders}\n";

// Órdenes del usuario 2
$user2Orders = Order::where('user_id', $user2->id)->count();
echo "Órdenes asociadas al usuario 2 ({$user2->email}): {$user2Orders}\n\n";

// Órdenes sin user_id (sistema antiguo)
$ordersWithoutUserId = Order::whereNull('user_id')->whereNotNull('mobile_user_id')->count();
echo "Órdenes SIN user_id (sistema antiguo): {$ordersWithoutUserId}\n\n";

echo "=== PROBLEMA IDENTIFICADO ===\n";
echo "Si ambos usuarios ven las mismas órdenes, es porque:\n";
echo "1. La app móvil NO está enviando el token de autenticación\n";
echo "2. El sistema está usando mobile_user_id en lugar de user_id\n";
echo "3. Ambos usuarios están usando el mismo device_id\n\n";

// Verificar si hay órdenes compartiendo el mismo mobile_user_id
$sharedOrders = Order::whereNotNull('mobile_user_id')
    ->select('mobile_user_id', \DB::raw('COUNT(*) as count'))
    ->groupBy('mobile_user_id')
    ->having('count', '>', 0)
    ->get();

echo "=== ÓRDENES POR DISPOSITIVO (mobile_user_id) ===\n";
foreach ($sharedOrders as $shared) {
    echo "Mobile User ID {$shared->mobile_user_id}: {$shared->count} órdenes\n";
}

echo "\n=== SOLUCIÓN ===\n";
echo "La app Flutter debe:\n";
echo "1. Guardar el token después del login\n";
echo "2. Enviar el token en el header Authorization: Bearer {token}\n";
echo "3. El sistema entonces filtrará por user_id en lugar de mobile_user_id\n";
