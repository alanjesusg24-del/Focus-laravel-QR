@extends('layouts.order-qr')

@section('title', 'Checkout')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-institutional-blue">Checkout</h1>
        <a href="{{ route('order-qr.payment.index') }}" class="text-institutional-blue hover:underline">
            ← Back to plans
        </a>
    </div>

    <div class="border-2 border-institutional-blue rounded-lg p-6 bg-white">
        <h2 class="text-xl font-bold text-institutional-blue mb-4">Order Summary</h2>

        <div class="space-y-3 mb-6">
            <div class="flex justify-between">
                <span class="text-gray-600">Plan:</span>
                <span class="font-semibold">{{ $plan->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Duration:</span>
                <span class="font-semibold">{{ $plan->duration_days }} days</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Data Retention:</span>
                <span class="font-semibold">{{ $plan->retention_days }} days</span>
            </div>
            <hr>
            <div class="flex justify-between text-lg">
                <span class="font-bold">Total:</span>
                <span class="font-bold text-institutional-orange">${{ number_format($plan->price, 2) }} MXN</span>
            </div>
        </div>

        <div class="bg-institutional-gray/10 p-4 rounded mb-6">
            <h3 class="font-semibold mb-2">Business Information</h3>
            <p class="text-sm text-gray-700">{{ $business->business_name }}</p>
            <p class="text-sm text-gray-600">{{ $business->email }}</p>
            <p class="text-sm text-gray-600">RFC: {{ $business->rfc }}</p>
        </div>

        <form action="{{ route('order-qr.payment.create-checkout-session', $plan) }}" method="POST">
            @csrf
            <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-4">
                <p class="text-sm text-blue-800">
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    You will be redirected to Stripe's secure payment page to complete your purchase.
                </p>
            </div>

            <button type="submit" class="w-full px-4 py-3 text-base font-medium text-white bg-institutional-blue hover:bg-institutional-blue/80 rounded-full transition-transform duration-150 active:scale-95">
                Proceed to Payment
            </button>
        </form>
    </div>

    <div class="text-center text-sm text-gray-500">
        <p>🔒 Secure payment powered by Stripe</p>
        <p class="mt-2">By proceeding, you agree to our terms of service</p>
    </div>
</div>
@endsection
