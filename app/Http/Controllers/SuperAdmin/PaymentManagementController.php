<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Business;
use App\Models\Plan;
use Illuminate\Http\Request;

class PaymentManagementController extends Controller
{
    /**
     * Display all payments from all businesses
     */
    public function index(Request $request)
    {
        $query = Payment::with(['business', 'plan']);

        // Filter by business
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        // Filter by plan
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Search by stripe payment ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('stripe_payment_id', 'like', "%{$search}%")
                  ->orWhere('stripe_subscription_id', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        // Get all businesses and plans for filter dropdowns
        $businesses = Business::orderBy('business_name')->get();
        $plans = Plan::orderBy('name')->get();

        return view('superadmin.payments.index', compact('payments', 'businesses', 'plans'));
    }
}
