<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\SupportTicket;
use App\Models\Business;

class SupportTicketPolicy
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
    public function view(Business $business, SupportTicket $supportTicket): bool
    {
        return $supportTicket->business_id === $business->business_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Business $business): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Business $business, SupportTicket $supportTicket): bool
    {
        return $supportTicket->business_id === $business->business_id &&
               $supportTicket->status === 'open';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Business $business, SupportTicket $supportTicket): bool
    {
        return $supportTicket->business_id === $business->business_id &&
               $supportTicket->status === 'open';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Business $business, SupportTicket $supportTicket): bool
    {
        return $supportTicket->business_id === $business->business_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Business $business, SupportTicket $supportTicket): bool
    {
        return $supportTicket->business_id === $business->business_id;
    }
}
