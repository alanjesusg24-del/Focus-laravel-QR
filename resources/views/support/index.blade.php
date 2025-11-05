@extends('layouts.business-app')

@section('title', 'Support Tickets - Order QR System')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Support Tickets</h2>
            <p class="mb-0">Manage your help requests and technical support</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('business.support.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Ticket
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('business.support.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter by status</label>
                    <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card border-0 shadow">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">Your Tickets</h2>
                </div>
            </div>
        </div>
        @if($tickets->count() > 0)
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="border-bottom" scope="col">ID</th>
                        <th class="border-bottom" scope="col">Subject</th>
                        <th class="border-bottom" scope="col">Priority</th>
                        <th class="border-bottom" scope="col">Status</th>
                        <th class="border-bottom" scope="col">Created</th>
                        <th class="border-bottom" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td class="fw-bolder text-gray-500">#{{ $ticket->support_ticket_id }}</td>
                        <td>
                            <a href="{{ route('business.support.show', $ticket->support_ticket_id) }}" class="text-primary fw-bold">
                                {{ Str::limit($ticket->subject, 50) }}
                            </a>
                        </td>
                        <td>
                            @php
                                $priorityConfig = [
                                    'low' => ['class' => 'bg-success', 'label' => 'Low'],
                                    'medium' => ['class' => 'bg-warning', 'label' => 'Medium'],
                                    'high' => ['class' => 'bg-danger', 'label' => 'High'],
                                ];
                                $config = $priorityConfig[$ticket->priority] ?? ['class' => 'bg-secondary', 'label' => 'Unknown'];
                            @endphp
                            <span class="badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                        </td>
                        <td>
                            @php
                                $statusConfig = [
                                    'open' => ['class' => 'bg-info', 'label' => 'Open'],
                                    'in_progress' => ['class' => 'bg-warning', 'label' => 'In Progress'],
                                    'closed' => ['class' => 'bg-secondary', 'label' => 'Closed'],
                                ];
                                $config = $statusConfig[$ticket->status] ?? ['class' => 'bg-secondary', 'label' => 'Unknown'];
                            @endphp
                            <span class="badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                        </td>
                        <td class="text-gray-500">{{ $ticket->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('business.support.show', $ticket->support_ticket_id) }}" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            {{ $tickets->links() }}
        </div>
        @endif
        @else
        <div class="card-body text-center py-5">
            <svg class="icon icon-xxl text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <p class="text-gray-600 mb-3">No support tickets found</p>
            <a href="{{ route('business.support.create') }}" class="btn btn-primary btn-sm">Create First Ticket</a>
        </div>
        @endif
    </div>
</div>
@endsection
