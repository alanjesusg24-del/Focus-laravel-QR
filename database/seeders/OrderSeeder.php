<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            // Cafetería El Buen Sabor (business_id = 1)
            [
                'business_id' => 1,
                'order_number' => 'ORD-2025-001',
                'folio_number' => 'CAF-001',
                'customer_name' => 'Juan Pérez',
                'customer_phone' => '+52 555 1234567',
                'customer_email' => 'juan@example.com',
                'description' => 'Café americano grande + croissant',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=CAF001&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'pending',
                'total_amount' => 85.00,
                'mobile_user_id' => null,
                'created_at' => now()->subMinutes(15),
            ],
            [
                'business_id' => 1,
                'order_number' => 'ORD-2025-002',
                'folio_number' => 'CAF-002',
                'customer_name' => 'María García',
                'customer_phone' => '+52 555 7654321',
                'description' => 'Cappuccino + sandwich de jamón',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=CAF002&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'ready',
                'total_amount' => 120.00,
                'mobile_user_id' => null,
                'ready_at' => now()->subMinutes(5),
                'created_at' => now()->subMinutes(30),
            ],
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);

            // Crear items de ejemplo para cada orden
            OrderItem::create([
                'order_id' => $order->order_id,
                'item_name' => 'Producto ' . $order->order_number,
                'quantity' => 2,
                'unit_price' => $order->total_amount / 2,
                'total_price' => $order->total_amount,
            ]);
        }
    }
}
