@props(['title', 'description' => null, 'icon' => null])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4']) }}>
    @if($icon)
        <div class="mx-auto h-12 w-12 text-slate-400 mb-4 flex justify-center">
            {{ $icon }}
        </div>
    @else
        <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    @endif
    
    <h3 class="mt-2 text-sm font-semibold text-slate-900">{{ $title }}</h3>
    
    @if($description)
        <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
    @endif
    
    @if(isset($action))
        <div class="mt-6">
            {{ $action }}
        </div>
    @endif
</div>
