@props(['variant' => 'primary', 'type' => 'button', 'href' => null, 'size' => 'md'])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md ds-transition ds-focus-ring focus:ring-offset-white border';
    
    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-6 py-3 text-lg',
        default => 'px-4 py-2 text-sm',
    };
    
    $variantClasses = match($variant) {
        'primary' => 'bg-blue-600 text-white border-transparent hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50 focus:ring-slate-500',
        'danger' => 'bg-red-600 text-white border-transparent hover:bg-red-700 focus:ring-red-500',
        default => 'bg-blue-600 text-white border-transparent hover:bg-blue-700 focus:ring-blue-500',
    };
    
    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
