<?php

namespace Database\Seeders;

use App\Models\Order;
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
                'folio_number' => 'CAF-001',
                'description' => 'Café americano grande + croissant',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=CAF001&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'pending',
                'mobile_user_id' => 1001,
                'created_at' => now()->subMinutes(15),
            ],
            [
                'business_id' => 1,
                'folio_number' => 'CAF-002',
                'description' => 'Cappuccino + sandwich de jamón',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=CAF002&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'ready',
                'mobile_user_id' => 1002,
                'ready_at' => now()->subMinutes(5),
                'created_at' => now()->subMinutes(30),
            ],
            [
                'business_id' => 1,
                'folio_number' => 'CAF-003',
                'description' => 'Latte + pastel de chocolate',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=CAF003&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'delivered',
                'mobile_user_id' => 1003,
                'ready_at' => now()->subHours(1),
                'delivered_at' => now()->subMinutes(45),
                'created_at' => now()->subHours(2),
            ],

            // Taller Mecánico AutoFix (business_id = 2)
            [
                'business_id' => 2,
                'folio_number' => 'TM-101',
                'description' => 'Cambio de aceite - Toyota Corolla 2020',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=TM101&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'pending',
                'mobile_user_id' => 2001,
                'created_at' => now()->subHours(3),
            ],
            [
                'business_id' => 2,
                'folio_number' => 'TM-102',
                'description' => 'Revisión de frenos - Honda Civic 2019',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=TM102&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'ready',
                'mobile_user_id' => 2002,
                'ready_at' => now()->subMinutes(10),
                'created_at' => now()->subHours(5),
            ],

            // Restaurante La Tradición (business_id = 3)
            [
                'business_id' => 3,
                'folio_number' => 'REST-501',
                'description' => 'Tacos al pastor x3 + refresco',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=REST501&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'pending',
                'mobile_user_id' => 3001,
                'created_at' => now()->subMinutes(8),
            ],
            [
                'business_id' => 3,
                'folio_number' => 'REST-502',
                'description' => 'Enchiladas verdes + agua de horchata',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=REST502&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'ready',
                'mobile_user_id' => 3002,
                'ready_at' => now()->subMinutes(2),
                'created_at' => now()->subMinutes(20),
            ],
            [
                'business_id' => 3,
                'folio_number' => 'REST-503',
                'description' => 'Mole poblano + arroz + frijoles',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=REST503&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'delivered',
                'mobile_user_id' => 3003,
                'ready_at' => now()->subHours(1)->subMinutes(30),
                'delivered_at' => now()->subHours(1),
                'created_at' => now()->subHours(2),
            ],
            [
                'business_id' => 3,
                'folio_number' => 'REST-504',
                'description' => 'Pozole rojo + tostadas',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=REST504&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'cancelled',
                'mobile_user_id' => 3004,
                'cancelled_at' => now()->subMinutes(25),
                'cancellation_reason' => 'Cliente canceló la orden',
                'created_at' => now()->subMinutes(35),
            ],
            [
                'business_id' => 1,
                'folio_number' => 'CAF-004',
                'description' => 'Frappé de vainilla + donas',
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?data=CAF004&size=300x300',
                'qr_token' => Str::random(32),
                'pickup_token' => Str::random(16),
                'status' => 'pending',
                'mobile_user_id' => 1004,
                'created_at' => now()->subMinutes(3),
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
