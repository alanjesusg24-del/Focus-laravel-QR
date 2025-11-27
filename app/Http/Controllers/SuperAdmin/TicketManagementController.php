<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketManagementController extends Controller
{
    /**
     * Display all support tickets from all businesses
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with('business');

        // Filter by business
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by subject
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get all businesses for filter dropdown
        $businesses = Business::orderBy('business_name')->get();

        return view('superadmin.tickets.index', compact('tickets', 'businesses'));
    }

    /**
     * Display the specified ticket
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load('business');
        return view('superadmin.tickets.show', compact('ticket'));
    }

    /**
     * Show the form to respond to a ticket
     */
    public function respond(SupportTicket $ticket)
    {
        // Check if ticket already has a response
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
    public function storeResponse(Request $request, SupportTicket $ticket)
    {
        // Check if ticket already has a response
        if ($ticket->response) {
            return back()->with('error', 'Este ticket ya ha sido respondido');
        }

        $validated = $request->validate([
            'response' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        // Handle file attachment
        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_response_' . $file->getClientOriginalName();
            $path = $file->storeAs('support_tickets/responses', $fileName, 'public');
            $attachmentUrl = Storage::url($path);
        }

        // Update ticket with response
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
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $updateData = ['status' => $validated['status']];

        // If closing the ticket, set closed_at timestamp
        if ($validated['status'] === 'closed') {
            $updateData['closed_at'] = now();
        } elseif ($validated['status'] === 'open') {
            // If reopening, clear closed_at
            $updateData['closed_at'] = null;
        }

        $ticket->update($updateData);

        $statusLabels = [
            'open' => 'Abierto',
            'in_progress' => 'En Progreso',
            'resolved' => 'Resuelto',
            'closed' => 'Cerrado',
        ];

        return redirect()
            ->route('superadmin.tickets.show', $ticket->support_ticket_id)
            ->with('success', 'Estado actualizado a: ' . $statusLabels[$validated['status']]);
    }
}
