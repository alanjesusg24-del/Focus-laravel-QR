<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businesses = [
            [
                'business_name' => 'Cafetería El Buen Sabor',
                'rfc' => 'CAEB850101ABC',
                'email' => 'contacto@cafeteria.com',
                'password_hash' => Hash::make('password123'),
                'phone' => '5512345678',
                'address' => 'Av. Insurgentes Sur 123, Col. Roma, CDMX',
                'latitude' => 19.4326,
                'longitude' => -99.1332,
                'plan_id' => 1,
                'registration_date' => now(),
                'last_payment_date' => now(),
                'is_active' => true,
                'theme' => 'food',
            ],
            [
                'business_name' => 'Taller Mecánico AutoFix',
                'rfc' => 'TAMF900202DEF',
                'email' => 'info@autofix.com',
                'password_hash' => Hash::make('password123'),
                'phone' => '5587654321',
                'address' => 'Calzada de Tlalpan 456, Col. Portales, CDMX',
                'latitude' => 19.3700,
                'longitude' => -99.1419,
                'plan_id' => 2,
                'registration_date' => now()->subDays(30),
                'last_payment_date' => now()->subDays(5),
                'is_active' => true,
                'theme' => 'professional',
            ],
            [
                'business_name' => 'Restaurante La Tradición',
                'rfc' => 'RELT950303GHI',
                'email' => 'reservas@latradicion.com',
                'password_hash' => Hash::make('password123'),
                'phone' => '5523456789',
                'address' => 'Paseo de la Reforma 789, Col. Cuauhtémoc, CDMX',
                'latitude' => 19.4270,
                'longitude' => -99.1677,
                'plan_id' => 3,
                'registration_date' => now()->subDays(60),
                'last_payment_date' => now()->subDays(10),
                'is_active' => true,
                'theme' => 'food',
            ],
        ];

        foreach ($businesses as $business) {
            Business::create($business);
        }
    }
}
