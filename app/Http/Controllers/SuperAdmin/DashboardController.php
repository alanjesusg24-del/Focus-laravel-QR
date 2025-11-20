<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_businesses' => Business::count(),
            'active_businesses' => Business::where('is_active', true)->count(),
            'inactive_businesses' => Business::where('is_active', false)->count(),
            'total_orders' => Order::count(),
            'total_payments' => Payment::sum('amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'in_process_orders' => Order::where('status', 'in_process')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'open_tickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'pending_tickets' => SupportTicket::where('status', 'pending')->count(),
            'resolved_tickets' => SupportTicket::whereIn('status', ['resolved', 'closed'])->count(),
            'total_tickets' => SupportTicket::count(),
        ];

        // Get order status breakdown
        $ordersByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Get businesses registered per month (last 6 months)
        $businessesPerMonth = Business::select(
                DB::raw('DATE_FORMAT(registration_date, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->where('registration_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get recent businesses (last 10)
        $recentBusinesses = Business::with('plan')
            ->orderBy('registration_date', 'desc')
            ->limit(10)
            ->get();

        // Get recent support tickets (last 10)
        $recentTickets = SupportTicket::with('business')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get top businesses by revenue
        $topBusinessesByRevenue = Business::select('businesses.*')
            ->selectRaw('COALESCE(SUM(payments.amount), 0) as total_revenue')
            ->leftJoin('payments', 'businesses.business_id', '=', 'payments.business_id')
            ->groupBy('businesses.business_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // Get monthly revenue (last 6 months)
        $monthlyRevenue = Payment::select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('payment_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get top businesses by order count
        $topBusinessesByOrders = Business::withCount('orders')
            ->with('plan')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get();

        return view('superadmin.dashboard', compact(
            'stats',
            'ordersByStatus',
            'businessesPerMonth',
            'recentBusinesses',
            'recentTickets',
            'topBusinessesByRevenue',
            'topBusinessesByOrders',
            'monthlyRevenue'
        ));
    }
}
