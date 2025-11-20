<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show business dashboard with reports
     */
    public function index(Request $request)
    {
        $businessId = Auth::id();

        // Get date range from request
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::today()->endOfDay();

        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Get all orders in the date range
        $orders = Order::where('business_id', $businessId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Calculate metrics
        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'delivered')->count();
        $cancelledOrders = $orders->where('status', 'cancelled')->count();
        $linkedOrders = $orders->whereNotNull('mobile_user_id')->count();
        $unlinkedOrders = $totalOrders - $linkedOrders;

        // Calculate rates
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
        $cancellationRate = $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100, 1) : 0;
        $mobileAdoptionRate = $totalOrders > 0 ? round(($linkedOrders / $totalOrders) * 100, 1) : 0;

        // Calculate average preparation time (pending -> ready)
        $avgPrepTime = $orders->where('status', '!=', 'pending')
            ->filter(function ($order) {
                return $order->ready_at && $order->created_at;
            })
            ->map(function ($order) {
                return $order->created_at->diffInMinutes($order->ready_at);
            })
            ->avg();

        $avgPreparationTime = $avgPrepTime ? round($avgPrepTime, 0) : '--';

        // Orders per day
        $ordersPerDay = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('d/m');
            $ordersPerDay[$dateKey] = $orders->filter(function ($order) use ($currentDate) {
                return $order->created_at->format('Y-m-d') === $currentDate->format('Y-m-d');
            })->count();
            $currentDate->addDay();
        }

        // Status distribution
        $statusDistribution = [
            'pending' => $orders->where('status', 'pending')->count(),
            'ready' => $orders->where('status', 'ready')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        // Orders by hour of day
        $ordersByHour = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $count = $orders->filter(function ($order) use ($hour) {
                return $order->created_at->hour === $hour;
            })->count();
            if ($count > 0) {
                $ordersByHour[$hour] = $count;
            }
        }

        // Orders by day of week
        $weekdays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $ordersByWeekday = [];
        foreach ($weekdays as $index => $weekday) {
            $dayOfWeek = $index + 1; // Carbon uses 1-7 (Monday-Sunday)
            $ordersByWeekday[$weekday] = $orders->filter(function ($order) use ($dayOfWeek) {
                return $order->created_at->dayOfWeekIso === $dayOfWeek;
            })->count();
        }

        // Comparison with previous period
        $periodDuration = $startDate->diffInDays($endDate) + 1;
        $previousStartDate = $startDate->copy()->subDays($periodDuration);
        $previousEndDate = $startDate->copy()->subDay();

        $previousOrders = Order::where('business_id', $businessId)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->get();

        $previousTotalOrders = $previousOrders->count();
        $previousCompletedOrders = $previousOrders->where('status', 'delivered')->count();
        $previousCancelledOrders = $previousOrders->where('status', 'cancelled')->count();

        // Calculate changes
        $totalOrdersChange = $previousTotalOrders > 0
            ? round((($totalOrders - $previousTotalOrders) / $previousTotalOrders) * 100, 1)
            : ($totalOrders > 0 ? 100 : 0);

        $completedOrdersChange = $previousCompletedOrders > 0
            ? round((($completedOrders - $previousCompletedOrders) / $previousCompletedOrders) * 100, 1)
            : ($completedOrders > 0 ? 100 : 0);

        $cancelledOrdersChange = $previousCancelledOrders > 0
            ? round((($cancelledOrders - $previousCancelledOrders) / $previousCancelledOrders) * 100, 1)
            : ($cancelledOrders > 0 ? 100 : 0);

        // Recent activity (últimas 10 órdenes)
        $recentActivity = Order::where('business_id', $businessId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $reportData = [
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'completion_rate' => $completionRate,
            'cancellation_rate' => $cancellationRate,
            'avg_preparation_time' => $avgPreparationTime,
            'linked_orders' => $linkedOrders,
            'unlinked_orders' => $unlinkedOrders,
            'mobile_adoption_rate' => $mobileAdoptionRate,
            'orders_per_day' => $ordersPerDay,
            'status_distribution' => $statusDistribution,
            'orders_by_hour' => $ordersByHour,
            'orders_by_weekday' => $ordersByWeekday,
            'total_orders_change' => $totalOrdersChange,
            'completed_orders_change' => $completedOrdersChange,
            'cancelled_orders_change' => $cancelledOrdersChange,
            'recent_activity' => $recentActivity,
        ];

        return view('dashboard.index', compact(
            'reportData',
            'startDate',
            'endDate',
            'totalDays'
        ));
    }

    /**
     * Show analytics page (legacy - redirect to main dashboard)
     */
    public function analytics(Request $request)
    {
        return redirect()->route('business.dashboard.index', $request->all());
    }
}
