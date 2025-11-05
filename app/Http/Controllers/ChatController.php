<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display chat interface with active orders
     */
    public function index(Request $request)
    {
        $business = Auth::user();

        // Check if business has chat module enabled
        if (!$business->has_chat_module) {
            return redirect()
                ->route('business.dashboard.index')
                ->with('error', 'El módulo de chat no está activado para tu cuenta.');
        }

        // Get active orders for today
        $activeOrders = Order::where('business_id', $business->business_id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['pending', 'in_progress', 'ready'])
            ->with('business')
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
     * Get messages for a specific order (API endpoint for future integration)
     */
    public function getMessages(Request $request, $orderId)
    {
        $business = Auth::user();

        $order = Order::where('business_id', $business->business_id)
            ->where('order_id', $orderId)
            ->firstOrFail();

        // TODO: Replace with actual chat messages from database
        $messages = [
            [
                'id' => 1,
                'sender' => 'customer',
                'message' => '¿Cuánto tiempo falta para mi orden?',
                'created_at' => now()->subMinutes(5)->format('H:i'),
            ],
            [
                'id' => 2,
                'sender' => 'business',
                'message' => 'Tu orden estará lista en aproximadamente 10 minutos.',
                'created_at' => now()->subMinutes(4)->format('H:i'),
            ],
            [
                'id' => 3,
                'sender' => 'customer',
                'message' => 'Perfecto, gracias!',
                'created_at' => now()->subMinutes(3)->format('H:i'),
            ],
        ];

        return response()->json(['messages' => $messages]);
    }

    /**
     * Send a message (API endpoint for future integration)
     */
    public function sendMessage(Request $request, $orderId)
    {
        $business = Auth::user();

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $order = Order::where('business_id', $business->business_id)
            ->where('order_id', $orderId)
            ->firstOrFail();

        // TODO: Save message to database and send via WebSocket/notification

        return response()->json([
            'success' => true,
            'message' => [
                'id' => rand(100, 999),
                'sender' => 'business',
                'message' => $validated['message'],
                'created_at' => now()->format('H:i'),
            ],
        ]);
    }
}
