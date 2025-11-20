<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;

echo "=== VERIFICACIÓN DE ÓRDENES PARA CHAT ===\n\n";

// Total de órdenes
$totalOrders = Order::count();
echo "Total de órdenes en el sistema: {$totalOrders}\n";

// Órdenes con mobile_user_id
$withMobileUser = Order::whereNotNull('mobile_user_id')->count();
echo "Órdenes ligadas a dispositivos móviles: {$withMobileUser}\n";

// Órdenes activas (pending o ready) con mobile_user_id
$activeOrders = Order::whereNotNull('mobile_user_id')
    ->whereIn('status', ['pending', 'ready'])
    ->count();
echo "Órdenes activas (pending/ready) ligadas a dispositivos: {$activeOrders}\n\n";

// Detalles de las primeras 5 órdenes activas
echo "=== PRIMERAS 5 ÓRDENES ACTIVAS ===\n";
$orders = Order::whereNotNull('mobile_user_id')
    ->whereIn('status', ['pending', 'ready'])
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get(['order_id', 'business_id', 'folio_number', 'status', 'mobile_user_id', 'created_at']);

foreach ($orders as $order) {
    echo sprintf(
        "Order #%d | Folio: %s | Business: %d | Status: %s | Mobile User: %d | Created: %s\n",
        $order->order_id,
        $order->folio_number,
        $order->business_id,
        $order->status,
        $order->mobile_user_id,
        $order->created_at->format('Y-m-d H:i:s')
    );
}

echo "\n=== VERIFICAR ORDEN 116 ===\n";
$order116 = Order::find(116);
if ($order116) {
    echo sprintf(
        "Order #%d | Folio: %s | Business: %d | Status: %s | Mobile User: %s | Created: %s\n",
        $order116->order_id,
        $order116->folio_number,
        $order116->business_id,
        $order116->status,
        $order116->mobile_user_id ?? 'NULL',
        $order116->created_at->format('Y-m-d H:i:s')
    );

    if ($order116->mobile_user_id && in_array($order116->status, ['pending', 'ready'])) {
        echo "✓ Esta orden DEBERÍA aparecer en el chat\n";
    } else {
        echo "✗ Esta orden NO aparecerá en el chat\n";
        echo "  Razón: ";
        if (!$order116->mobile_user_id) {
            echo "No tiene mobile_user_id asignado\n";
        } elseif (!in_array($order116->status, ['pending', 'ready'])) {
            echo "El status '{$order116->status}' no está en [pending, ready]\n";
        }
    }
} else {
    echo "✗ La orden 116 no existe en la base de datos\n";
}
