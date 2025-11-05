<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Actualizar planes existentes con precios modulares
        Plan::query()->delete(); // Limpiar planes antiguos

        $plans = [
            [
                'name' => 'Plan Base',
                'price' => 299.00, // Precio base mensual
                'base_price' => 299.00,
                'chat_module_price' => 150.00, // +$150 MXN por módulo de chat
                'retention_price_per_month' => 50.00, // +$50 MXN por mes adicional de retención
                'duration_days' => 30,
                'retention_days' => 30, // 1 mes por defecto
                'description' => 'Plan base del sistema. Precio: $299 MXN/mes. Módulo de chat: +$150 MXN. Retención adicional: +$50 MXN/mes.',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
