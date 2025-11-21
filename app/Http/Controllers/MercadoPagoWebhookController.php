<?php

/**
 * ============================================
 * CETAM - MercadoPago Webhook Controller
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        MercadoPagoWebhookController.php
 * @description Controlador de webhooks de MercadoPago
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Handle MercadoPago webhook notifications
     */
    public function handleWebhook(Request $request)
    {
        Log::info('MercadoPago webhook received', [
            'data' => $request->all(),
        ]);

        try {
            $data = $request->all();

            // Procesar notificación
            $result = $this->mercadoPagoService->processWebhookNotification($data);

            if ($result) {
                return response()->json(['status' => 'success'], 200);
            } else {
                return response()->json(['status' => 'ignored'], 200);
            }

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }
}
