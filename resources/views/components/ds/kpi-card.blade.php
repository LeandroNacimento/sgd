@props(['title', 'value', 'icon' => null, 'href' => null, 'color' => 'blue'])

@php
    $iconColorClasses = match($color) {
        'blue' => 'bg-blue-100 text-blue-600',
        'orange' => 'bg-orange-100 text-orange-600',
        'green' => 'bg-green-100 text-green-600',
        'slate' => 'bg-slate-100 text-slate-600',
        default => 'bg-blue-100 text-blue-600',
    };
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'block bg-white rounded-lg shadow-sm border border-slate-200 hover:border-slate-300 hover:shadow ds-transition']) }}>
@else
<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-slate-200']) }}>
@endif
    <div class="p-6 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 truncate">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="p-3 rounded-full {{ $iconColorClasses }}">
                {{ $icon }}
            </div>
        @endif
    </div>
@if($href)
</a>
@else
</div>
@endif
