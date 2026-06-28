@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'pb-5 border-b border-slate-200']) }}>
    <h3 class="text-lg leading-6 font-semibold text-slate-900">{{ $title }}</h3>
    @if($description)
        <p class="mt-2 max-w-4xl text-sm text-slate-500">{{ $description }}</p>
    @endif
</div>
