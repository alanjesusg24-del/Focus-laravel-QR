<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected OrderService $orderService;
    protected PaymentService $paymentService;

    public function __construct(
        OrderService $orderService,
        PaymentService $paymentService
    ) {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    /**
     * Show business dashboard
     */
    public function index(Request $request)
    {
        $businessId = Auth::id();
        $business = Business::with('plan')->findOrFail($businessId);

        // Get order statistics
        $days = $request->get('days', 30);
        $orderStats = $this->orderService->getOrderStatistics($businessId, $days);

        // Get active orders
        $activeOrders = Order::where('business_id', $businessId)
            ->whereIn('status', ['pending', 'ready'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent orders
        $recentOrders = Order::where('business_id', $businessId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Payment info
        $paymentExpired = $this->paymentService->isPaymentExpired($business);
        $paymentStats = $this->paymentService->getPaymentStatistics($businessId);

        return view('dashboard.index', compact(
            'business',
            'orderStats',
            'activeOrders',
            'recentOrders',
            'paymentExpired',
            'paymentStats',
            'days'
        ));
    }

    /**
     * Show analytics page
     */
    public function analytics(Request $request)
    {
        $businessId = Auth::id();
        $period = $request->get('period', 'month');

        $days = match ($period) {
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 30,
        };

        $orderStats = $this->orderService->getOrderStatistics($businessId, $days);

        // Get daily order count for charts
        $dailyOrders = Order::where('business_id', $businessId)
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status distribution
        $statusDistribution = Order::where('business_id', $businessId)
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('dashboard.analytics', compact(
            'orderStats',
            'dailyOrders',
            'statusDistribution',
            'period'
        ));
    }
}
