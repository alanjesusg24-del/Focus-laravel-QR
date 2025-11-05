<?php

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
     *
     * @param Order $order
     * @return bool
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
     *
     * @param Order $order
     * @param string $reason
     * @return bool
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
     *
     * @param Order $order
     * @return bool
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
     *
     * @param int $mobileUserId
     * @param string $title
     * @param string $message
     * @param string $type
     * @param Order|null $order
     * @return bool
     */
    public function sendNotificationToUser(
        int $mobileUserId,
        string $title,
        string $message,
        string $type,
        ?Order $order = null
    ): bool {
        // Get all active devices for the user
        $devices = MobileDevice::where('mobile_user_id', $mobileUserId)
            ->where('is_active', true)
            ->get();

        if ($devices->isEmpty()) {
            Log::warning("No active devices found for user {$mobileUserId}");
            return false;
        }

        $success = true;

        foreach ($devices as $device) {
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

            if (!$sent) {
                $success = false;
            }

            // Log notification
            if ($order) {
                $this->logNotification($order, $mobileUserId, $type, $title, $message, $sent);
            }
        }

        return $success;
    }

    /**
     * Send FCM push notification
     *
     * @param string $fcmToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
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

            if ($response->successful()) {
                Log::info("Notification sent successfully to token: {$fcmToken}");
                return true;
            }

            Log::error("FCM notification failed", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Exception sending FCM notification: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Log notification to database
     *
     * @param Order $order
     * @param int $mobileUserId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param bool $sentSuccessfully
     * @return Notification
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
     *
     * @param array $userIds
     * @param string $title
     * @param string $message
     * @return array Results
     */
    public function sendBulkNotifications(array $userIds, string $title, string $message): array
    {
        $results = [];

        foreach ($userIds as $userId) {
            $results[$userId] = $this->sendNotificationToUser(
                $userId,
                $title,
                $message,
                'bulk'
            );
        }

        return $results;
    }

    /**
     * Register a new mobile device
     *
     * @param int $mobileUserId
     * @param string $fcmToken
     * @param string $platform
     * @return MobileDevice
     */
    public function registerDevice(int $mobileUserId, string $fcmToken, string $platform): MobileDevice
    {
        // Deactivate existing devices with same token
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
     *
     * @param string $fcmToken
     * @return bool
     */
    public function deactivateDevice(string $fcmToken): bool
    {
        return MobileDevice::where('fcm_token', $fcmToken)
            ->update(['is_active' => false]) > 0;
    }
}
