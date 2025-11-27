<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use App\Models\Business;
use App\Models\Plan;

class TestMercadoPago extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mercadopago';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test MercadoPago API connection and preference creation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing MercadoPago SDK...');

        try {
            // Test 1: Config
            $this->info('1. Configuring MercadoPago...');
            $accessToken = config('services.mercadopago.access_token');
            $this->info("Access Token: " . substr($accessToken, 0, 20) . "...");

            MercadoPagoConfig::setAccessToken($accessToken);
            $this->info('✓ Configuration successful');

            // Test 2: Get a business and plan
            $this->info('2. Getting business and plan...');
            $business = Business::first();
            $plan = Plan::first();

            if (!$business || !$plan) {
                $this->error('No business or plan found in database');
                return;
            }

            $this->info("Business: {$business->business_name}");
            $this->info("Plan: {$plan->name} - \${$plan->price}");

            // Test 3: Create preference
            $this->info('3. Creating preference...');
            $client = new PreferenceClient();

            $preferenceData = [
                "items" => [
                    [
                        "title" => $plan->name,
                        "description" => "Test - Suscripción {$plan->name}",
                        "quantity" => 1,
                        "unit_price" => (float) $plan->price,
                        "currency_id" => "MXN"
                    ]
                ],
                "payer" => [
                    "email" => $business->email,
                    "name" => $business->business_name
                ],
                "back_urls" => [
                    "success" => route('business.payments.success'),
                    "failure" => route('business.payments.cancel'),
                    "pending" => route('business.payments.success')
                ],
                "auto_return" => "approved",
                "external_reference" => "test-{$business->business_id}-{$plan->plan_id}"
            ];

            $this->info('Preference data:');
            $this->line(json_encode($preferenceData, JSON_PRETTY_PRINT));

            $preference = $client->create($preferenceData);

            $this->info('✓ Preference created successfully!');
            $this->info('Preference Type: ' . get_class($preference));
            $this->info('Preference Structure:');
            $this->line(print_r($preference, true));

            // Intentar diferentes formas de acceso
            $this->info('--- Testing property access methods ---');

            // Método 1: Acceso directo
            try {
                $this->info("Method 1 (->id): " . ($preference->id ?? 'NULL'));
            } catch (\Exception $e) {
                $this->error("Method 1 failed: " . $e->getMessage());
            }

            // Método 2: Acceso como array
            try {
                $this->info("Method 2 (['id']): " . ($preference['id'] ?? 'NULL'));
            } catch (\Exception $e) {
                $this->error("Method 2 failed: " . $e->getMessage());
            }

            // Método 3: getContent() o similar
            if (method_exists($preference, 'getContent')) {
                $content = $preference->getContent();
                $this->info("Method 3 (getContent): " . json_encode($content));
            }

            // Método 4: __toString
            if (method_exists($preference, '__toString')) {
                $this->info("Method 4 (__toString): " . $preference->__toString());
            }

        } catch (\Exception $e) {
            $this->error('ERROR: ' . $e->getMessage());
            $this->error('Class: ' . get_class($e));
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());

            if (method_exists($e, 'getApiResponse')) {
                $this->error('API Response:');
                $this->line(json_encode($e->getApiResponse(), JSON_PRETTY_PRINT));
            }

            $this->error('Trace: ' . $e->getTraceAsString());
        }
    }
}
