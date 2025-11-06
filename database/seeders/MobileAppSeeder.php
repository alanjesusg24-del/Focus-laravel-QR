<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MobileAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un negocio de prueba si no existe
        $business = Business::firstOrCreate(
            ['business_id' => 1],
            [
                'business_name' => 'Cafetería de Prueba',
                'rfc' => 'TEST123456ABC',
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
                'phone' => '5512345678',
                'address' => 'Dirección de prueba 123',
                'latitude' => 19.4326,
                'longitude' => -99.1332,
                'plan_id' => 1,
                'registration_date' => now(),
                'last_payment_date' => now(),
                'is_active' => true,
                'theme' => 'food',
            ]
        );

        // Crear órdenes de prueba
        $ordersData = [
            [
                'business_id' => $business->business_id,
                'order_number' => 'ORD-2025-001',
                'folio_number' => 'TEST-001',
                'customer_name' => 'Juan Pérez',
                'customer_phone' => '+52 555 1234567',
                'customer_email' => 'juan@example.com',
                'description' => 'Café americano grande + croissant',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=TEST001&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'pending',
                'total_amount' => 85.00,
                'mobile_user_id' => null,
            ],
            [
                'business_id' => $business->business_id,
                'order_number' => 'ORD-2025-002',
                'folio_number' => 'TEST-002',
                'customer_name' => 'María García',
                'customer_phone' => '+52 555 7654321',
                'customer_email' => 'maria@example.com',
                'description' => 'Cappuccino + sandwich de jamón',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=TEST002&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'ready',
                'total_amount' => 120.00,
                'mobile_user_id' => null,
            ],
        ];

        foreach ($ordersData as $orderData) {
            $order = Order::create($orderData);

            // Crear items para cada orden
            OrderItem::create([
                'order_id' => $order->order_id,
                'item_name' => 'Producto de ejemplo',
                'description' => 'Descripción del producto',
                'quantity' => 2,
                'unit_price' => $order->total_amount / 2,
                'total_price' => $order->total_amount,
            ]);
        }

        $this->command->info('Datos de prueba para la app móvil creados exitosamente!');
    }
}
