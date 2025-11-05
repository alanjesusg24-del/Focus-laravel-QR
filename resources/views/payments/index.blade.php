@extends('layouts.order-qr')

@section('title', 'Payment Plans')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-institutional-blue">Payment Plans</h1>
    </div>

    @if($business->plan && !app(App\Services\PaymentService::class)->isPaymentExpired($business))
        <div class="bg-green-100 border-2 border-green-400 text-green-700 px-4 py-3 rounded">
            <strong>Current Plan:</strong> {{ $business->plan->name }}
            - Valid until {{ $business->last_payment_date->addDays($business->plan->duration_days)->format('M d, Y') }}
        </div>
    @else
        <div class="bg-yellow-100 border-2 border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <strong>Notice:</strong> Your payment has expired or you don't have an active plan. Please select a plan to continue.
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($plans as $plan)
            <div class="border-2 border-institutional-blue rounded-lg p-6 hover:shadow-lg transition-shadow
                {{ $business->plan_id === $plan->plan_id ? 'bg-institutional-blue/10' : 'bg-white' }}">
                <div class="text-center mb-4">
                    <h3 class="text-xl font-bold text-institutional-blue">{{ $plan->name }}</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-bold text-institutional-orange">${{ number_format($plan->price, 2) }}</span>
                        <span class="text-gray-600"> MXN</span>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">{{ $plan->duration_days }} days duration</p>
                </div>

                <div class="mb-4">
                    <p class="text-gray-700">{{ $plan->description }}</p>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $plan->retention_days }} days data retention
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Unlimited orders
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            QR code generation
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Push notifications
                        </li>
                    </ul>
                </div>

                <form action="{{ route('order-qr.payment.checkout', $plan) }}" method="GET">
                    @if($business->plan_id === $plan->plan_id)
                        <button type="submit" class="w-full px-4 py-2 text-base font-medium text-white bg-institutional-gray rounded-full">
                            Renew Plan
                        </button>
                    @else
                        <button type="submit" class="w-full px-4 py-2 text-base font-medium text-white bg-institutional-blue hover:bg-institutional-blue/80 rounded-full transition-transform duration-150 active:scale-95">
                            Select Plan
                        </button>
                    @endif
                </form>
            </div>
        @endforeach
    </div>

    @if($payments->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold text-institutional-blue mb-4">Recent Payments</h2>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-institutional-gray/20">
                            <th class="p-3 text-left">Plan</th>
                            <th class="p-3 text-left">Amount</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ $payment->plan->name }}</td>
                                <td class="p-3 font-semibold">${{ number_format($payment->amount, 2) }} MXN</td>
                                <td class="p-3">
                                    <span class="px-3 py-1 rounded-full text-sm
                                        @if($payment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="p-3">{{ $payment->payment_date->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <a href="{{ route('order-qr.payment.history') }}" class="text-institutional-blue hover:underline">
                    View all payment history →
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
