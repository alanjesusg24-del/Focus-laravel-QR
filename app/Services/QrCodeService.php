<?php

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
     *
     * @param Order $order
     * @return string QR code URL
     */
    public function generateQrCodeForOrder(Order $order): string
    {
        try {
            // Generate unique QR token if not exists
            if (empty($order->qr_token)) {
                $order->qr_token = Str::random(32);
            }

            // Generate unique pickup token if not exists
            if (empty($order->pickup_token)) {
                $order->pickup_token = Str::random(16);
            }

            // Create QR code data URL - Web URL for browser scanning
            $appUrl = config('app.url', env('APP_URL', 'http://localhost'));
            $qrData = "{$appUrl}/orders/associate/{$order->qr_token}";

            Log::info('Generating QR code', [
                'order_id' => $order->order_id,
                'qr_data' => $qrData,
            ]);

            // Generate QR code image using SimpleSoftwareIO (SVG format - no imagick required)
            // SVG format works without imagick extension
            $qrCodeImage = QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($qrData);

            // Create directory if not exists
            $directory = "qr_codes/{$order->business_id}";
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Save QR code to storage (SVG format)
            $fileName = "{$directory}/order_{$order->order_id}_{$order->qr_token}.svg";
            Storage::disk('public')->put($fileName, $qrCodeImage);

            // Generate public URL
            $qrCodeUrl = Storage::url($fileName);

            // Update order with QR code URL
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
     *
     * @param Order $order
     * @return string New QR code URL
     */
    public function regenerateQrCodeForOrder(Order $order): string
    {
        // Delete old QR code if exists
        if ($order->qr_code_url) {
            $this->deleteQrCode($order);
        }

        // Generate new tokens
        $order->qr_token = Str::random(32);
        $order->pickup_token = Str::random(16);
        $order->save();

        return $this->generateQrCodeForOrder($order);
    }

    /**
     * Delete QR code from storage
     *
     * @param Order $order
     * @return bool
     */
    public function deleteQrCode(Order $order): bool
    {
        if (!$order->qr_code_url) {
            return false;
        }

        $fileName = str_replace('/storage/', '', $order->qr_code_url);

        if (Storage::disk('public')->exists($fileName)) {
            return Storage::disk('public')->delete($fileName);
        }

        return false;
    }

    /**
     * Validate QR token
     *
     * @param string $token
     * @return Order|null
     */
    public function validateQrToken(string $token): ?Order
    {
        return Order::where('qr_token', $token)
            ->whereIn('status', ['pending', 'ready'])
            ->first();
    }

    /**
     * Validate pickup token
     *
     * @param string $token
     * @return Order|null
     */
    public function validatePickupToken(string $token): ?Order
    {
        return Order::where('pickup_token', $token)
            ->where('status', 'ready')
            ->first();
    }

    /**
     * Generate bulk QR codes for multiple orders
     *
     * @param array $orderIds
     * @return array Generated URLs
     */
    public function generateBulkQrCodes(array $orderIds): array
    {
        $results = [];

        foreach ($orderIds as $orderId) {
            $order = Order::find($orderId);

            if ($order) {
                try {
                    $results[$orderId] = [
                        'success' => true,
                        'url' => $this->generateQrCodeForOrder($order),
                    ];
                } catch (\Exception $e) {
                    $results[$orderId] = [
                        'success' => false,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        }

        return $results;
    }
}
