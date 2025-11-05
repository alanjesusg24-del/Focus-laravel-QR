<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Order QR System Seeders - CETAM Standards
        $this->call([
            PlanSeeder::class,
            SuperAdminSeeder::class,
            BusinessSeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
            SupportTicketSeeder::class,
        ]);

        $this->command->info('✅ Order QR System seeded successfully!');
    }
}
