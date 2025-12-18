<?php

/**
 * Company: CETAM
 * Project: FF
 * File: NotificationService.php
 * Created on: 20/10/2025
 * Created by: Alan Jesus Garcia Nava
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 10/11/2025 |
 *   Modified by: Alan Jesus Garcia Nava |
 *   Description: Refactored NotificationService|
 */

namespace App\Services;

use App\Models\MobileDevice;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    protected ?string $serverKey;

    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key', env('FIREBASE_SERVER_KEY'));
    }

    /**
     * Send order ready notification
     */
    public function sendOrderReadyNotification(Order $order): bool
    {
        $title = 'Tu pedido está listo';
        $message = "El pedido {$order->folio_number} está listo para recoger. ¡Te esperamos!";

        return $this->sendNotificationToUser(
            $order->mobile_user_id,
            $title,
            $message,
            'order_ready',
            $order
        );
    }

    /**
     * Send order cancelled notification
     */
    public function sendOrderCancelledNotification(Order $order, string $reason): bool
    {
        $title = 'Pedido cancelado';
        $message = "El pedido {$order->folio_number} ha sido cancelado. Razón: {$reason}";

        return $this->sendNotificationToUser(
            $order->mobile_user_id,
            $title,
            $message,
            'order_cancelled',
            $order
        );
    }

    /**
     * Send reminder notification
     */
    public function sendReminderNotification(Order $order): bool
    {
        $title = 'Recordatorio de pedido';
        $message = "No olvides recoger tu pedido {$order->folio_number}. Token de recogida: {$order->pickup_token}";

        return $this->sendNotificationToUser(
            $order->mobile_user_id,
            $title,
            $message,
            'reminder',
            $order
        );
    }

    /**
     * Send notification to a specific user
     */
    public function sendNotificationToUser(
        int $mobileUserId,
        string $title,
        string $message,
        string $type,
        ?Order $order = null
    ): bool {
        $devices = MobileDevice::where('mobile_user_id', $mobileUserId)
            ->where('is_active', true)
            ->get();

        if ($devices->isEmpty()) {
            Log::warning("No active devices found for user {$mobileUserId}");
            return false;
        }

        $results = $devices->map(function (MobileDevice $device) use ($title, $message, $type, $order, $mobileUserId) {
            $sent = $this->sendFcmNotification(
                $device->fcm_token,
                $title,
                $message,
                [
                    'type' => $type,
                    'order_id' => $order?->order_id,
                    'folio_number' => $order?->folio_number,
                ]
            );

            if ($order) {
                $this->logNotification($order, $mobileUserId, $type, $title, $message, $sent);
            }

            return $sent;
        });

        return $results->filter(fn(bool $sent) => $sent)->isNotEmpty();
    }

    /**
     * Send FCM push notification
     */
    protected function sendFcmNotification(
        string $fcmToken,
        string $title,
        string $body,
        array $data = []
    ): bool {

        if (!$this->serverKey) {
            Log::error('Firebase server key not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'badge' => '1',
                ],
                'data' => $data,
                'priority' => 'high',
            ]);

            // 5.4.1: Early Return - Request failed
            if (!$response->successful()) {
                Log::error("FCM notification failed", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            Log::info("Notification sent successfully to token: {$fcmToken}");
            return true;

        } catch (\Exception $e) {
            Log::error("Exception sending FCM notification: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Log notification to database
     */
    protected function logNotification(
        Order $order,
        int $mobileUserId,
        string $type,
        string $title,
        string $message,
        bool $sentSuccessfully
    ): Notification {
        return Notification::create([
            'order_id' => $order->order_id,
            'mobile_user_id' => $mobileUserId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'sent_successfully' => $sentSuccessfully,
            'sent_at' => now(),
        ]);
    }

    /**
     * Send bulk notifications
     */
    public function sendBulkNotifications(array $userIds, string $title, string $message): array
    {
        return collect($userIds)->mapWithKeys(fn(int $userId) => [
            $userId => $this->sendNotificationToUser($userId, $title, $message, 'bulk')
        ])->toArray();
    }

    /**
     * Register a new mobile device
     */
    public function registerDevice(int $mobileUserId, string $fcmToken, string $platform): MobileDevice
    {
        MobileDevice::where('fcm_token', $fcmToken)->update(['is_active' => false]);

        return MobileDevice::create([
            'mobile_user_id' => $mobileUserId,
            'fcm_token' => $fcmToken,
            'platform' => $platform,
            'is_active' => true,
        ]);
    }

    /**
     * Deactivate a device
     */
    public function deactivateDevice(string $fcmToken): bool
    {
        return MobileDevice::where('fcm_token', $fcmToken)
            ->update(['is_active' => false]) > 0;
    }
}
