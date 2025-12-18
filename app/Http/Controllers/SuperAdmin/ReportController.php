<?php

/**
 * Company: CETAM
 * Project: FF
 * File: ReportController.php (SuperAdmin)
 * Created on: 20/11/2025
 * Created by: Dafne Vanessa Castillo Moreno
 * Approved by: Dafne Vanessa Castillo Moreno
 *
 * Changelog:
 * - ID: 1 | Modified on: 15/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreno |
 *   Description: Refactored to comply  |
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Business;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    /**
     * Display reports and statistics dashboard
     */
    public function index(Request $request): View
    {
        // 5.5: Extract all data gathering to private methods following SRP
        $businessStats = $this->getBusinessStats();
        $ordersStats = $this->getOrdersStats();
        $revenueStats = $this->getRevenueStats();
        $topBusinesses = $this->getTopBusinessesByOrders();
        $ticketsStats = $this->getTicketsStats();
        $recentActivity = $this->getRecentActivity();

        return view('superadmin.reports.index', array_merge(
            $businessStats,
            $ordersStats,
            $revenueStats,
            ['topBusinesses' => $topBusinesses],
            $ticketsStats,
            $recentActivity
        ));
    }

    /**
     * Get business statistics
     * 5.5: Private helper method following SRP
     */
    private function getBusinessStats(): array
    {
        $totalBusinesses = Business::count();
        $activeBusinesses = Business::where('is_active', true)->count();
        $inactiveBusinesses = $totalBusinesses - $activeBusinesses;

        return [
            'totalBusinesses' => $totalBusinesses,
            'activeBusinesses' => $activeBusinesses,
            'inactiveBusinesses' => $inactiveBusinesses,
        ];
    }

    /**
     * Get orders statistics
     * 5.5: Private helper method following SRP
     */
    private function getOrdersStats(): array
    {
        return [
            'totalOrders' => Order::count(),
            'ordersByStatus' => Order::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),
        ];
    }

    /**
     * Get revenue statistics
     * 5.5: Private helper method following SRP
     */
    private function getRevenueStats(): array
    {
        $revenueByMonth = Payment::where('status', 'completed')
            ->where('payment_date', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');

        return [
            'revenueByMonth' => $revenueByMonth,
            'totalRevenue' => $totalRevenue,
        ];
    }

    /**
     * Get top 5 businesses by order count
     * 5.5: Private helper method following SRP
     */
    private function getTopBusinessesByOrders(): Collection
    {
        return Business::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get support tickets statistics
     * 5.5: Private helper method following SRP
     */
    private function getTicketsStats(): array
    {
        return [
            'totalTickets' => SupportTicket::count(),
            'openTickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'resolvedTickets' => SupportTicket::whereIn('status', ['resolved', 'closed'])->count(),
        ];
    }

    /**
     * Get recent activity (orders and payments)
     * 5.5: Private helper method following SRP
     */
    private function getRecentActivity(): array
    {
        return [
            'recentOrders' => Order::orderBy('created_at', 'desc')->limit(5)->get(),
            'recentPayments' => Payment::orderBy('payment_date', 'desc')->limit(5)->get(),
        ];
    }
}
