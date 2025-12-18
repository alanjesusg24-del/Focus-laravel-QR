<?php

/**
 * Company: CETAM
 * Project: FF
 * File: GlobalOrderController.php (SuperAdmin)
 * Created on: 10/10/2025
 * Created by: Dafne Vanessa Castillo Moreo
 * Approved by: Dafne Vanessa Castillo Moreno
 *
 * Changelog:
 * - ID: 1 | Modified on: 15/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreo |
 *   Description: Refactored to comply  |
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;

class GlobalOrderController extends Controller
{
    /**
     * Display all orders from all businesses
     */
    public function index(Request $request): View
    {
        $query = Order::with(['business', 'business.plan']);

        $query = $this->applyFilters($query, $request);

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get all businesses for filter dropdown
        $businesses = Business::orderBy('business_name')->get();

        return view('superadmin.orders.index', compact('orders', 'businesses'));
    }

    /**
     * Apply all filters to the query
     */
    private function applyFilters(Builder $query, Request $request): Builder
    {
        // Filter by business
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by folio or customer name
        if ($request->filled('search')) {
            $query = $this->applySearchFilter($query, $request->search);
        }

        return $query;
    }

    /**
     * Apply search filter to the query
     */
    private function applySearchFilter(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('folio_number', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('order_number', 'like', "%{$search}%");
        });
    }
}
