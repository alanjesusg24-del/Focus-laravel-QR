<?php

/**
 * Company: CETAM
 * Project: FF
 * File: TicketManagementController.php (SuperAdmin)
 * Created on: 20/11/2025
 * Created by: Dafne Vanessa Castillo Moreo
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 16/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreo |
 *   Description: Refactored to Ticket Management Controller standards |
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TicketManagementController extends Controller
{
    /**
     * Display all support tickets from all businesses
     */
    public function index(Request $request): View
    {
        $query = SupportTicket::with('business');

        // 5.5: Apply filters through private method
        $query = $this->applyFilters($query, $request);

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        $businesses = Business::orderBy('business_name')->get();

        return view('superadmin.tickets.index', compact('tickets', 'businesses'));
    }

    /**
     * Display the specified ticket
     */
    public function show(SupportTicket $ticket): View
    {
        $ticket->load('business');

        return view('superadmin.tickets.show', compact('ticket'));
    }

    /**
     * Show the form to respond to a ticket
     */
    public function respond(SupportTicket $ticket): View|RedirectResponse
    {
        // 5.4.1: Early Return - Already responded
        if ($ticket->response) {
            return redirect()
                ->route('superadmin.tickets.show', $ticket->support_ticket_id)
                ->with('error', 'Este ticket ya ha sido respondido');
        }

        $ticket->load('business');

        return view('superadmin.tickets.respond', compact('ticket'));
    }

    /**
     * Store the response to a ticket
     */
    public function storeResponse(Request $request, SupportTicket $ticket): RedirectResponse
    {
        // 5.4.1: Early Return - Already responded
        if ($ticket->response) {
            return back()->with('error', 'Este ticket ya ha sido respondido');
        }

        $validated = $request->validate([
            'response' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        // 5.5: Extract file handling to private method
        $attachmentUrl = $this->handleAttachment($request);

        $ticket->update([
            'response' => $validated['response'],
            'response_attachment_url' => $attachmentUrl,
            'responded_at' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()
            ->route('superadmin.tickets.show', $ticket->support_ticket_id)
            ->with('success', 'Respuesta enviada exitosamente');
    }

    /**
     * Update the ticket status
     */
    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        // 5.5: Extract status update data to private method
        $updateData = $this->prepareStatusUpdateData($validated['status']);

        $ticket->update($updateData);

        $statusLabel = $this->getStatusLabel($validated['status']);

        return redirect()
            ->route('superadmin.tickets.show', $ticket->support_ticket_id)
            ->with('success', 'Estado actualizado a: ' . $statusLabel);
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Handle file attachment upload
     * 5.5: Private helper method following SRP
     */
    private function handleAttachment(Request $request): ?string
    {
        // 5.4.1: Early Return - No attachment
        if (!$request->hasFile('attachment')) {
            return null;
        }

        $file = $request->file('attachment');
        $fileName = time() . '_response_' . $file->getClientOriginalName();
        $path = $file->storeAs('support_tickets/responses', $fileName, 'public');

        return Storage::url($path);
    }

    /**
     * Prepare status update data
     * 5.5: Private helper method following SRP
     */
    private function prepareStatusUpdateData(string $status): array
    {
        $updateData = ['status' => $status];

        // 5.1: Use match expression instead of if-elseif
        $updateData['closed_at'] = match ($status) {
            'closed' => now(),
            'open' => null,
            default => $updateData['closed_at'] ?? null,
        };

        return $updateData;
    }

    /**
     * Get status label
     * 5.5: Private helper method following SRP
     */
    private function getStatusLabel(string $status): string
    {
        // 5.1: Use match expression
        return match ($status) {
            'open' => 'Abierto',
            'in_progress' => 'En Progreso',
            'resolved' => 'Resuelto',
            'closed' => 'Cerrado',
            default => 'Desconocido',
        };
    }
}
