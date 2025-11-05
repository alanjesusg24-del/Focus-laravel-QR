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
        SuperAdmin::create([
            'email' => 'admin@cetam.mx',
            'password_hash' => Hash::make('password123'),
            'full_name' => 'CETAM Administrator',
        ]);

        SuperAdmin::create([
            'email' => 'superadmin@orderqr.com',
            'password_hash' => Hash::make('password123'),
            'full_name' => 'Order QR Super Admin',
        ]);
    }
}
