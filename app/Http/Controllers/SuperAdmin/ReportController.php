<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Business;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reports and statistics dashboard
     */
    public function index(Request $request)
    {
        // Total businesses stats
        $totalBusinesses = Business::count();
        $activeBusinesses = Business::where('is_active', true)->count();
        $inactiveBusinesses = $totalBusinesses - $activeBusinesses;

        // Orders stats
        $totalOrders = Order::count();
        $ordersByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Payments stats - Revenue by month (last 6 months)
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

        // Top 5 businesses by orders
        $topBusinesses = Business::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get();

        // Support tickets stats
        $totalTickets = SupportTicket::count();
        $openTickets = SupportTicket::whereIn('status', ['open', 'in_progress'])->count();
        $resolvedTickets = SupportTicket::whereIn('status', ['resolved', 'closed'])->count();

        // Recent activity summary
        $recentOrders = Order::orderBy('created_at', 'desc')->limit(5)->get();
        $recentPayments = Payment::orderBy('payment_date', 'desc')->limit(5)->get();

        return view('superadmin.reports.index', compact(
            'totalBusinesses',
            'activeBusinesses',
            'inactiveBusinesses',
            'totalOrders',
            'ordersByStatus',
            'revenueByMonth',
            'totalRevenue',
            'topBusinesses',
            'totalTickets',
            'openTickets',
            'resolvedTickets',
            'recentOrders',
            'recentPayments'
        ));
    }
}
