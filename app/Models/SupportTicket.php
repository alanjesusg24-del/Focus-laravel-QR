<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $table = 'support_tickets';
    protected $primaryKey = 'support_ticket_id';

    public $timestamps = false; // No tiene updated_at

    const UPDATED_AT = null;

    protected $fillable = [
        'business_id',
        'subject',
        'description',
        'status',
        'priority',
        'responded_at',
        'closed_at',
        'response',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'responded_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the business associated with this ticket
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }

    /**
     * Scope to get open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope to get in progress tickets
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope to filter by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get high priority tickets
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope to filter by business
     */
    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}
