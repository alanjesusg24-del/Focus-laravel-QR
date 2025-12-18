<?php

/**
 * Company: CETAM
 * Project: FF
 * File: MobileController.php
 * Created on: 04/12/2025
 * Created by: Dafne VAnessa CAstillo Moreno
 * Approved by: EAlan Jesus Garcia Nava
 *
 * Changelog:
 * - ID: 1 | Modified on: 04/12/2025 |
 *   Modified by: Alan Jesus Garcia Nava|
 *   Description: Controller for mobile device operations |
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use App\Models\Order;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MobileController extends Controller
{
    /**
     * Registrar o actualizar un dispositivo móvil
     * POST /api/v1/mobile/register
     */
    public function registerDevice(Request $request): JsonResponse
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
    public function associateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
        ]);

        $user = $request->user('sanctum');
        $mobileUser = $request->mobile_user;

        $order = Order::where('qr_token', $validated['qr_token'])
            ->whereNull('user_id') // Solo órdenes no asociadas a un usuario
            ->whereNull('mobile_user_id') // Y no asociadas a un dispositivo
            ->first();

        // 5.4.1: Early Return - Guard Clause
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'QR code invalid, expired, or order already associated',
            ], 404);
        }

        // 5.4.1: Early Return - Guard Clause para autenticación
        if (!$user && !$mobileUser) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación o device_id',
            ], 401);
        }

        // Asociar según el método de autenticación
        $mobileUserId = $user?->id ?? $mobileUser->id;
        $order->mobile_user_id = $mobileUserId;
        $order->associated_at = now();
        $order->save();

        // Log según tipo de autenticación
        if ($user) {
            Log::info('Order associated to authenticated mobile user', [
                'order_id' => $order->order_id,
                'mobile_user_id' => $user->id,
                'email' => $user->email,
            ]);
        } else {
            Log::info('Order associated to device without auth', [
                'order_id' => $order->order_id,
                'mobile_user_id' => $mobileUser->id,
            ]);
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
    public function getOrders(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');
        $mobileUser = $request->mobile_user;

        // 5.4.1: Early Return - Guard Clause para autenticación
        if (!$user && !$mobileUser) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación o device_id',
            ], 401);
        }

        // Determinar mobile_user_id y construir query
        $mobileUserId = $user?->id ?? $mobileUser->id;
        $query = Order::where('mobile_user_id', $mobileUserId);

        // Log según tipo de autenticación
        if ($user) {
            Log::info('Fetching orders for authenticated mobile user', [
                'mobile_user_id' => $user->id,
                'email' => $user->email
            ]);
        } else {
            Log::info('Fetching orders for device', [
                'mobile_user_id' => $mobileUser->id
            ]);
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
    public function getOrderDetail(Request $request, int $orderId): JsonResponse
    {
        $user = $request->user('sanctum');
        $mobileUser = $request->mobile_user;

        // 5.4.1: Early Return - Guard Clause para autenticación
        if (!$user && !$mobileUser) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación o device_id',
            ], 401);
        }

        // Determinar mobile_user_id
        $mobileUserId = $user?->id ?? $mobileUser->id;

        // Buscar la orden con verificación de propiedad
        $order = Order::where('order_id', $orderId)
            ->where('mobile_user_id', $mobileUserId)
            ->with(['items', 'statusHistory'])
            ->first();

        // 5.4.1: Early Return - Guard Clause
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
    public function updateFcmToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
            'platform' => 'nullable|string|in:ios,android',
        ]);

        $user = $request->user('sanctum');
        $mobileUser = $request->mobile_user;

        // 5.1.3: Null coalescing operator en lugar de ternario anidado
        $mobileUserId = $user?->id ?? $mobileUser?->id;

        // 5.4.1: Early Return - Guard Clause
        if (!$mobileUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación o device_id',
            ], 401);
        }

        // Crear o actualizar dispositivo
        $device = \App\Models\MobileDevice::updateOrCreate(
            [
                'mobile_user_id' => $mobileUserId,
                'fcm_token' => $validated['fcm_token'],
            ],
            [
                'platform' => $validated['platform'] ?? 'android',
                'is_active' => true,
            ]
        );

        Log::info('FCM token updated', [
            'mobile_user_id' => $mobileUserId,
            'device_id' => $device->mobile_device_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully',
            'device_id' => $device->mobile_device_id,
        ], 200);
    }

    /**
     * Validar pickup token y marcar orden como entregada (Scanner QR desde Dashboard)
     * POST /api/v1/scanner/validate-delivery
     */
    public function validateDelivery(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pickup_token' => 'required|string',
        ]);

        $token = $validated['pickup_token'];

        // Intentar buscar por pickup_token primero
        $order = Order::where('pickup_token', $token)->first();

        // 5.4.1: Early Return - Si no se encuentra, intentar con qr_token
        if (!$order) {
            $order = Order::where('qr_token', $token)->first();
        }

        // 5.4.1: Early Return - Guard Clause
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o no encontrado',
            ], 404);
        }

        // 5.4.1: Early Return - Guard Clause para verificar estado
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
