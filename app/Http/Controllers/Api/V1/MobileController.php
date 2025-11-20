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

        $user = $request->user('sanctum');
        $mobileUser = $request->mobile_user ?? null;

        $order = Order::where('qr_token', $validated['qr_token'])
            ->whereNull('user_id') // Solo órdenes no asociadas a un usuario
            ->whereNull('mobile_user_id') // Y no asociadas a un dispositivo
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'QR code invalid, expired, or order already associated',
            ], 404);
        }

        // Asociar según el método de autenticación
        if ($user) {
            // Usuario autenticado: asociar a user_id
            $order->user_id = $user->id;
            $order->mobile_user_id = $mobileUser ? $mobileUser->id : null; // Guardar también mobile_user_id como backup
            $order->associated_at = now();
            $order->save();

            \Log::info('Order associated to authenticated user', [
                'order_id' => $order->order_id,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } else if ($mobileUser) {
            // Sin autenticación: asociar solo a mobile_user_id (sistema antiguo)
            $order->mobile_user_id = $mobileUser->id;
            $order->associated_at = now();
            $order->save();

            \Log::info('Order associated to device without auth', [
                'order_id' => $order->order_id,
                'mobile_user_id' => $mobileUser->id,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación o device_id',
            ], 401);
        }

        // Enviar notificación de orden asociada
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
        $user = $request->user('sanctum'); // Usuario autenticado (si existe)
        $mobileUser = $request->mobile_user ?? null; // Dispositivo del middleware

        // IMPORTANTE: Priorizar usuario autenticado sobre mobile_user_id
        if ($user) {
            // Usuario autenticado: filtrar por user_id
            $query = Order::where('user_id', $user->id);
            \Log::info('Fetching orders for authenticated user', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        } else if ($mobileUser) {
            // Sin autenticación: filtrar por mobile_user_id (sistema antiguo)
            $query = Order::where('mobile_user_id', $mobileUser->id);
            \Log::info('Fetching orders for device', [
                'mobile_user_id' => $mobileUser->id
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación o device_id',
            ], 401);
        }

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
        $user = $request->user('sanctum');
        $mobileUser = $request->mobile_user ?? null;

        // Buscar la orden con verificación de propiedad
        $query = Order::where('order_id', $orderId);

        if ($user) {
            // Usuario autenticado: verificar que sea del usuario
            $query->where('user_id', $user->id);
        } else if ($mobileUser) {
            // Sin autenticación: verificar que sea del dispositivo
            $query->where('mobile_user_id', $mobileUser->id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación o device_id',
            ], 401);
        }

        $order = $query->with(['items', 'statusHistory'])->first();

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
