<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$business = App\Models\Business::find(3);
$business->update([
    'subscription_start_date' => now(),
    'subscription_end_date' => now()->addDays(30),
    'subscription_active' => true,
    'subscription_days' => 30,
    'last_payment_date' => now(),
]);

echo "✓ Suscripción activada para: {$business->business_name}\n";
echo "  Inicio: {$business->subscription_start_date}\n";
echo "  Fin: {$business->subscription_end_date}\n";
echo "  Días: 30\n";
