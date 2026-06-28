@props(['items' => []])

<div class="flow-root">
    <ul role="list" class="-mb-8">
        @foreach($items as $item)
            <li>
                <div class="relative pb-8">
                    @if(!$loop->last)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                    @endif
                    <div class="relative flex space-x-3">
                        <div>
                            @if($item->type === 'version')
                                <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                    </svg>
                                </span>
                            @else
                                <span class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm text-slate-500">
                                    @if($item->actor)
                                        <span class="font-medium text-slate-900">{{ $item->actor }}</span>
                                    @endif
                                    
                                    {!! $item->title !!}
                                    
                                    @if($item->url)
                                        <a href="{{ $item->url }}" class="font-medium text-blue-600 hover:underline">{{ $item->description }}</a>
                                    @endif
                                </p>
                                
                                @if(!$item->url && $item->description)
                                    <p class="mt-0.5 text-sm text-slate-500">{{ $item->description }}</p>
                                @endif
                                
                                @if(!empty($item->metadata))
                                    <div class="mt-2 text-xs text-slate-500 bg-slate-50 p-2 rounded border border-slate-100">
                                        @foreach($item->metadata as $key => $value)
                                            @if(is_array($value))
                                                <div class="font-medium mb-1">{{ $key }}:</div>
                                                <ul class="list-disc list-inside ml-2">
                                                    @foreach($value as $v)
                                                        <li>{{ $v }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="{{ $key === 'transition' ? 'text-blue-700 font-medium' : '' }}">{{ $value }}</div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="whitespace-nowrap text-right text-sm text-slate-500">
                                <time datetime="{{ $item->timestamp->toIso8601String() }}">{{ $item->timestamp->diffForHumans() }}</time>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
