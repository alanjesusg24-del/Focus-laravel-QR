<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class PushNotificationService
{
    /**
     * Obtener instancia de Firebase Messaging usando la nueva API v1
     */
    private static function getMessaging()
    {
        // Obtener la ruta del archivo de credenciales
        $credentialsPath = env('FIREBASE_CREDENTIALS_PATH');

        // Si está vacía o no configurada, usar la ruta predeterminada
        if (empty($credentialsPath)) {
            $credentialsPath = storage_path('firebase-credentials.json');
        }

        if (!file_exists($credentialsPath)) {
            Log::error('❌ Archivo de credenciales de Firebase no encontrado', [
                'path' => $credentialsPath,
                'env_value' => env('FIREBASE_CREDENTIALS_PATH'),
            ]);
            throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
        }

        $factory = (new Factory)->withServiceAccount($credentialsPath);
        return $factory->createMessaging();
    }

    /**
     * Enviar notificación de cambio de estado de orden
     *
     * @param string $fcmToken Token FCM del dispositivo móvil
     * @param object $order Objeto de la orden
     * @param string $oldStatus Estado anterior
     * @param string $newStatus Estado nuevo
     * @return bool
     */
    public static function sendOrderStatusChange($fcmToken, $order, $oldStatus, $newStatus)
    {
        // Determinar el título y mensaje según el estado
        [$title, $body] = self::getNotificationContent($order, $oldStatus, $newStatus);

        // Payload de la notificación
        $data = [
            'to' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => [
                'type' => 'order_status_change',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'folio_number' => $order->folio_number,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'priority' => 'high',
            'content_available' => true,
        ];

        return self::sendNotification($data);
    }

    /**
     * Enviar notificación de nueva orden asociada
     */
    public static function sendOrderAssociated($fcmToken, $order)
    {
        $data = [
            'to' => $fcmToken,
            'notification' => [
                'title' => '🎉 Nueva orden asociada',
                'body' => "Se ha asociado la orden {$order->order_number} a tu dispositivo",
                'sound' => 'default',
            ],
            'data' => [
                'type' => 'order_associated',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'new_status' => $order->status,
                'folio_number' => $order->folio_number,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'priority' => 'high',
            'content_available' => true,
        ];

        return self::sendNotification($data);
    }

    /**
     * Enviar notificación de orden cancelada
     */
    public static function sendOrderCancelled($fcmToken, $order)
    {
        $data = [
            'to' => $fcmToken,
            'notification' => [
                'title' => '❌ Orden cancelada',
                'body' => "La orden {$order->order_number} ha sido cancelada",
                'sound' => 'default',
            ],
            'data' => [
                'type' => 'order_cancelled',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'old_status' => 'pending',
                'new_status' => 'cancelled',
                'folio_number' => $order->folio_number,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'priority' => 'high',
            'content_available' => true,
        ];

        return self::sendNotification($data);
    }

    /**
     * Enviar notificación de orden entregada
     */
    public static function sendOrderDelivered($fcmToken, $order)
    {
        $data = [
            'to' => $fcmToken,
            'notification' => [
                'title' => '✅ Orden entregada',
                'body' => "Tu orden {$order->order_number} ha sido entregada exitosamente. ¡Gracias por tu compra!",
                'sound' => 'default',
            ],
            'data' => [
                'type' => 'order_delivered',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'old_status' => 'ready',
                'new_status' => 'delivered',
                'folio_number' => $order->folio_number,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'priority' => 'high',
            'content_available' => true,
        ];

        return self::sendNotification($data);
    }

    /**
     * Enviar notificación de nuevo mensaje de chat desde el negocio
     *
     * @param string $fcmToken Token FCM del dispositivo móvil
     * @param object $order Objeto de la orden
     * @param string $messageText Texto del mensaje
     * @return array
     */
    public static function sendChatMessage($fcmToken, $order, $messageText)
    {
        try {
            $messaging = self::getMessaging();

            Log::info('📤 Enviando notificación de mensaje de chat', [
                'token' => substr($fcmToken, 0, 20) . '...',
                'order_number' => $order->order_number,
            ]);

            $businessName = $order->business->business_name ?? 'Negocio';
            $title = "💬 Mensaje de {$businessName}";
            $body = substr($messageText, 0, 100);

            // Construir el mensaje usando la nueva API
            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification($notification)
                ->withData([
                    'type' => 'new_chat_message',
                    'order_id' => (string) $order->order_id,
                    'order_number' => $order->order_number,
                    'folio_number' => $order->folio_number,
                    'business_id' => (string) $order->business_id,
                    'business_name' => $businessName,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]);

            // Enviar el mensaje
            $result = $messaging->send($message);

            Log::info('✅ Notificación de chat enviada exitosamente', [
                'result' => $result,
            ]);

            return [
                'success' => true,
                'message' => 'Chat notification sent successfully',
                'result' => $result,
            ];

        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            Log::error('❌ Token FCM no válido para notificación de chat', [
                'message' => $e->getMessage(),
                'order_number' => $order->order_number,
            ]);

            return [
                'success' => false,
                'message' => 'Invalid FCM token or device not found',
                'error' => $e->getMessage(),
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error al enviar notificación de chat', [
                'message' => $e->getMessage(),
                'order_number' => $order->order_number,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send chat notification',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Enviar recordatorio de orden lista (re-alerta)
     *
     * @param string $fcmToken Token FCM del dispositivo móvil
     * @param object $order Objeto de la orden
     * @param int $alertNumber Número de re-alerta (1, 2, 3, etc.)
     * @return array
     */
    public static function sendReadyReminder($fcmToken, $order, $alertNumber = 1)
    {
        try {
            $messaging = self::getMessaging();

            Log::info('📤 Enviando re-alerta de orden lista', [
                'token' => substr($fcmToken, 0, 20) . '...',
                'order_number' => $order->order_number,
                'alert_number' => $alertNumber,
            ]);

            // Personalizar el mensaje según el número de alerta
            $title = $alertNumber === 1
                ? '⏰ Recordatorio: Tu orden está lista'
                : "⏰ Recordatorio #{$alertNumber}: Tu orden sigue esperando";

            $body = "La orden {$order->order_number} está lista para recoger. ¡No olvides pasar por ella!";

            // Construir el mensaje usando la nueva API
            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification($notification)
                ->withData([
                    'type' => 'order_ready_reminder',
                    'order_id' => (string) $order->order_id,
                    'order_number' => $order->order_number,
                    'folio_number' => $order->folio_number,
                    'alert_number' => (string) $alertNumber,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]);

            // Enviar el mensaje
            $result = $messaging->send($message);

            Log::info('✅ Re-alerta enviada exitosamente', [
                'result' => $result,
                'alert_number' => $alertNumber,
            ]);

            return [
                'success' => true,
                'message' => 'Re-alert sent successfully',
                'result' => $result,
            ];

        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            Log::error('❌ Token FCM no válido para re-alerta', [
                'message' => $e->getMessage(),
                'order_number' => $order->order_number,
            ]);

            return [
                'success' => false,
                'message' => 'Invalid FCM token or device not found',
                'error' => $e->getMessage(),
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error al enviar re-alerta', [
                'message' => $e->getMessage(),
                'order_number' => $order->order_number,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send re-alert',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtener el contenido de la notificación según el cambio de estado
     */
    private static function getNotificationContent($order, $oldStatus, $newStatus)
    {
        switch ($newStatus) {
            case 'ready':
                return [
                    '🎉 ¡Tu orden está lista!',
                    "La orden {$order->order_number} está lista para recoger. ¡Ve por ella!"
                ];

            case 'delivered':
                return [
                    '✅ Orden entregada',
                    "La orden {$order->order_number} ha sido entregada exitosamente"
                ];

            case 'cancelled':
                return [
                    '❌ Orden cancelada',
                    "La orden {$order->order_number} ha sido cancelada"
                ];

            case 'pending':
                return [
                    '⏳ Orden en preparación',
                    "Tu orden {$order->order_number} está siendo preparada"
                ];

            default:
                return [
                    '🔔 Actualización de orden',
                    "La orden {$order->order_number} cambió de estado a {$newStatus}"
                ];
        }
    }

    /**
     * Enviar notificación usando FCM HTTP v1 API
     */
    private static function sendNotification($data)
    {
        try {
            $messaging = self::getMessaging();
            $fcmToken = $data['to'];

            Log::info('📤 Enviando notificación (FCM v1)', [
                'token' => substr($fcmToken, 0, 20) . '...',
                'type' => $data['data']['type'] ?? 'unknown',
                'order_id' => $data['data']['order_id'] ?? null,
            ]);

            // Construir el mensaje usando la nueva API
            $notification = Notification::create(
                $data['notification']['title'],
                $data['notification']['body']
            );

            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification($notification)
                ->withData($data['data']);

            // Enviar el mensaje
            $result = $messaging->send($message);

            Log::info('✅ Notificación enviada exitosamente (FCM v1)', [
                'result' => $result,
            ]);

            return true;
        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            Log::error('❌ Token FCM no válido o dispositivo no encontrado', [
                'message' => $e->getMessage(),
            ]);
            return false;
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error('❌ Error de Firebase Messaging', [
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('❌ Excepción al enviar notificación', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}
