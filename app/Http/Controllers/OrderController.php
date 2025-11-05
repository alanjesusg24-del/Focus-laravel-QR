<?php

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
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20);

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
            'description' => 'required|string|max:500',
            'mobile_user_id' => 'nullable|integer',
        ]);

        try {
            $businessId = Auth::id();
            $order = $this->orderService->createOrder($businessId, $validated);

            return redirect()
                ->route('business.orders.show', $order->order_id)
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
            'description' => 'required|string|max:500',
        ]);

        $order->update($validated);

        return redirect()
            ->route('business.orders.show', $order->order_id)
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
}
