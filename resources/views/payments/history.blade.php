@extends('layouts.order-qr')

@section('title', 'Payment History')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-institutional-blue">Payment History</h1>
        <a href="{{ route('business.payments.index') }}" class="text-institutional-blue hover:underline">
            ← Back to plans
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white border-2 border-institutional-blue rounded-lg p-4">
            <p class="text-sm text-gray-600">Total Payments</p>
            <p class="text-2xl font-bold text-institutional-blue">{{ $statistics['total_payments'] }}</p>
        </div>
        <div class="bg-white border-2 border-green-400 rounded-lg p-4">
            <p class="text-sm text-gray-600">Completed</p>
            <p class="text-2xl font-bold text-green-600">{{ $statistics['completed'] }}</p>
        </div>
        <div class="bg-white border-2 border-yellow-400 rounded-lg p-4">
            <p class="text-sm text-gray-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $statistics['pending'] }}</p>
        </div>
        <div class="bg-white border-2 border-institutional-orange rounded-lg p-4">
            <p class="text-sm text-gray-600">Total Revenue</p>
            <p class="text-2xl font-bold text-institutional-orange">${{ number_format($statistics['total_revenue'], 2) }}</p>
        </div>
    </div>

    <div class="bg-white border-2 border-institutional-blue rounded-lg p-6">
        <h2 class="text-xl font-bold text-institutional-blue mb-4">All Payments</h2>

        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-institutional-gray/20">
                            <th class="p-3 text-left">Payment ID</th>
                            <th class="p-3 text-left">Plan</th>
                            <th class="p-3 text-left">Amount</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Payment Date</th>
                            <th class="p-3 text-left">Next Payment</th>
                            <th class="p-3 text-left">Stripe ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 font-mono text-sm">#{{ $payment->payment_id }}</td>
                                <td class="p-3">{{ $payment->plan->name }}</td>
                                <td class="p-3 font-semibold">${{ number_format($payment->amount, 2) }} MXN</td>
                                <td class="p-3">
                                    <span class="px-3 py-1 rounded-full text-sm
                                        @if($payment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                        @elseif($payment->status === 'refunded') bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="p-3">{{ $payment->payment_date->format('M d, Y H:i') }}</td>
                                <td class="p-3">
                                    @if($payment->next_payment_date)
                                        {{ $payment->next_payment_date->format('M d, Y') }}
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="p-3 font-mono text-xs text-gray-600">
                                    @if($payment->stripe_payment_id)
                                        {{ Str::limit($payment->stripe_payment_id, 15) }}
                                    @elseif($payment->stripe_subscription_id)
                                        {{ Str::limit($payment->stripe_subscription_id, 15) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <p class="text-lg font-semibold mb-2">No payment history found</p>
                <p class="mb-4">You haven't made any payments yet</p>
                <a href="{{ route('business.payments.index') }}" class="text-institutional-blue hover:underline">
                    View available plans →
                </a>
            </div>
        @endif
    </div>

    @if($payments->count() > 0 && $payments->first()->stripe_subscription_id)
        <div class="bg-white border-2 border-red-400 rounded-lg p-6">
            <h3 class="text-lg font-bold text-red-600 mb-2">Cancel Subscription</h3>
            <p class="text-gray-700 mb-4">
                If you wish to cancel your recurring subscription, you can do so here. Your access will remain active until the end of the current billing period.
            </p>
            <form action="{{ route('order-qr.payment.cancel-subscription') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2 text-base font-medium text-white bg-red-600 hover:bg-red-700 rounded-full transition-transform duration-150 active:scale-95">
                    Cancel Subscription
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
