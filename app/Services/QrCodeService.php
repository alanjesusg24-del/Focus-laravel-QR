<?php

/**
 * Company: CETAM
 * Project: FF
 * File: QrCodeService.php
 * Created on: 20/11/2025
 * Created by: Alan Jesus Garcia Nava
 * Approved by: Alan Jesus Garcia Nava
 *
 * Changelog:
 * - ID: 1 | Modified on: 20/11/2025 |
 *   Modified by: Alan Jesus Garcia Nava |
 *   Description: Refactored QR code generation and management |
 */

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate QR code for an order
     */
    public function generateQrCodeForOrder(Order $order): string
    {
        try {
            // 5.5: Extract token generation to private method
            $this->ensureTokensExist($order);

            $appUrl = config('app.url', env('APP_URL', 'http://localhost'));
            $qrData = "{$appUrl}/orders/associate/{$order->qr_token}";

            Log::info('Generating QR code', [
                'order_id' => $order->order_id,
                'qr_data' => $qrData,
            ]);

            $qrCodeImage = QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($qrData);

            // 5.5: Extract file storage to private method
            $fileName = $this->saveQrCodeToStorage($order, $qrCodeImage);

            $qrCodeUrl = Storage::url($fileName);

            $order->qr_code_url = $qrCodeUrl;
            $order->save();

            Log::info('QR code generated successfully', [
                'order_id' => $order->order_id,
                'qr_code_url' => $qrCodeUrl,
            ]);

            return $qrCodeUrl;

        } catch (\Exception $e) {
            Log::error('Failed to generate QR code', [
                'order_id' => $order->order_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Error al generar código QR: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate QR code for an order
     */
    public function regenerateQrCodeForOrder(Order $order): string
    {
        if ($order->qr_code_url) {
            $this->deleteQrCode($order);
        }

        $order->qr_token = Str::random(32);
        $order->pickup_token = Str::random(16);
        $order->save();

        return $this->generateQrCodeForOrder($order);
    }

    /**
     * Delete QR code from storage
     */
    public function deleteQrCode(Order $order): bool
    {
        // 5.4.1: Early Return - No QR code
        if (!$order->qr_code_url) {
            return false;
        }

        $fileName = str_replace('/storage/', '', $order->qr_code_url);

        // 5.4.1: Early Return - File not found
        if (!Storage::disk('public')->exists($fileName)) {
            return false;
        }

        return Storage::disk('public')->delete($fileName);
    }

    /**
     * Validate QR token
     */
    public function validateQrToken(string $token): ?Order
    {
        return Order::where('qr_token', $token)
            ->whereIn('status', ['pending', 'ready'])
            ->first();
    }

    /**
     * Validate pickup token
     */
    public function validatePickupToken(string $token): ?Order
    {
        return Order::where('pickup_token', $token)
            ->where('status', 'ready')
            ->first();
    }

    /**
     * Generate bulk QR codes for multiple orders
     */
    public function generateBulkQrCodes(array $orderIds): array
    {
        // 5.3: Use Collection mapWithKeys instead of foreach
        return collect($orderIds)->mapWithKeys(function (int $orderId) {
            $order = Order::find($orderId);

            // 5.4.1: Early Return - Order not found
            if (!$order) {
                return [$orderId => ['success' => false, 'error' => 'Order not found']];
            }

            try {
                return [
                    $orderId => [
                        'success' => true,
                        'url' => $this->generateQrCodeForOrder($order),
                    ]
                ];
            } catch (\Exception $e) {
                return [
                    $orderId => [
                        'success' => false,
                        'error' => $e->getMessage(),
                    ]
                ];
            }
        })->toArray();
    }

    /**
     * Ensure tokens exist for order
     * 5.5: Private helper method following SRP
     */
    private function ensureTokensExist(Order $order): void
    {
        if (empty($order->qr_token)) {
            $order->qr_token = Str::random(32);
        }

        if (empty($order->pickup_token)) {
            $order->pickup_token = Str::random(16);
        }
    }

    /**
     * Save QR code image to storage
     * 5.5: Private helper method following SRP
     */
    private function saveQrCodeToStorage(Order $order, string $qrCodeImage): string
    {
        $directory = "qr_codes/{$order->business_id}";

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $fileName = "{$directory}/order_{$order->order_id}_{$order->qr_token}.svg";
        Storage::disk('public')->put($fileName, $qrCodeImage);

        return $fileName;
    }
}
