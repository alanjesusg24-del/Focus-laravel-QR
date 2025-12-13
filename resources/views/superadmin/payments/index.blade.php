{{--
  Company: CETAM
  Project: FQR
  File: index.blade.php
  Created on: 10/11/2025
  Created by: Alan Jesus Garcia Nava
  Approved by: Dafne Vanessa Castillo Moreno
--}}
@extends('layouts.superadmin-app')

@section('title', 'Pagos y Suscripciones')

@section('page')
<div class="py-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">

        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('superadmin.dashboard') }}" class="text-primary">
                            <x-icon name="nav.home" />
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Pagos y Suscripciones</li>
                </ol>
            </nav>
            <h2 class="h4 mt-1">Pagos y Suscripciones</h2>
            <p class="mb-0 text-primary">Administra todos los pagos y suscripciones del sistema</p>
        </div>
    </div>

    <div class="table-settings mb-4">
        <div class="row align-items-center justify-content-between">

            <div class="col-12 d-flex align-items-center flex-wrap gap-3">

                {{-- 1. Search --}}
                <div class="input-group" style="max-width: 350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <x-icon name="action.search" class="text-gray-500" />
                    </span>
                    <input type="text"
                           id="search"
                           class="form-control border-start-0 ps-0"
                           placeholder="Buscar por ID, negocio o plan..."
                           autocomplete="off">
                </div>
                <div class="d-flex align-items-center">
                    <span class="small fw-bold text-gray-600 me-2">Filtrar por estado:</span>
                    <form method="GET" action="{{ route('superadmin.payments.index') }}">
                        <input type="hidden" name="plan_id" value="{{ request('plan_id') }}">
                        <select name="status" onchange="this.form.submit()" class="form-select" style="min-width: 160px;">
                            <option value="">Todos</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completados</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Fallidos</option>
                        </select>
                    </form>
                </div>

                <div class="d-flex align-items-center">
                    <span class="small fw-bold text-gray-600 me-2">Filtrar por plan:</span>
                    <form method="GET" action="{{ route('superadmin.payments.index') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <select name="plan_id" onchange="this.form.submit()" class="form-select" style="min-width: 180px;">
                            <option value="">Todos los planes</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->plan_id }}" {{ request('plan_id') == $plan->plan_id ? 'selected' : '' }}>
                                    {{ $plan->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>


            </div>

        </div>
    </div>

    <div class="card border-0 shadow mb-4" style="overflow: visible;">
        <div class="card-body" style="overflow: visible; padding: 20px 24px;">
            <div class="table-responsive" style="overflow: visible;">
                <table class="table align-items-center table-flush table-hover">
                    <thead class="thead-light rounded">
                        <tr>
                            <th class="border-bottom" scope="col">ID</th>
                            <th class="border-bottom" scope="col">Negocio</th>
                            <th class="border-bottom" scope="col">Plan</th>
                            <th class="border-bottom" scope="col">Monto</th>
                            <th class="border-bottom" scope="col">Fecha Pago</th>
                            <th class="border-bottom" scope="col">Próximo Pago</th>
                            <th class="border-bottom" scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td class="text-gray-500">
                                <small>#{{ $payment->payment_id }}</small>
                            </td>
                            <td class="text-gray-900">
                                <span class="fw-bold">{{ $payment->business->business_name ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-secondary">{{ $payment->plan->name ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td class="text-gray-500">
                                {{ $payment->payment_date?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="text-gray-500">
                                @if($payment->payment_date)
                                    {{ $payment->payment_date->addMonth()->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @switch($payment->status)
                                    @case('completed')
                                        <span class="fw-bold text-success">Completado</span>
                                        @break
                                    @case('pending')
                                        <span class="fw-bold text-warning">Pendiente</span>
                                        @break
                                    @case('failed')
                                        <span class="fw-bold text-danger">Fallido</span>
                                        @break
                                    @default
                                        <span class="fw-bold text-muted">{{ ucfirst($payment->status) }}</span>
                                @endswitch
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center" style="font-size: 3rem;">
                                        <x-icon name="state.info" class="text-gray-300 mb-3"/>

                                        <h6 class="text-gray-500 fw-bold mb-1">No se encontraron pagos</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
            @if($payments->hasPages())
                {{ $payments->links('vendor.pagination.volt-custom') }}
            @else
                {{-- Empty space on the left when there is no pagination --}}
                <div></div>
                {{-- Count message when there is no pagination --}}
                @if($payments->total() > 0)
                    <div class="fw-normal small">
                        Mostrando
                        <span class="fw-bold">{{ $payments->firstItem() }}</span>
                        a
                        <span class="fw-bold">{{ $payments->lastItem() }}</span>
                        de
                        <span class="fw-bold">{{ $payments->total() }}</span>
                        {{ $payments->total() == 1 ? 'entrada' : 'entradas' }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const tbody = document.querySelector('tbody');

    // Search in real-time
    if (searchInput && tbody) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const rows = tbody.querySelectorAll('tr');
            let visibleCount = 0;
            let noResultsRow = document.getElementById('no-results-row');

            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) {
                    if (row.id !== 'no-results-row') {
                        row.style.display = 'none';
                    }
                    return;
                }

                // Search in: ID, Business, Plan, Amount, Date
                const id = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                const businessName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const planName = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                const amount = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                const paymentDate = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';

                const matches = id.includes(searchTerm) ||
                               businessName.includes(searchTerm) ||
                               planName.includes(searchTerm) ||
                               amount.includes(searchTerm) ||
                               paymentDate.includes(searchTerm);

                row.style.display = matches ? '' : 'none';

                if (matches) visibleCount++;
            });

            if (searchTerm && visibleCount === 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="7" class="text-center py-5">
                            <p class="text-gray-600 mb-0">No se encontraron pagos que coincidan con "<strong>${searchTerm}</strong>"</p>
                            <small class="text-muted">Intenta buscar por ID, negocio, plan, monto o fecha</small>
                        </td>
                    `;
                    tbody.appendChild(noResultsRow);
                } else {
                    noResultsRow.querySelector('strong').textContent = searchTerm;
                    noResultsRow.style.display = '';
                }
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        });
    }
});
</script>
@endpush
