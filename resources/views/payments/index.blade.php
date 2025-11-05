@extends('layouts.business-app')

@section('title', 'Payment Plans - Order QR System')

@section('page')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Payment Plans</h2>
            <p class="mb-0">Manage your subscription and billing</p>
        </div>
    </div>

    <!-- Payment Status Alert -->
    @if($business->plan && !app(App\Services\PaymentService::class)->isPaymentExpired($business))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <strong>Current Plan:</strong> {{ $business->plan->name }} - Valid until {{ $business->last_payment_date->addDays($business->plan->duration_days)->format('M d, Y') }}
            </div>
        </div>
    @else
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <strong>Notice:</strong> Your payment has expired or you don't have an active plan. Please select a plan to continue.
            </div>
        </div>
    @endif

    <!-- Plans Grid -->
    <div class="row mb-5">
        @foreach($plans as $plan)
        <div class="col-12 col-md-6 mb-4">
            <div class="card border-0 shadow {{ $business->plan_id === $plan->plan_id ? 'border-primary' : '' }}">
                <div class="card-header bg-white border-bottom">
                    <div class="text-center">
                        <h3 class="h5 mb-2">{{ $plan->name }}</h3>
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="display-4 fw-bold text-primary">${{ number_format($plan->price, 2) }}</span>
                            <span class="text-gray-600 ms-2">MXN</span>
                        </div>
                        <p class="text-muted small mt-1">{{ $plan->duration_days }} days duration</p>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-gray-700 mb-3">{{ $plan->description }}</p>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center mb-2">
                            <svg class="icon icon-xs text-success me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="small">{{ $plan->retention_days }} days data retention</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <svg class="icon icon-xs text-success me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="small">Unlimited orders</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <svg class="icon icon-xs text-success me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="small">QR code generation</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <svg class="icon icon-xs text-success me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="small">Push notifications</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-top">
                    <form action="{{ route('business.payments.checkout', $plan) }}" method="GET">
                        @if($business->plan_id === $plan->plan_id)
                            <button type="submit" class="btn btn-secondary w-100">Renew Plan</button>
                        @else
                            <button type="submit" class="btn btn-primary w-100">Select Plan</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Recent Payments -->
    @if($payments->count() > 0)
    <div class="card border-0 shadow">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">Recent Payments</h2>
                </div>
                <div class="col text-end">
                    <a href="{{ route('business.payments.history') }}" class="btn btn-sm btn-secondary">View All</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="border-bottom" scope="col">Plan</th>
                        <th class="border-bottom" scope="col">Amount</th>
                        <th class="border-bottom" scope="col">Status</th>
                        <th class="border-bottom" scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td class="text-gray-900">{{ $payment->plan->name }}</td>
                        <td class="fw-bold">${{ number_format($payment->amount, 2) }} MXN</td>
                        <td>
                            @php
                                $statusConfig = [
                                    'completed' => ['class' => 'bg-success', 'label' => 'Completed'],
                                    'pending' => ['class' => 'bg-warning', 'label' => 'Pending'],
                                    'failed' => ['class' => 'bg-danger', 'label' => 'Failed'],
                                ];
                                $config = $statusConfig[$payment->status] ?? ['class' => 'bg-secondary', 'label' => 'Unknown'];
                            @endphp
                            <span class="badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                        </td>
                        <td class="text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
