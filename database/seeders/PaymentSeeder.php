<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payments = [
            [
                'business_id' => 1,
                'plan_id' => 1,
                'amount' => 299.00,
                'stripe_payment_id' => 'pi_3ABC123def456',
                'stripe_subscription_id' => 'sub_1ABC123def456',
                'status' => 'completed',
                'payment_date' => now(),
                'next_payment_date' => now()->addDays(30),
            ],
            [
                'business_id' => 2,
                'plan_id' => 2,
                'amount' => 599.00,
                'stripe_payment_id' => 'pi_3DEF456ghi789',
                'stripe_subscription_id' => 'sub_1DEF456ghi789',
                'status' => 'completed',
                'payment_date' => now()->subDays(5),
                'next_payment_date' => now()->addDays(25),
            ],
            [
                'business_id' => 3,
                'plan_id' => 3,
                'amount' => 999.00,
                'stripe_payment_id' => 'pi_3GHI789jkl012',
                'stripe_subscription_id' => 'sub_1GHI789jkl012',
                'status' => 'completed',
                'payment_date' => now()->subDays(10),
                'next_payment_date' => now()->addDays(20),
            ],
        ];

        foreach ($payments as $payment) {
            Payment::create($payment);
        }
    }
}
