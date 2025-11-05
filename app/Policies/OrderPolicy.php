<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Order;
use App\Models\Business;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Business $business): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Business $business, Order $order): bool
    {
        return $order->business_id === $business->business_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Business $business): bool
    {
        return $business->is_active;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Business $business, Order $order): bool
    {
        return $order->business_id === $business->business_id &&
               in_array($order->status, ['pending', 'preparing']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Business $business, Order $order): bool
    {
        return $order->business_id === $business->business_id &&
               $order->status === 'pending';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Business $business, Order $order): bool
    {
        return $order->business_id === $business->business_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Business $business, Order $order): bool
    {
        return $order->business_id === $business->business_id;
    }
}
