@props(['header' => null, 'footer' => null, 'noPadding' => false])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden']) }}>
    @if($header)
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
            {{ $header }}
        </div>
    @endif

    <div class="{{ $noPadding ? '' : 'p-6' }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $footer }}
        </div>
    @endif
</div>
