<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    
    public function run(): void
    {
        
        Plan::query()->delete(); 

        $plans = [
            [
                'name' => 'Plan Base',
                'price' => 299.00, 
                'base_price' => 299.00,
                'chat_module_price' => 150.00, 
                'retention_price_per_month' => 50.00, 
                'duration_days' => 30,
                'retention_days' => 30, 
                'description' => 'Plan base del sistema. Precio: $299 MXN/mes. Módulo de chat: +$150 MXN. Retención adicional: +$50 MXN/mes.',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
