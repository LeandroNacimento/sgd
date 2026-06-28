@props(['headers' => []])

<div class="overflow-x-auto shadow-sm ring-1 ring-slate-200 rounded-lg bg-white">
    <table class="min-w-full divide-y divide-slate-200">
        @if($headers)
            <thead class="bg-slate-50">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @else
            @if(isset($header))
                <thead class="bg-slate-50">
                    {{ $header }}
                </thead>
            @endif
        @endif
        
        <tbody class="divide-y divide-slate-200 bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
