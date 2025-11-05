<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of support tickets for the authenticated business
     */
    public function index(Request $request)
    {
        $businessId = Auth::id();

        $status = $request->get('status');
        $query = SupportTicket::where('business_id', $businessId)
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $tickets = $query->paginate(15);

        return view('support.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new support ticket
     */
    public function create()
    {
        return view('support.create');
    }

    /**
     * Store a newly created support ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $businessId = Auth::id();

        // Handle file attachment
        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('support_tickets', $fileName, 'public');
            $attachmentUrl = Storage::url($path);
        }

        $ticket = SupportTicket::create([
            'business_id' => $businessId,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'attachment_url' => $attachmentUrl,
        ]);

        return redirect()
            ->route('business.support.show', $ticket->support_ticket_id)
            ->with('success', 'Ticket de soporte creado exitosamente. Te contactaremos pronto.');
    }

    /**
     * Display the specified support ticket
     */
    public function show(SupportTicket $supportTicket)
    {
        $this->authorize('view', $supportTicket);

        return view('support.show', compact('supportTicket'));
    }

    /**
     * Show the form for editing the support ticket (only description/priority)
     */
    public function edit(SupportTicket $supportTicket)
    {
        $this->authorize('update', $supportTicket);

        // Only allow editing if ticket is still open
        if ($supportTicket->status !== 'open') {
            return redirect()
                ->route('business.support.show', $supportTicket->support_ticket_id)
                ->with('error', 'No se pueden editar tickets que no están abiertos');
        }

        return view('support.edit', compact('supportTicket'));
    }

    /**
     * Update the specified support ticket
     */
    public function update(Request $request, SupportTicket $supportTicket)
    {
        $this->authorize('update', $supportTicket);

        // Only allow editing if ticket is still open
        if ($supportTicket->status !== 'open') {
            return back()->with('error', 'No se pueden editar tickets que no están abiertos');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $supportTicket->update($validated);

        return redirect()
            ->route('business.support.show', $supportTicket->support_ticket_id)
            ->with('success', 'Ticket actualizado exitosamente');
    }

    /**
     * Mark ticket as resolved (close it)
     */
    public function close(SupportTicket $supportTicket)
    {
        $this->authorize('update', $supportTicket);

        if ($supportTicket->status === 'closed') {
            return back()->with('error', 'Este ticket ya está cerrado');
        }

        $supportTicket->update([
            'status' => 'closed',
            'resolved_at' => now(),
        ]);

        return redirect()
            ->route('business.support.index')
            ->with('success', 'Ticket cerrado exitosamente');
    }

    /**
     * Reopen a closed ticket
     */
    public function reopen(SupportTicket $supportTicket)
    {
        $this->authorize('update', $supportTicket);

        if ($supportTicket->status !== 'closed') {
            return back()->with('error', 'Solo se pueden reabrir tickets cerrados');
        }

        $supportTicket->update([
            'status' => 'open',
            'resolved_at' => null,
        ]);

        return redirect()
            ->route('business.support.show', $supportTicket->support_ticket_id)
            ->with('success', 'Ticket reabierto exitosamente');
    }

    /**
     * Delete ticket (only if open)
     */
    public function destroy(SupportTicket $supportTicket)
    {
        $this->authorize('delete', $supportTicket);

        // Only allow deletion if ticket is open
        if ($supportTicket->status !== 'open') {
            return back()->with('error', 'Solo se pueden eliminar tickets abiertos');
        }

        // Delete attachment if exists
        if ($supportTicket->attachment_url) {
            $path = str_replace('/storage/', '', $supportTicket->attachment_url);
            Storage::disk('public')->delete($path);
        }

        $supportTicket->delete();

        return redirect()
            ->route('business.support.index')
            ->with('success', 'Ticket eliminado exitosamente');
    }
}
