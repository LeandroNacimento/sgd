@props(['state', 'label'])

@php
    // Mapping state name to color system
    // Draft -> amber, In Review -> blue, Published -> green, Archived -> gray
    $stateName = strtolower(is_object($state) ? $state->name : $state);
    
    $colorClasses = match($stateName) {
        'draft' => 'bg-amber-100 text-amber-800 border-amber-200',
        'in review' => 'bg-blue-100 text-blue-800 border-blue-200',
        'published' => 'bg-green-100 text-green-800 border-green-200',
        'archived' => 'bg-slate-100 text-slate-800 border-slate-200',
        default => 'bg-slate-100 text-slate-800 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {$colorClasses}"]) }}>
    {{ $label }}
</span>
