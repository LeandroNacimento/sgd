@props(['disabled' => false, 'error' => false])

@php
    $baseClasses = 'block w-full rounded-md shadow-sm sm:text-sm ds-transition py-2 pl-3 pr-10';
    $stateClasses = $error
        ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500'
        : 'border-slate-300 focus:border-blue-500 focus:ring-blue-500';
    $disabledClasses = $disabled ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : 'bg-white';
    
    $classes = "{$baseClasses} {$stateClasses} {$disabledClasses}";
@endphp

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>
    {{ $slot }}
</select>
