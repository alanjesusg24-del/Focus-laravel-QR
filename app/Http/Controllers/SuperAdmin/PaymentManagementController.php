<?php

/**
 * Company: CETAM
 * Project: FF
 * File: PaymentManagementController.php (SuperAdmin)
 * Created on: 20/10/2025
 * Created by: Alan Jesus Garcia Nava
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 16/12/2025 |
 *   Modified by: Alan Jesus Garcia Nava |
 *   Description: Refactored to Payment Management Controller standards |
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Business;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentManagementController extends Controller
{
    /**
     * Display all payments from all businesses
     */
    public function index(Request $request): View
    {
        $query = Payment::with(['business', 'plan']);

        // 5.5: Apply filters through private method
        $query = $this->applyFilters($query, $request);

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        $businesses = Business::orderBy('business_name')->get();
        $plans = Plan::orderBy('name')->get();

        return view('superadmin.payments.index', compact('payments', 'businesses', 'plans'));
    }

    /**
     * Apply all filters to the query
     * 5.5: Private helper method following SRP
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('business', fn($q) =>
                $q->where('business_name', 'like', "%{$search}%")
            );
        }

        return $query;
    }
}
