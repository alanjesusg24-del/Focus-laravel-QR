<?php

/**
 * ============================================
 * CETAM - Order Controller
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        OrderController.php
 * @description Controlador CRUD de órdenes con sistema QR
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    protected OrderService $orderService;

    /**
     * Create a new controller instance
     *
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders for the authenticated business
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $businessId = Auth::id();
        $status = $request->get('status');

        $query = Order::where('business_id', $businessId)
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $perPage = config('cetam.cs.pagination.per_page', 15);
        $orders = $query->paginate($perPage);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     *
     * @return View
     */
    public function create(): View
    {
        return view('orders.create');
    }

    /**
     * Store a newly created order
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'mobile_user_id' => 'nullable|integer',
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
     *
     * @param Order $order
     * @return View
     */
    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the order
     *
     * @param Order $order
     * @return View
     */
    public function edit(Order $order): View
    {
        $this->authorize('update', $order);

        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     *
     * @param Request $request
     * @param Order $order
     * @return RedirectResponse
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $order->update($validated);

        return redirect()
            ->route('business.orders.show', $order->order_id)
            ->with('success', 'Orden actualizada exitosamente');
    }

    /**
     * Mark order as ready
     *
     * @param Order $order
     * @return RedirectResponse
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
     *
     * @param Request $request
     * @param Order $order
     * @return RedirectResponse
     */
    public function markAsDelivered(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'pickup_token' => 'required|string',
        ]);

        try {
            $this->orderService->markAsDelivered($order, $validated['pickup_token']);

            return back()->with('success', 'Orden entregada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel the specified order
     *
     * @param Request $request
     * @param Order $order
     * @return RedirectResponse
     */
    public function cancel(Request $request, Order $order): RedirectResponse
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
     *
     * @param Order $order
     * @return BinaryFileResponse|RedirectResponse
     */
    public function downloadQr(Order $order): BinaryFileResponse|RedirectResponse
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
     *
     * @param Request $request
     * @return JsonResponse
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
     *
     * @param Order $order
     * @return JsonResponse
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
}
