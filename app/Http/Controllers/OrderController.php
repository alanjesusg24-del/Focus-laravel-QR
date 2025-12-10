<?php

/**
 * ============================================
 * CETAM - Order Controller
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        OrderController.php
 * @description Controlador de gestión de órdenes/pedidos
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders for the authenticated business
     */
    public function index(Request $request)
    {
        $businessId = Auth::id(); // Assuming business authentication

        $status = $request->get('status');
        $query = Order::where('business_id', $businessId)
            ->withTrashed() // Mostrar también órdenes eliminadas (soft deleted)
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(config('cetam.cs.pagination.per_page', 15));

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_folio' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'mobile_user_id' => 'nullable|integer',
        ], [
            'business_folio.max' => 'El folio del negocio no puede tener más de 100 caracteres.',
            'description.max' => 'La descripción no puede tener más de 500 caracteres.',
        ]);

        try {
            $businessId = Auth::id();
            $order = $this->orderService->createOrder($businessId, $validated);

            return redirect()
                ->route('business.orders.index')
                ->with('success', 'Orden creada exitosamente con código QR');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the order
     */
    public function edit(Order $order)
    {
        $this->authorize('update', $order);

        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'business_folio' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ], [
            'business_folio.max' => 'El folio del negocio no puede tener más de 100 caracteres.',
            'description.max' => 'La descripción no puede tener más de 500 caracteres.',
        ]);

        $order->update($validated);

        return redirect()
            ->route('business.orders.index')
            ->with('success', 'Orden actualizada exitosamente');
    }

    /**
     * Mark order as ready
     */
    public function markAsReady(Order $order)
    {
        $this->authorize('update', $order);

        try {
            $this->orderService->markAsReady($order);

            return back()->with('success', 'Orden marcada como lista. Notificación enviada.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark order as delivered
     */
    public function markAsDelivered(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        try {
            $this->orderService->markAsDelivered($order);

            return back()->with('success', 'Orden entregada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel the specified order
     */
    public function cancel(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        try {
            $this->orderService->cancelOrder($order, $validated['cancellation_reason']);

            return back()->with('success', 'Orden cancelada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download QR code
     */
    public function downloadQr(Order $order)
    {
        $this->authorize('view', $order);

        if (!$order->qr_code_url) {
            return back()->with('error', 'Esta orden no tiene código QR');
        }

        $filePath = public_path(str_replace('/storage/', 'storage/', $order->qr_code_url));

        if (!file_exists($filePath)) {
            return back()->with('error', 'Archivo de código QR no encontrado');
        }

        return response()->download($filePath, "orden_{$order->folio_number}_qr.png");
    }

    /**
     * Get order statistics
     */
    public function statistics(Request $request)
    {
        $businessId = Auth::id();
        $days = $request->get('days', 30);

        $stats = $this->orderService->getOrderStatistics($businessId, $days);

        return response()->json($stats);
    }

    /**
     * Check if order is linked to mobile user
     */
    public function checkLinked(Order $order)
    {
        $this->authorize('view', $order);

        return response()->json([
            'is_linked' => !is_null($order->mobile_user_id),
            'mobile_user_id' => $order->mobile_user_id,
            'associated_at' => $order->associated_at?->toIso8601String(),
        ]);
    }

    /**
     * Associate order via QR scan (public route - no auth required)
     * Works for both WEB browsers and MOBILE app
     *
     * - If request is from mobile app (Accept: application/json) -> Returns JSON
     * - If request is from web browser -> Shows success/error view
     */
    public function associateOrder(Request $request, string $qr_token)
    {
        try {
            // Find order by QR token
            $order = Order::where('qr_token', $qr_token)
                ->whereIn('status', ['pending'])
                ->first();

            $isApiRequest = $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

            // Error: Invalid or already associated order
            if (!$order) {
                if ($isApiRequest) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Código QR inválido o la orden ya fue asociada',
                        'error_code' => 'INVALID_QR'
                    ], 404);
                }

                return view('orders.associate-error', [
                    'message' => 'Código QR inválido o la orden ya fue asociada'
                ]);
            }

            // Check if already associated
            if ($order->mobile_user_id) {
                if ($isApiRequest) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Esta orden ya fue tomada anteriormente',
                        'order' => [
                            'order_id' => $order->order_id,
                            'folio_number' => $order->folio_number,
                            'description' => $order->description,
                            'status' => $order->status,
                            'associated_at' => $order->associated_at?->toIso8601String(),
                        ],
                        'already_associated' => true
                    ], 200);
                }

                return view('orders.associate-success', [
                    'order' => $order,
                    'message' => 'Esta orden ya fue tomada anteriormente'
                ]);
            }

            // Determine if request comes from mobile app or web
            $deviceId = $request->header('X-Device-ID', 'WEB_SYSTEM');
            $deviceType = $request->header('X-Device-Type', 'web');
            $deviceModel = $request->header('X-Device-Model', 'Browser');
            $osVersion = $request->header('X-OS-Version', 'Web');
            $appVersion = $request->header('X-App-Version', '1.0.0');

            // Get or create user (phantom for web, real for mobile app)
            $mobileUser = \App\Models\MobileUser::firstOrCreate(
                ['device_id' => $deviceId],
                [
                    'device_type' => $deviceType,
                    'device_model' => $deviceModel,
                    'os_version' => $osVersion,
                    'app_version' => $appVersion,
                    'is_active' => true,
                    'last_seen_at' => now(),
                ]
            );

            // Update last_seen_at if user already exists
            $mobileUser->touch('last_seen_at');

            // Associate order with mobile user
            $order->mobile_user_id = $mobileUser->id;
            $order->associated_at = now();
            $order->save();

            // Return JSON response for mobile app
            if ($isApiRequest) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Orden tomada exitosamente!',
                    'order' => [
                        'order_id' => $order->order_id,
                        'folio_number' => $order->folio_number,
                        'description' => $order->description,
                        'status' => $order->status,
                        'business_id' => $order->business_id,
                        'associated_at' => $order->associated_at->toIso8601String(),
                        'pickup_token' => $order->pickup_token,
                    ],
                    'mobile_user_id' => $mobileUser->id
                ], 200);
            }

            // Return web view for browser
            return view('orders.associate-success', [
                'order' => $order,
                'message' => '¡Orden tomada exitosamente!'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error associating order', [
                'qr_token' => $qr_token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al procesar la orden',
                    'error_code' => 'SERVER_ERROR'
                ], 500);
            }

            return view('orders.associate-error', [
                'message' => 'Ocurrió un error al procesar la orden. Por favor intenta nuevamente.'
            ]);
        }
    }
}
