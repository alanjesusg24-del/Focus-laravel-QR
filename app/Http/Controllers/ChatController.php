<?php

/**
 * ============================================
 * CETAM - Chat Controller
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        ChatController.php
 * @description Controlador de mensajería y chat en tiempo real
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ChatMessage;
use App\Models\MobileUser;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Display chat interface with active orders
     */
    public function index(Request $request)
    {
        $business = Auth::user();

        // Check if business has chat module enabled through their plan
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
    public function getMessages(Request $request, $orderId)
    {
        $business = Auth::user();

        $order = Order::where('business_id', $business->business_id)
            ->where('order_id', $orderId)
            ->firstOrFail();

        // Get all messages for this order
        $messages = ChatMessage::forOrder($orderId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'message_id' => $message->message_id,
                    'sender' => $message->sender_type === 'business' ? 'business' : 'customer',
                    'message' => $message->message,
                    'attachment_url' => $message->attachment_url,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at->format('H:i'),
                    'full_date' => $message->created_at->toIso8601String(),
                ];
            });

        // Mark customer messages as read
        ChatMessage::forOrder($orderId)
            ->bySenderType('customer')
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message from business to customer
     */
    public function sendMessage(Request $request, $orderId)
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

        // Verificar que la orden esté ligada a un dispositivo
        if (!$order->mobile_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Esta orden no está ligada a ningún dispositivo móvil',
            ], 400);
        }

        try {
            // Manejar archivo adjunto si existe
            $attachmentUrl = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_business_' . $business->business_id . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('chat_attachments', $fileName, 'public');
                $attachmentUrl = Storage::url($path);
            }

            // Crear mensaje
            $message = ChatMessage::create([
                'order_id' => $orderId,
                'sender_type' => 'business',
                'sender_id' => $business->business_id,
                'message' => $validated['message'],
                'attachment_url' => $attachmentUrl,
                'is_read' => false,
            ]);

            // Enviar notificación push al cliente
            $mobileUser = MobileUser::find($order->mobile_user_id);
            if ($mobileUser && $mobileUser->fcm_token) {
                PushNotificationService::sendChatMessage(
                    $mobileUser->fcm_token,
                    $order,
                    $validated['message']
                );
            }

            return response()->json([
                'success' => true,
                'message' => [
                    'message_id' => $message->message_id,
                    'sender' => 'business',
                    'message' => $message->message,
                    'attachment_url' => $message->attachment_url,
                    'created_at' => $message->created_at->format('H:i'),
                    'full_date' => $message->created_at->toIso8601String(),
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error sending business chat message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar mensaje: ' . $e->getMessage(),
            ], 500);
        }
    }
}
