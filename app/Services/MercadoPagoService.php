<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use App\Models\Payment;
use App\Models\Business;
use App\Models\Plan;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    public function __construct()
    {
        // Configurar SDK de MercadoPago v3.x
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    /**
     * Crear preferencia de pago
     */
    public function createPreference(Business $business, Plan $plan)
    {
        try {
            $client = new PreferenceClient();

            // Construir URLs de retorno - forzar usar APP_URL de .env
            $baseUrl = config('app.url');
            $successUrl = $baseUrl . '/business/payments/success';
            $failureUrl = $baseUrl . '/business/payments/cancel';
            $pendingUrl = $baseUrl . '/business/payments/success';
            $webhookUrl = $baseUrl . '/webhook/mercadopago';

            $preferenceData = [
                "items" => [
                    [
                        "title" => $plan->name,
                        "description" => "Suscripción {$plan->name} - {$plan->duration_days} días",
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
                    "success" => $successUrl,
                    "failure" => $failureUrl,
                    "pending" => $pendingUrl
                ],
                "auto_return" => "approved",
                "external_reference" => "{$business->business_id}-{$plan->plan_id}",
                "metadata" => [
                    "business_id" => (string) $business->business_id,
                    "plan_id" => (string) $plan->plan_id,
                    "subscription_days" => (string) $plan->duration_days
                ],
                "notification_url" => $webhookUrl
            ];

            Log::info('Creating MercadoPago preference', [
                'back_urls' => $preferenceData['back_urls'],
                'business_id' => $business->business_id,
            ]);

            $preference = $client->create($preferenceData);

            // El SDK v3 devuelve un objeto MercadoPago\Resources\Preference
            // Acceder a propiedades con -> (usa __get mágico)
            $preferenceId = $preference->id;
            $initPoint = $preference->init_point;
            $sandboxInitPoint = $preference->sandbox_init_point;

            Log::info('MercadoPago preference created', [
                'preference_id' => $preferenceId,
                'init_point' => $initPoint,
                'sandbox_init_point' => $sandboxInitPoint,
                'business_id' => $business->business_id,
                'plan_id' => $plan->plan_id,
            ]);

            return [
                'success' => true,
                'preference_id' => (string) $preferenceId,
                'init_point' => (string) $initPoint,
                'sandbox_init_point' => (string) $sandboxInitPoint,
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errorDetails = [
                'error' => $errorMessage,
                'business_id' => $business->business_id,
                'plan_id' => $plan->plan_id,
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];

            // Si es un MPApiException de MercadoPago, capturar detalles
            if ($e instanceof \MercadoPago\Exceptions\MPApiException) {
                try {
                    // Intentar obtener diferentes propiedades
                    if (method_exists($e, 'getApiResponse')) {
                        $apiResponse = $e->getApiResponse();
                        $errorDetails['api_response_raw'] = print_r($apiResponse, true);
                    }

                    if (method_exists($e, 'getStatusCode')) {
                        $errorDetails['status_code'] = $e->getStatusCode();
                    }

                    if (method_exists($e, 'getHeaders')) {
                        $errorDetails['headers'] = $e->getHeaders();
                    }
                } catch (\Exception $ex) {
                    $errorDetails['api_response_error'] = $ex->getMessage();
                }
            }

            Log::error('MercadoPago preference creation failed', $errorDetails);

            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        }
    }

    /**
     * Procesar notificación de webhook
     */
    public function processWebhookNotification($data)
    {
        try {
            $type = $data['type'] ?? null;
            $paymentId = $data['data']['id'] ?? null;

            if ($type === 'payment' && $paymentId) {
                // Obtener información del pago usando PaymentClient
                $client = new PaymentClient();
                $paymentInfo = $client->get($paymentId);

                if (!$paymentInfo) {
                    Log::warning('MercadoPago payment not found', ['payment_id' => $paymentId]);
                    return false;
                }

                // Extraer metadata
                $businessId = $paymentInfo->metadata->business_id ?? null;
                $planId = $paymentInfo->metadata->plan_id ?? null;
                $subscriptionDays = $paymentInfo->metadata->subscription_days ?? 30;

                if (!$businessId || !$planId) {
                    Log::warning('Missing metadata in MercadoPago payment', [
                        'payment_id' => $paymentId,
                    ]);
                    return false;
                }

                $business = Business::find($businessId);
                $plan = Plan::find($planId);

                if (!$business || !$plan) {
                    Log::error('Business or plan not found', [
                        'business_id' => $businessId,
                        'plan_id' => $planId,
                    ]);
                    return false;
                }

                // Registrar o actualizar pago
                $payment = Payment::updateOrCreate(
                    ['mercadopago_payment_id' => $paymentId],
                    [
                        'business_id' => $business->business_id,
                        'plan_id' => $plan->plan_id,
                        'amount' => $paymentInfo->transaction_amount,
                        'mercadopago_preference_id' => $paymentInfo->preference_id ?? null,
                        'mercadopago_status' => $paymentInfo->status,
                        'mercadopago_response' => json_encode($paymentInfo),
                        'payment_provider' => 'mercadopago',
                        'status' => $this->mapMercadoPagoStatus($paymentInfo->status),
                        'payment_date' => now(),
                    ]
                );

                // Si el pago fue aprobado, activar suscripción
                if ($paymentInfo->status === 'approved') {
                    $this->activateSubscription($business, $subscriptionDays);

                    Log::info('Subscription activated', [
                        'business_id' => $business->business_id,
                        'payment_id' => $payment->payment_id,
                        'subscription_days' => $subscriptionDays,
                    ]);
                }

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('MercadoPago webhook processing failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return false;
        }
    }

    /**
     * Activar suscripción del negocio
     */
    protected function activateSubscription(Business $business, int $days)
    {
        $startDate = now();
        $endDate = now()->addDays($days);

        $business->update([
            'subscription_start_date' => $startDate,
            'subscription_end_date' => $endDate,
            'subscription_active' => true,
            'subscription_days' => $days,
            'last_payment_date' => now(),
        ]);

        Log::info('Subscription activated', [
            'business_id' => $business->business_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days' => $days,
        ]);
    }

    /**
     * Mapear estado de MercadoPago a estado del sistema
     */
    protected function mapMercadoPagoStatus($mpStatus)
    {
        return match($mpStatus) {
            'approved' => 'completed',
            'pending', 'in_process' => 'pending',
            'rejected', 'cancelled' => 'failed',
            'refunded' => 'refunded',
            default => 'pending',
        };
    }

    /**
     * Verificar si la suscripción está activa
     */
    public function isSubscriptionActive(Business $business)
    {
        if (!$business->subscription_active) {
            return false;
        }

        if (!$business->subscription_end_date) {
            return false;
        }

        // Verificar si la fecha de expiración ya pasó
        if (now()->isAfter($business->subscription_end_date)) {
            // Desactivar suscripción expirada
            $business->update(['subscription_active' => false]);
            return false;
        }

        return true;
    }

    /**
     * Obtener días restantes de suscripción
     */
    public function getSubscriptionDaysRemaining(Business $business)
    {
        if (!$this->isSubscriptionActive($business)) {
            return 0;
        }

        return now()->diffInDays($business->subscription_end_date, false);
    }
}
