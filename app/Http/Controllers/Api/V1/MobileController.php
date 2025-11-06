<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use App\Models\Order;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    /**
     * Registrar o actualizar un dispositivo móvil
     * POST /api/v1/mobile/register
     */
    public function registerDevice(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'fcm_token' => 'nullable|string',
            'device_type' => 'required|in:android,ios',
            'device_model' => 'nullable|string',
            'os_version' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        $mobileUser = MobileUser::updateOrCreate(
            ['device_id' => $validated['device_id']],
            array_merge($validated, ['last_seen_at' => now()])
        );

        return response()->json([
            'success' => true,
            'message' => 'Device registered successfully',
            'data' => $mobileUser,
        ], 200);
    }

    /**
     * Asociar una orden con el dispositivo mediante QR
     * POST /api/v1/mobile/orders/associate
     */
    public function associateOrder(Request $request)
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
        ]);

        $order = Order::where('qr_token', $validated['qr_token'])->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'QR code invalid or expired',
            ], 404);
        }

        // Si ya está asociada a otro dispositivo
        if ($order->mobile_user_id && $order->mobile_user_id !== $request->mobile_user->id) {
            return response()->json([
                'success' => false,
                'message' => 'This order is already associated with another device',
            ], 409);
        }

        // Asociar orden con el dispositivo
        $order->update([
            'mobile_user_id' => $request->mobile_user->id,
            'associated_at' => now(),
        ]);

        // Enviar notificación de orden asociada
        $mobileUser = $request->mobile_user;
        if ($mobileUser && $mobileUser->fcm_token) {
            PushNotificationService::sendOrderAssociated(
                $mobileUser->fcm_token,
                $order
            );
        }

        // Cargar relaciones
        $order->load('items', 'statusHistory');

        // Asegurar que statusHistory nunca sea null
        if (!$order->statusHistory) {
            $order->statusHistory = [];
        }

        return response()->json([
            'success' => true,
            'message' => 'Order associated successfully',
            'data' => $order,
        ], 200);
    }

    /**
     * Obtener órdenes del dispositivo
     * GET /api/v1/mobile/orders?status=pending&page=1&per_page=20
     */
    public function getOrders(Request $request)
    {
        $query = Order::where('mobile_user_id', $request->mobile_user->id);

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar por más recientes
        $query->orderBy('created_at', 'desc');

        // Paginación
        $perPage = $request->get('per_page', 20);
        $orders = $query->with('items')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'total_pages' => $orders->lastPage(),
                    'total_items' => $orders->total(),
                    'per_page' => $orders->perPage(),
                ],
            ],
        ], 200);
    }

    /**
     * Obtener detalle de una orden
     * GET /api/v1/mobile/orders/{orderId}
     */
    public function getOrderDetail(Request $request, $orderId)
    {
        $order = Order::where('order_id', $orderId)
                      ->where('mobile_user_id', $request->mobile_user->id)
                      ->with(['items', 'statusHistory'])
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        // Asegurar que statusHistory nunca sea null
        if (!$order->statusHistory) {
            $order->statusHistory = [];
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ], 200);
    }

    /**
     * Actualizar token FCM del dispositivo
     * PUT /api/v1/mobile/update-token
     */
    public function updateFcmToken(Request $request)
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->mobile_user->update([
            'fcm_token' => $validated['fcm_token'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully',
        ], 200);
    }

    /**
     * Validar pickup token y marcar orden como entregada (Scanner QR desde Dashboard)
     * POST /api/v1/scanner/validate-delivery
     */
    public function validateDelivery(Request $request)
    {
        $validated = $request->validate([
            'pickup_token' => 'required|string',
        ]);

        $token = $validated['pickup_token'];

        // Intentar buscar por pickup_token primero
        $order = Order::where('pickup_token', $token)->first();

        // Si no se encuentra, intentar con qr_token
        if (!$order) {
            $order = Order::where('qr_token', $token)->first();
        }

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o no encontrado',
            ], 404);
        }

        // Verificar que la orden esté lista para ser entregada
        if ($order->status !== 'ready') {
            return response()->json([
                'success' => false,
                'message' => 'La orden no está lista para ser entregada. Estado actual: ' . $order->status,
                'current_status' => $order->status,
                'order_number' => $order->order_number,
            ], 400);
        }

        // Marcar como entregada
        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        // Registrar en historial
        $order->statusHistory()->create([
            'order_id' => $order->order_id,
            'old_status' => 'ready',
            'new_status' => 'delivered',
            'notes' => 'Entregada mediante escaneo de QR del cliente',
            'changed_by' => $order->business_id,
        ]);

        // Enviar notificación al cliente
        $mobileUser = $order->mobileUser;
        if ($mobileUser && $mobileUser->fcm_token) {
            PushNotificationService::sendOrderDelivered(
                $mobileUser->fcm_token,
                $order
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden entregada exitosamente',
            'data' => [
                'order_id' => $order->order_id,
                'folio_number' => $order->folio_number,
                'status' => $order->status,
                'delivered_at' => $order->delivered_at,
            ],
        ], 200);
    }
}
