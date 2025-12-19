<?php

/**
 * Company: CETAM
 * Project: FF
 * File: SupportTicketController.php
 * Created on: 20/10/2025
 * Created by: Dafne Vanessa Castillo Moreo
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 16/11/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreo |
 *   Description: Refactored Support Ticket Controller |
 */

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of support tickets for the authenticated business
     */
    public function index(Request $request): View
    {
        $businessId = Auth::id();

        $query = SupportTicket::where('business_id', $businessId)
            ->orderBy('created_at', 'desc');

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        $tickets = $query->paginate(config('cetam.cs.pagination.per_page', 15));

        return view('support.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new support ticket
     */
    public function create(): View
    {
        return view('support.create');
    }

    /**
     * Store a newly created support ticket
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'priority' => 'nullable|in:low,medium,high',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $businessId = Auth::id();

        // 5.5: Extract file handling to private method
        $attachmentUrl = $this->handleAttachment($request);

        $ticket = SupportTicket::create([
            'business_id' => $businessId,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'] ?? 'medium',
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
    public function show(SupportTicket $supportTicket): View
    {
        $this->authorize('view', $supportTicket);

        return view('support.show', compact('supportTicket'));
    }

    /**
     * Show the form for editing the support ticket
     */
    public function edit(SupportTicket $supportTicket): View|RedirectResponse
    {
        $this->authorize('update', $supportTicket);

        // 5.4.1: Early Return - Ticket not open
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
    public function update(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $this->authorize('update', $supportTicket);

        // 5.4.1: Early Return - Ticket not open
        if ($supportTicket->status !== 'open') {
            return back()->with('error', 'No se pueden editar tickets que no están abiertos');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:2000',
        ]);

        $supportTicket->update($validated);

        return redirect()
            ->route('business.support.show', $supportTicket->support_ticket_id)
            ->with('success', 'Ticket actualizado exitosamente');
    }

    /**
     * Mark ticket as resolved (close it)
     */
    public function close(SupportTicket $supportTicket): RedirectResponse
    {
        $this->authorize('update', $supportTicket);

        // 5.4.1: Early Return - Already closed
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
    public function reopen(SupportTicket $supportTicket): RedirectResponse
    {
        $this->authorize('update', $supportTicket);

        // 5.4.1: Early Return - Not closed
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
    public function destroy(SupportTicket $supportTicket): RedirectResponse
    {
        $this->authorize('delete', $supportTicket);

        // 5.4.1: Early Return - Not open
        if ($supportTicket->status !== 'open') {
            return back()->with('error', 'Solo se pueden eliminar tickets abiertos');
        }

        // 5.5: Extract attachment deletion to private method
        $this->deleteAttachment($supportTicket);

        $supportTicket->delete();

        return redirect()
            ->route('business.support.index')
            ->with('success', 'Ticket eliminado exitosamente');
    }

    /**
     * Handle file attachment upload
     * 5.5: Private helper method following SRP - Eliminates code duplication
     */
    private function handleAttachment(Request $request): ?string
    {
        // 5.4.1: Early Return - No attachment
        if (!$request->hasFile('attachment')) {
            return null;
        }

        $file = $request->file('attachment');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('support_tickets', $fileName, 'public');

        return Storage::url($path);
    }

    /**
     * Delete attachment from storage
     * 5.5: Private helper method following SRP
     */
    private function deleteAttachment(SupportTicket $supportTicket): void
    {
        // 5.4.1: Early Return - No attachment
        if (!$supportTicket->attachment_url) {
            return;
        }

        $path = str_replace('/storage/', '', $supportTicket->attachment_url);
        Storage::disk('public')->delete($path);
    }
}
