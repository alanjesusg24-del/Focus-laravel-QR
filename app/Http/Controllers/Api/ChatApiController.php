<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\MobileUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatApiController extends Controller
{
    /**
     * Get messages for a specific order
     * GET /api/orders/{order}/messages
     */
    public function getMessages(Request $request, $orderId)
    {
        try {
            $deviceId = $request->header('X-Device-ID');

            if (!$deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device ID es requerido en el header X-Device-ID',
                ], 400);
            }

            // Verificar que el dispositivo existe
            $mobileUser = MobileUser::where('device_id', $deviceId)->first();

            if (!$mobileUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dispositivo no encontrado',
                ], 404);
            }

            // Verificar que la orden existe y está ligada a este dispositivo
            $order = Order::where('order_id', $orderId)
                ->where('mobile_user_id', $mobileUser->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden no encontrada o no pertenece a este dispositivo',
                ], 404);
            }

            // Obtener mensajes
            $messages = ChatMessage::forOrder($orderId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'message_id' => $message->message_id,
                        'sender_type' => $message->sender_type,
                        'message' => $message->message,
                        'attachment_url' => $message->attachment_url,
                        'is_read' => $message->is_read,
                        'created_at' => $message->created_at->toIso8601String(),
                        'read_at' => $message->read_at ? $message->read_at->toIso8601String() : null,
                    ];
                });

            // Marcar mensajes del negocio como leídos
            ChatMessage::forOrder($orderId)
                ->bySenderType('business')
                ->unread()
                ->each(function ($message) {
                    $message->markAsRead();
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->order_id,
                    'folio_number' => $order->folio_number,
                    'messages' => $messages,
                    'total_messages' => $messages->count(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting chat messages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener mensajes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a message from mobile app
     * POST /api/orders/{order}/messages
     */
    public function sendMessage(Request $request, $orderId)
    {
        try {
            $deviceId = $request->header('X-Device-ID');

            if (!$deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device ID es requerido en el header X-Device-ID',
                ], 400);
            }

            // Validar datos
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
            ]);

            // Verificar que el dispositivo existe
            $mobileUser = MobileUser::where('device_id', $deviceId)->first();

            if (!$mobileUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dispositivo no encontrado',
                ], 404);
            }

            // Verificar que la orden existe y está ligada a este dispositivo
            $order = Order::where('order_id', $orderId)
                ->where('mobile_user_id', $mobileUser->id)
                ->with('business')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden no encontrada o no pertenece a este dispositivo',
                ], 404);
            }

            // Manejar archivo adjunto si existe
            $attachmentUrl = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $mobileUser->id . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('chat_attachments', $fileName, 'public');
                $attachmentUrl = Storage::url($path);
            }

            // Crear mensaje
            $message = ChatMessage::create([
                'order_id' => $orderId,
                'sender_type' => 'customer',
                'sender_id' => $mobileUser->id,
                'message' => $validated['message'],
                'attachment_url' => $attachmentUrl,
                'is_read' => false,
            ]);

            // TODO: Enviar notificación push al negocio (implementar después)

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado exitosamente',
                'data' => [
                    'message_id' => $message->message_id,
                    'sender_type' => $message->sender_type,
                    'message' => $message->message,
                    'attachment_url' => $message->attachment_url,
                    'created_at' => $message->created_at->toIso8601String(),
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error sending chat message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar mensaje: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark messages as read
     * PUT /api/orders/{order}/messages/mark-read
     */
    public function markAsRead(Request $request, $orderId)
    {
        try {
            $deviceId = $request->header('X-Device-ID');

            if (!$deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device ID es requerido en el header X-Device-ID',
                ], 400);
            }

            // Verificar que el dispositivo existe
            $mobileUser = MobileUser::where('device_id', $deviceId)->first();

            if (!$mobileUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dispositivo no encontrado',
                ], 404);
            }

            // Verificar que la orden existe y está ligada a este dispositivo
            $order = Order::where('order_id', $orderId)
                ->where('mobile_user_id', $mobileUser->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden no encontrada o no pertenece a este dispositivo',
                ], 404);
            }

            // Marcar todos los mensajes del negocio como leídos
            $updatedCount = ChatMessage::forOrder($orderId)
                ->bySenderType('business')
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Mensajes marcados como leídos',
                'data' => [
                    'messages_marked' => $updatedCount,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking messages as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar mensajes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all orders linked to a mobile user with unread message count
     * GET /api/mobile/orders
     */
    public function getOrdersForMobileUser(Request $request)
    {
        try {
            $deviceId = $request->header('X-Device-ID');

            if (!$deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device ID es requerido en el header X-Device-ID',
                ], 400);
            }

            // Verificar que el dispositivo existe
            $mobileUser = MobileUser::where('device_id', $deviceId)->first();

            if (!$mobileUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dispositivo no encontrado',
                ], 404);
            }

            // Obtener órdenes del usuario con conteo de mensajes no leídos
            $orders = Order::where('mobile_user_id', $mobileUser->id)
                ->with('business:business_id,business_name,logo_url,phone')
                ->withCount([
                    'chatMessages as unread_messages_count' => function ($query) {
                        $query->where('sender_type', 'business')->where('is_read', false);
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    return [
                        'order_id' => $order->order_id,
                        'folio_number' => $order->folio_number,
                        'pickup_token' => $order->pickup_token,
                        'description' => $order->description,
                        'status' => $order->status,
                        'created_at' => $order->created_at->toIso8601String(),
                        'business' => [
                            'business_id' => $order->business->business_id,
                            'business_name' => $order->business->business_name,
                            'logo_url' => $order->business->logo_url,
                            'phone' => $order->business->phone,
                        ],
                        'unread_messages_count' => $order->unread_messages_count ?? 0,
                        'has_unread_messages' => ($order->unread_messages_count ?? 0) > 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'total' => $orders->count(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting orders for mobile user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener órdenes: ' . $e->getMessage(),
            ], 500);
        }
    }
}
