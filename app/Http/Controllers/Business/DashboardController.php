<?php

/**
 * Company: CETAM
 * Project: FF
 * File: DashboardController.php
 * Created on: 20/11/2025
 * Created by: Dafne Vanessa Castillo Moreno
 * Approved by: Alan Jesus Garcia Nava
 *
 * Changelog:
 * - ID: 1 | Modified on: 15/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreno |
 *   Description: Refactored to comply  |
 */

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Show business dashboard with reports
     */
    public function index(Request $request): View
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

        // Calculate basic metrics
        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'delivered')->count();
        $cancelledOrders = $orders->where('status', 'cancelled')->count();
        $linkedOrders = $orders->whereNotNull('mobile_user_id')->count();
        $unlinkedOrders = $totalOrders - $linkedOrders;

        // Calculate rates
        $completionRate = $this->calculateRate($completedOrders, $totalOrders);
        $cancellationRate = $this->calculateRate($cancelledOrders, $totalOrders);
        $mobileAdoptionRate = $this->calculateRate($linkedOrders, $totalOrders);

        // Calculate average preparation time
        $avgPreparationTime = $this->calculateAveragePreparationTime($orders);

        // 5.2.3: Using CarbonPeriod instead of while loop
        $ordersPerDay = $this->calculateOrdersPerDay($orders, $startDate, $endDate);

        // Status distribution
        $statusDistribution = $this->calculateStatusDistribution($orders);

        // 5.2.3: Using Collection range() instead of for loop
        $ordersByHour = $this->calculateOrdersByHour($orders);

        // 5.2.3: Using Collection methods instead of foreach
        $ordersByWeekday = $this->calculateOrdersByWeekday($orders);

        // Comparison with previous period
        $periodComparison = $this->calculatePeriodComparison(
            $businessId,
            $startDate,
            $endDate,
            $totalOrders,
            $completedOrders,
            $cancelledOrders
        );

        // Recent activity
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
            'total_orders_change' => $periodComparison['total_orders_change'],
            'completed_orders_change' => $periodComparison['completed_orders_change'],
            'cancelled_orders_change' => $periodComparison['cancelled_orders_change'],
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
    public function analytics(Request $request): RedirectResponse
    {
        return redirect()->route('business.dashboard.index', $request->all());
    }

    /**
     * Calculate percentage rate
     * 5.5: Private helper method following SRP
     */
    private function calculateRate(int $numerator, int $denominator): float
    {
        return $denominator > 0 ? round(($numerator / $denominator) * 100, 1) : 0;
    }

    /**
     * Calculate average preparation time in minutes
     * 5.5: Private helper method following SRP
     */
    private function calculateAveragePreparationTime(Collection $orders): string|int
    {
        $avgPrepTime = $orders
            ->filter(fn($order) => $order->status !== 'pending' && $order->ready_at && $order->created_at)
            ->map(fn($order) => $order->created_at->diffInMinutes($order->ready_at))
            ->avg();

        return $avgPrepTime ? round($avgPrepTime, 0) : '--';
    }

    /**
     * Calculate orders per day using CarbonPeriod
     * 5.2.3: Replace while loop with CarbonPeriod iteration
     * 5.5: Private helper method following SRP
     */
    private function calculateOrdersPerDay(Collection $orders, Carbon $startDate, Carbon $endDate): array
    {
        $period = CarbonPeriod::create($startDate, $endDate);

        return collect($period)
            ->mapWithKeys(function (Carbon $date) use ($orders) {
                $dateKey = $date->format('d/m');
                $count = $orders->filter(fn($order) => $order->created_at->isSameDay($date))->count();
                return [$dateKey => $count];
            })
            ->toArray();
    }

    /**
     * Calculate status distribution
     * 5.5: Private helper method following SRP
     */
    private function calculateStatusDistribution(Collection $orders): array
    {
        return [
            'pending' => $orders->where('status', 'pending')->count(),
            'ready' => $orders->where('status', 'ready')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Calculate orders by hour of day
     * 5.2.3: Using Collection range() instead of for loop
     * 5.5: Private helper method following SRP
     */
    private function calculateOrdersByHour(Collection $orders): array
    {
        return collect(range(0, 23))
            ->mapWithKeys(function (int $hour) use ($orders) {
                $count = $orders->filter(fn($order) => $order->created_at->hour === $hour)->count();
                return $count > 0 ? [$hour => $count] : [];
            })
            ->toArray();
    }

    /**
     * Calculate orders by day of week
     * 5.2.3: Using Collection methods instead of foreach
     * 5.5: Private helper method following SRP
     */
    private function calculateOrdersByWeekday(Collection $orders): array
    {
        $weekdays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        return collect($weekdays)
            ->mapWithKeys(function (string $weekday, int $index) use ($orders) {
                $dayOfWeek = $index + 1;
                $count = $orders->filter(fn($order) => $order->created_at->dayOfWeekIso === $dayOfWeek)->count();
                return [$weekday => $count];
            })
            ->toArray();
    }

    /**
     * Calculate period comparison with previous period
     * 5.5: Private helper method following SRP
     */
    private function calculatePeriodComparison(
        int $businessId,
        Carbon $startDate,
        Carbon $endDate,
        int $totalOrders,
        int $completedOrders,
        int $cancelledOrders
    ): array {
        $periodDuration = $startDate->diffInDays($endDate) + 1;
        $previousStartDate = $startDate->copy()->subDays($periodDuration);
        $previousEndDate = $startDate->copy()->subDay();

        $previousOrders = Order::where('business_id', $businessId)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->get();

        $previousTotalOrders = $previousOrders->count();
        $previousCompletedOrders = $previousOrders->where('status', 'delivered')->count();
        $previousCancelledOrders = $previousOrders->where('status', 'cancelled')->count();

        return [
            'total_orders_change' => $this->calculatePercentageChange($totalOrders, $previousTotalOrders),
            'completed_orders_change' => $this->calculatePercentageChange($completedOrders, $previousCompletedOrders),
            'cancelled_orders_change' => $this->calculatePercentageChange($cancelledOrders, $previousCancelledOrders),
        ];
    }

    /**
     * Calculate percentage change between two values
     * 5.1.3: Eliminar ternarios anidados complejos
     * 5.5: Private helper method following SRP
     */
    private function calculatePercentageChange(int $current, int $previous): float
    {
        // 5.4.1: Early Return - No previous data
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
