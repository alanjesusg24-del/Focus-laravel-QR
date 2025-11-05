@extends('layouts.order-qr')

@section('title', 'Payment Cancelled')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 text-center">
    <div class="bg-white border-2 border-yellow-400 rounded-lg p-8">
        <div class="mb-4">
            <svg class="w-20 h-20 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-yellow-600 mb-4">Payment Cancelled</h1>

        <p class="text-gray-700 mb-6">
            Your payment was cancelled. No charges have been made to your account.
        </p>

        <p class="text-gray-600 mb-6">
            If you experienced any issues during the payment process, please contact our support team.
        </p>

        <div class="space-y-3">
            <a href="{{ route('order-qr.payment.index') }}" class="block px-6 py-3 text-base font-medium text-white bg-institutional-blue hover:bg-institutional-blue/80 rounded-full transition-transform duration-150 active:scale-95">
                Try Again
            </a>

            <a href="{{ route('order-qr.dashboard') }}" class="block px-6 py-3 text-base font-medium text-institutional-gray bg-transparent border-2 border-institutional-gray hover:bg-institutional-gray/10 rounded-full transition-transform duration-150 active:scale-95">
                Return to Dashboard
            </a>

            <a href="{{ route('order-qr.support.create') }}" class="block text-institutional-gray hover:underline">
                Contact Support
            </a>
        </div>
    </div>

    <div class="bg-gray-50 border border-gray-200 p-4 rounded">
        <h3 class="font-semibold text-gray-800 mb-2">Need Help?</h3>
        <p class="text-sm text-gray-600">
            Our support team is available to assist you with any payment issues or questions you may have.
        </p>
    </div>
</div>
@endsection
