@props(['disabled' => false, 'error' => false])

@php
    $baseClasses = 'block w-full rounded-md shadow-sm sm:text-sm ds-transition';
    $stateClasses = $error
        ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 placeholder-red-300'
        : 'border-slate-300 focus:border-blue-500 focus:ring-blue-500';
    $disabledClasses = $disabled ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '';
    
    $classes = "{$baseClasses} {$stateClasses} {$disabledClasses}";
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>
