<?php

namespace Database\Seeders;

use App\Models\SupportTicket;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = [
            [
                'business_id' => 1,
                'subject' => 'No puedo generar códigos QR',
                'description' => 'Al intentar crear una nueva orden, el sistema no genera el código QR automáticamente.',
                'status' => 'open',
                'priority' => 'high',
                'created_at' => now()->subHours(2),
            ],
            [
                'business_id' => 2,
                'subject' => 'Problema con notificaciones',
                'description' => 'Las notificaciones push no están llegando a los dispositivos móviles de los clientes.',
                'status' => 'in_progress',
                'priority' => 'high',
                'responded_at' => now()->subHours(1),
                'response' => 'Estamos revisando la configuración de Firebase. Le notificaremos cuando esté solucionado.',
                'created_at' => now()->subHours(3),
            ],
            [
                'business_id' => 3,
                'subject' => 'Consulta sobre plan Enterprise',
                'description' => '¿El plan Enterprise incluye reportes personalizados? Necesito más información.',
                'status' => 'closed',
                'priority' => 'medium',
                'responded_at' => now()->subDays(1),
                'closed_at' => now()->subHours(12),
                'response' => 'Sí, el plan Enterprise incluye reportes personalizados y exportación de datos. Puede contactarnos para más detalles.',
                'created_at' => now()->subDays(2),
            ],
            [
                'business_id' => 1,
                'subject' => 'Cambio de plan',
                'description' => 'Quiero cambiar del plan Basic al Professional. ¿Cómo lo hago?',
                'status' => 'open',
                'priority' => 'low',
                'created_at' => now()->subMinutes(30),
            ],
        ];

        foreach ($tickets as $ticket) {
            SupportTicket::create($ticket);
        }
    }
}
