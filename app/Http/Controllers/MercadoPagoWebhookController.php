<?php

/**
 * Company: CETAM
 * Project: FF
 * File: MercadoPagoWebhookController.php
 * Created on: 20/11/2025
 * Created by:Dafne Vanessa Castillo Moreo
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 15/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreo |
 *   Description: Refactored to comply  |
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    protected MercadoPagoService $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Handle MercadoPago webhook notifications
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        Log::info('MercadoPago webhook received', [
            'data' => $request->all(),
        ]);

        try {
            $data = $request->all();

            // Procesar notificación
            $result = $this->mercadoPagoService->processWebhookNotification($data);

            // 5.4.1: Early Return - Notification ignored
            if (!$result) {
                return response()->json(['status' => 'ignored'], 200);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }
}
