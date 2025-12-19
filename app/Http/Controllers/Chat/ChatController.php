<?php

/**
 * Company: CETAM
 * Project: FF
 * File: ChatController.php
 * Created on: 20/11/2025
 * Created by: Dafne Vanessa Castillo Moreno
 * Approved by: Alan Jesus Garcia Nava
 *
 * Changelog:
 * - ID: 1 | Modified on: 15/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreno |
 *   Description: Refactored to comply with CETAM Point 5 standards |
 */

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ChatMessage;
use App\Models\MobileUser;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class ChatController extends Controller
{
    /**
     * Display chat interface with active orders
     */
    public function index(Request $request): View|RedirectResponse
    {
        $business = Auth::user();

        // 5.4.1: Early Return - Chat module not enabled
        if (!$business->plan || !$business->plan->has_chat_module) {
            return redirect()
                ->route('business.dashboard.index')
                ->with('error', 'El módulo de chat no está activado para tu cuenta.');
        }

        // Get active orders that have been linked to a mobile device
        $activeOrders = Order::where('business_id', $business->business_id)
            ->whereNotNull('mobile_user_id')
            ->whereIn('status', ['pending', 'ready'])
            ->with('business')
            ->withCount([
                'chatMessages as unread_messages_count' => function ($query) {
                    $query->where('sender_type', 'customer')->where('is_read', false);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get selected order if provided
        $selectedOrder = null;
        if ($request->has('order_id')) {
            $selectedOrder = Order::where('business_id', $business->business_id)
                ->where('order_id', $request->order_id)
                ->first();
        }

        return view('chat.index', compact('activeOrders', 'selectedOrder', 'business'));
    }

    /**
     * Get messages for a specific order (API endpoint for web chat)
     */
    public function getMessages(Request $request, int $orderId): JsonResponse
    {
        $business = Auth::user();

        $order = Order::where('business_id', $business->business_id)
            ->where('order_id', $orderId)
            ->firstOrFail();

        // 5.3.1: Using Collection with arrow function
        $messages = ChatMessage::forOrder($orderId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn(ChatMessage $message) => $this->formatMessageForResponse($message));

        // 5.5: Extract to private method for SRP
        $this->markCustomerMessagesAsRead($orderId);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message from business to customer
     */
    public function sendMessage(Request $request, int $orderId): JsonResponse
    {
        $business = Auth::user();

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $order = Order::where('business_id', $business->business_id)
            ->where('order_id', $orderId)
            ->with('business')
            ->firstOrFail();

        // 5.4.1: Early Return - Order not linked to mobile device
        if (!$order->mobile_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Esta orden no está ligada a ningún dispositivo móvil',
            ], 400);
        }

        try {
            // 5.5: Extract file upload to private method
            $attachmentUrl = $this->handleAttachmentUpload($request, $business->business_id);

            // Crear mensaje
            $message = ChatMessage::create([
                'order_id' => $orderId,
                'sender_type' => 'business',
                'sender_id' => $business->business_id,
                'message' => $validated['message'],
                'attachment_url' => $attachmentUrl,
                'is_read' => false,
            ]);

            // 5.5: Extract push notification to private method
            $this->sendPushNotificationToCustomer($order, $validated['message']);

            return response()->json([
                'success' => true,
                'message' => $this->formatMessageForResponse($message),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error sending business chat message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar mensaje: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format chat message for API response
     * 5.5: Private helper method following SRP - Eliminates code duplication
     */
    private function formatMessageForResponse(ChatMessage $message): array
    {
        return [
            'message_id' => $message->message_id,
            'sender' => $message->sender_type === 'business' ? 'business' : 'customer',
            'message' => $message->message,
            'attachment_url' => $message->attachment_url,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at->format('H:i'),
            'full_date' => $message->created_at->toIso8601String(),
        ];
    }

    /**
     * Mark customer messages as read for an order
     * 5.5: Private helper method following SRP
     */
    private function markCustomerMessagesAsRead(int $orderId): void
    {
        ChatMessage::forOrder($orderId)
            ->bySenderType('customer')
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Handle file attachment upload
     * 5.5: Private helper method following SRP
     */
    private function handleAttachmentUpload(Request $request, int $businessId): ?string
    {
        // 5.4.1: Early Return - No attachment
        if (!$request->hasFile('attachment')) {
            return null;
        }

        $file = $request->file('attachment');
        $fileName = time() . '_business_' . $businessId . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('chat_attachments', $fileName, 'public');

        return Storage::url($path);
    }

    /**
     * Send push notification to customer
     * 5.5: Private helper method following SRP
     */
    private function sendPushNotificationToCustomer(Order $order, string $message): void
    {
        $mobileUser = MobileUser::find($order->mobile_user_id);

        // 5.4.1: Early Return - No mobile user or no FCM token
        if (!$mobileUser || !$mobileUser->fcm_token) {
            return;
        }

        PushNotificationService::sendChatMessage(
            $mobileUser->fcm_token,
            $order,
            $message
        );
    }
}
