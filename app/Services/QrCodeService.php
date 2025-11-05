<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

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
        // Generate unique QR token if not exists
        if (empty($order->qr_token)) {
            $order->qr_token = Str::random(32);
        }

        // Generate unique pickup token if not exists
        if (empty($order->pickup_token)) {
            $order->pickup_token = Str::random(16);
        }

        // Create QR code data URL
        $mobileAppUrl = config('app.mobile_app_url', env('MOBILE_APP_URL', 'https://app.orderqr.com'));
        $qrData = "{$mobileAppUrl}/order/scan/{$order->qr_token}";

        // Generate QR code image using BaconQrCode with SVG (no imagick required)
        $renderer = new ImageRenderer(
            new RendererStyle(300, 1),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeImage = $writer->writeString($qrData);

        // Save QR code to storage (SVG format)
        $fileName = "qr_codes/{$order->business_id}/order_{$order->order_id}_{$order->qr_token}.svg";
        Storage::disk('public')->put($fileName, $qrCodeImage);

        // Generate public URL
        $qrCodeUrl = Storage::url($fileName);

        // Update order with QR code URL
        $order->qr_code_url = $qrCodeUrl;
        $order->save();

        return $qrCodeUrl;
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
