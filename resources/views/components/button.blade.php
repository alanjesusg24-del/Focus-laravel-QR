@props(['type' => 'button', 'variant' => 'primary', 'size' => 'md'])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

    $variants = [
        'primary' => 'bg-institutional-blue text-white hover:bg-blue-700 focus:ring-institutional-blue',
        'secondary' => 'bg-institutional-orange text-white hover:bg-orange-700 focus:ring-institutional-orange',
        'outline' => 'border-2 border-institutional-blue text-institutional-blue hover:bg-institutional-blue hover:text-white focus:ring-institutional-blue',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $classes = $baseClasses . ' ' . $variantClass . ' ' . $sizeClass;
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
