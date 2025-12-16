<?php

/**
 * Company: CETAM
 * Project: FF
 * File: ChatApiController.php
 * Created on: 04/10/2025
 * Created by: Alan Jesus Garcia Nava
 * Approved by: Alan Jesus Garcia Nava
 *
 * Changelog:
 * - ID: 1 | Modified on: 23/11/2025 |
 *   Modified by: Alan Jesus Garcia Nava |
 *   Description: API controller for chat messaging |
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\MobileUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatApiController extends Controller
{
    /**
     * Get messages for a specific order
     * GET /api/orders/{order}/messages
     */
    public function getMessages(Request $request, int $orderId): JsonResponse
    {
        try {
            // 5.4.1: Early Return - Validate authentication
            $mobileUserId = $this->getMobileUserId($request);
            if (!$mobileUserId) {
                return $this->unauthorizedResponse();
            }

            // 5.4.1: Early Return - Validate order access
            $order = $this->getOrderForUser($orderId, $mobileUserId);
            if (!$order) {
                return $this->orderNotFoundResponse();
            }

            // Get and format messages
            $messages = $this->getFormattedMessages($orderId);

            // Mark business messages as read
            $this->markBusinessMessagesAsRead($orderId);

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
    public function sendMessage(Request $request, int $orderId): JsonResponse
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
            ]);

            // 5.4.1: Early Return - Validate authentication
            $mobileUserId = $this->getMobileUserId($request);
            if (!$mobileUserId) {
                return $this->unauthorizedResponse();
            }

            // 5.4.1: Early Return - Validate order access
            $order = $this->getOrderForUser($orderId, $mobileUserId, ['business']);
            if (!$order) {
                return $this->orderNotFoundResponse();
            }

            // Handle attachment upload
            $attachmentUrl = $this->handleAttachmentUpload($request, $mobileUserId);

            // Create message
            $message = ChatMessage::create([
                'order_id' => $orderId,
                'sender_type' => 'customer',
                'sender_id' => $mobileUserId,
                'message' => $validated['message'],
                'attachment_url' => $attachmentUrl,
                'is_read' => false,
            ]);

            // TODO: Send push notification to business

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
    public function markAsRead(Request $request, int $orderId): JsonResponse
    {
        try {
            // 5.4.1: Early Return - Validate authentication
            $mobileUserId = $this->getMobileUserId($request);
            if (!$mobileUserId) {
                return $this->unauthorizedResponse();
            }

            // 5.4.1: Early Return - Validate order access
            $order = $this->getOrderForUser($orderId, $mobileUserId);
            if (!$order) {
                return $this->orderNotFoundResponse();
            }

            // Mark all business messages as read
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
    public function getOrdersForMobileUser(Request $request): JsonResponse
    {
        try {
            $deviceId = $request->header('X-Device-ID');

            // 5.4.1: Early Return - Validate device ID
            if (!$deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device ID es requerido en el header X-Device-ID',
                ], 400);
            }

            // 5.4.1: Early Return - Validate mobile user exists
            $mobileUser = MobileUser::where('device_id', $deviceId)->first();
            if (!$mobileUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dispositivo no encontrado',
                ], 404);
            }

            // Get orders with unread message count
            $orders = $this->getOrdersWithUnreadCount($mobileUser->id);

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

    /**
     * Get mobile user ID from request (authenticated user or device)
     * 5.1.3: Using null coalescing operator instead of nested ternary
     */
    private function getMobileUserId(Request $request): ?int
    {
        $user = $request->user('sanctum');
        $mobileUser = $request->mobile_user;

        return $user?->id ?? $mobileUser?->id;
    }

    /**
     * Get order for authenticated user
     */
    private function getOrderForUser(int $orderId, int $mobileUserId, array $with = []): ?Order
    {
        return Order::where('order_id', $orderId)
            ->where('mobile_user_id', $mobileUserId)
            ->when(!empty($with), fn($query) => $query->with($with))
            ->first();
    }

    /**
     * Get formatted messages for order
     */
    private function getFormattedMessages(int $orderId)
    {
        return ChatMessage::forOrder($orderId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn(ChatMessage $message) => [
                'message_id' => $message->message_id,
                'sender_type' => $message->sender_type,
                'message' => $message->message,
                'attachment_url' => $message->attachment_url,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at->toIso8601String(),
                'read_at' => $message->read_at?->toIso8601String(),
            ]);
    }

    /**
     * Mark business messages as read for order
     */
    private function markBusinessMessagesAsRead(int $orderId): void
    {
        ChatMessage::forOrder($orderId)
            ->bySenderType('business')
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Handle attachment file upload
     */
    private function handleAttachmentUpload(Request $request, int $mobileUserId): ?string
    {
        if (!$request->hasFile('attachment')) {
            return null;
        }

        $file = $request->file('attachment');
        $fileName = time() . '_' . $mobileUserId . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('chat_attachments', $fileName, 'public');

        return Storage::url($path);
    }

    /**
     * Get orders with unread message count for mobile user
     */
    private function getOrdersWithUnreadCount(int $mobileUserId)
    {
        return Order::where('mobile_user_id', $mobileUserId)
            ->with('business:business_id,business_name,logo_url,phone')
            ->withCount([
                'chatMessages as unread_messages_count' => fn($query) =>
                    $query->where('sender_type', 'business')->where('is_read', false)
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(Order $order) => [
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
            ]);
    }

    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Se requiere autenticación o device_id',
        ], 401);
    }

    /**
     * Return order not found response
     */
    private function orderNotFoundResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Orden no encontrada o no tienes acceso a ella',
        ], 404);
    }
}
