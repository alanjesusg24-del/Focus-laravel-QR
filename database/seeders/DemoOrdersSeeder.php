<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DemoOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds for demo orders
     */
    public function run()
    {
        $business = Business::where('email', 'alanjesusg24@gmail.com')->first();

        if (!$business) {
            $this->command->error('No se encontró el negocio con ese email');
            return;
        }

        $businessId = $business->business_id;

        // Limpiar órdenes existentes del demo
        Order::where('business_id', $businessId)->delete();

        $this->command->info('Generando 100 órdenes de prueba para los últimos 45 días...');

        $descriptions = [
            '2 cafés americanos grandes, 1 capuchino',
            '1 hamburguesa con papas, 1 refresco',
            '3 tacos de asada, 2 de pastor',
            '1 pizza mediana pepperoni',
            '2 tortas de jamón, 1 jugo de naranja',
            '1 ensalada césar, 1 agua mineral',
            '4 quesadillas de queso',
            '2 burritos de pollo, 1 coca cola',
            '1 sándwich club, papas fritas',
            '3 empanadas de carne',
            '2 hot dogs completos, 1 sprite',
            '1 orden de alitas BBQ',
            '2 chilaquiles verdes',
            '1 desayuno americano completo',
            '3 molletes con frijoles',
        ];

        $statuses = ['pending', 'ready', 'delivered', 'cancelled'];
        $statusWeights = [
            'pending' => 15,    // 15% pendientes
            'ready' => 10,      // 10% listos
            'delivered' => 70,  // 70% entregados
            'cancelled' => 5,   // 5% cancelados
        ];

        // Horas pico: 8-10am, 2-3pm, 7-9pm
        $hourWeights = [
            8 => 12, 9 => 15, 10 => 10,  // Desayuno
            13 => 8, 14 => 12, 15 => 10,  // Comida
            19 => 10, 20 => 15, 21 => 8,  // Cena
        ];

        // Días de la semana (1=Lunes, 7=Domingo)
        $weekdayWeights = [
            1 => 10,  // Lunes
            2 => 12,  // Martes
            3 => 14,  // Miércoles
            4 => 15,  // Jueves
            5 => 18,  // Viernes
            6 => 20,  // Sábado
            7 => 11,  // Domingo
        ];

        $orderCount = 1;

        // Generar 100 órdenes distribuidas en los últimos 45 días
        for ($i = 0; $i < 100; $i++) {
            // Seleccionar día aleatorio en los últimos 45 días
            $daysAgo = rand(0, 45);
            $createdAt = Carbon::now()->subDays($daysAgo);

            // Ajustar día de la semana según pesos
            $weekday = $createdAt->dayOfWeekIso;
            if (!isset($weekdayWeights[$weekday])) {
                $weekday = array_rand($weekdayWeights);
                $createdAt->startOfWeek()->addDays($weekday - 1);
            }

            // Seleccionar hora según pesos
            $hour = $this->weightedRandom($hourWeights);
            $minute = rand(0, 59);
            $createdAt->setTime($hour, $minute);

            // Seleccionar estado según pesos
            $status = $this->weightedRandom($statusWeights);

            // 60% de las órdenes estarán ligadas a móvil
            $mobileUserId = rand(1, 100) <= 60 ? rand(1, 20) : null;

            $folioNumber = 'ORD-' . now()->year . '-' . str_pad($orderCount, 4, '0', STR_PAD_LEFT);
            $qrToken = Str::random(32);
            $pickupToken = strtoupper(Str::random(16));

            $order = Order::create([
                'business_id' => $businessId,
                'folio_number' => $folioNumber,
                'order_number' => $folioNumber,
                'description' => $descriptions[array_rand($descriptions)],
                'qr_token' => $qrToken,
                'pickup_token' => $pickupToken,
                'qr_code_url' => '/storage/qr_codes/demo_' . $qrToken . '.png',
                'status' => $status,
                'mobile_user_id' => $mobileUserId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Establecer fechas según el estado
            if ($mobileUserId) {
                $order->associated_at = $createdAt->copy()->addMinutes(rand(1, 5));
            }

            if (in_array($status, ['ready', 'delivered'])) {
                // Tiempo de preparación entre 5 y 45 minutos
                $prepTime = rand(5, 45);
                $order->ready_at = $createdAt->copy()->addMinutes($prepTime);
            }

            if ($status === 'delivered') {
                // Tiempo de espera para recoger entre 2 y 20 minutos
                $waitTime = rand(2, 20);
                $order->delivered_at = $order->ready_at->copy()->addMinutes($waitTime);
            }

            if ($status === 'cancelled') {
                $cancellationReasons = [
                    'Cliente no llegó a recoger',
                    'Pedido duplicado',
                    'Ingredientes no disponibles',
                    'Cliente canceló por teléfono',
                    'Error en el sistema',
                ];
                $order->cancelled_at = $createdAt->copy()->addMinutes(rand(1, 15));
                $order->cancellation_reason = $cancellationReasons[array_rand($cancellationReasons)];
            }

            $order->save();
            $orderCount++;
        }

        $this->command->info("✅ Se generaron 100 órdenes de prueba exitosamente!");
        $this->command->info("📊 Distribución:");
        $this->command->info("   - Entregadas: ~70%");
        $this->command->info("   - Pendientes: ~15%");
        $this->command->info("   - Listas: ~10%");
        $this->command->info("   - Canceladas: ~5%");
        $this->command->info("   - Ligadas a móvil: ~60%");
    }

    /**
     * Select a random key based on weights
     */
    private function weightedRandom($weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        foreach ($weights as $key => $weight) {
            $random -= $weight;
            if ($random <= 0) {
                return $key;
            }
        }

        return array_key_first($weights);
    }
}
