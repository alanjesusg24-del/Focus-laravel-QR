<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing super admins first
        SuperAdmin::truncate();

        // Super Admin de prueba principal
        SuperAdmin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Super Administrador',
        ]);

        // Super Admin secundario
        SuperAdmin::create([
            'email' => 'superadmin@cetam.mx',
            'password' => Hash::make('password123'),
            'full_name' => 'CETAM Administrator',
        ]);
    }
}
