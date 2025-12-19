<?php

/**
 * Company: CETAM
 * Project: FF
 * File: OrderController.php
 * Created on: 04/11/2025
 * Created by: Dafne Vanessa Castillo Moreno
 * Approved by: Dafne Vanessa Castillo Moreno
 *
 * Changelog:
 * - ID: 1 | Modified on: 04/11/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreno |
 *   Description: Controller for order management |
 */

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
    public function index(Request $request): View
    {
        $businessId = Auth::id();

        $status = $request->get('status');
        $query = Order::where('business_id', $businessId)
            ->withTrashed()
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
    public function create(): View
    {
        return view('orders.create');
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_folio' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'mobile_user_id' => 'nullable|integer',
        ], [
            'business_folio.required' => 'El campo folio del negocio es obligatorio.',
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
    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the order
     */
    public function edit(Order $order): View
    {
        $this->authorize('update', $order);

        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'business_folio' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ], [
            'business_folio.required' => 'El campo folio del negocio es obligatorio.',
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
    public function markAsReady(Order $order): RedirectResponse
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
    public function markAsDelivered(Request $request, Order $order): RedirectResponse
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
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ], [
            'cancellation_reason.required' => 'El motivo de cancelación es obligatorio.',
            'cancellation_reason.max' => 'El motivo de cancelación no puede exceder 500 caracteres.',
        ]);

        // Validate that cancellation_reason is not only whitespace
        if (empty(trim($validated['cancellation_reason']))) {
            return back()->withInput()->withErrors(['cancellation_reason' => 'El motivo de cancelación no puede estar vacío.']);
        }

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
    public function downloadQr(Order $order): BinaryFileResponse|RedirectResponse
    {
        $this->authorize('view', $order);

        // 5.4.1: Early Return - Guard Clause
        if (!$order->qr_code_url) {
            return back()->with('error', 'Esta orden no tiene código QR');
        }

        $filePath = public_path(str_replace('/storage/', 'storage/', $order->qr_code_url));

        // 5.4.1: Early Return - Guard Clause
        if (!file_exists($filePath)) {
            return back()->with('error', 'Archivo de código QR no encontrado');
        }

        return response()->download($filePath, "orden_{$order->folio_number}_qr.png");
    }

    /**
     * Get order statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $businessId = Auth::id();
        $days = $request->get('days', 30);

        $stats = $this->orderService->getOrderStatistics($businessId, $days);

        return response()->json($stats);
    }

    /**
     * Check if order is linked to mobile user
     */
    public function checkLinked(Order $order): JsonResponse
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
    public function associateOrder(Request $request, string $qr_token): JsonResponse|View
    {
        try {
            // Find order by QR token
            $order = Order::where('qr_token', $qr_token)
                ->whereIn('status', ['pending'])
                ->first();

            $isApiRequest = $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

            // 5.4.1: Early Return - Invalid order
            if (!$order) {
                return $this->handleInvalidOrder($isApiRequest);
            }

            // 5.4.1: Early Return - Already associated
            if ($order->mobile_user_id) {
                return $this->handleAlreadyAssociated($order, $isApiRequest);
            }

            // Associate order with mobile user
            $mobileUser = $this->getOrCreateMobileUser($request);
            $this->associateOrderToUser($order, $mobileUser);

            // Return appropriate response
            return $this->handleSuccessfulAssociation($order, $mobileUser, $isApiRequest);

        } catch (\Exception $e) {
            Log::error('Error associating order', [
                'qr_token' => $qr_token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->handleAssociationError($request->expectsJson());
        }
    }

    /**
     * Handle invalid order response
     */
    private function handleInvalidOrder(bool $isApiRequest): JsonResponse|View
    {
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

    /**
     * Handle already associated order response
     */
    private function handleAlreadyAssociated(Order $order, bool $isApiRequest): JsonResponse|View
    {
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

    /**
     * Get or create mobile user from request headers
     */
    private function getOrCreateMobileUser(Request $request): \App\Models\MobileUser
    {
        $deviceId = $request->header('X-Device-ID', 'WEB_SYSTEM');
        $deviceType = $request->header('X-Device-Type', 'web');
        $deviceModel = $request->header('X-Device-Model', 'Browser');
        $osVersion = $request->header('X-OS-Version', 'Web');
        $appVersion = $request->header('X-App-Version', '1.0.0');

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

        $mobileUser->touch('last_seen_at');

        return $mobileUser;
    }

    /**
     * Associate order with mobile user
     */
    private function associateOrderToUser(Order $order, \App\Models\MobileUser $mobileUser): void
    {
        $order->mobile_user_id = $mobileUser->id;
        $order->associated_at = now();
        $order->save();
    }

    /**
     * Handle successful association response
     */
    private function handleSuccessfulAssociation(Order $order, \App\Models\MobileUser $mobileUser, bool $isApiRequest): JsonResponse|View
    {
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

        return view('orders.associate-success', [
            'order' => $order,
            'message' => '¡Orden tomada exitosamente!'
        ]);
    }

    /**
     * Handle association error response
     */
    private function handleAssociationError(bool $expectsJson): JsonResponse|View
    {
        if ($expectsJson) {
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
