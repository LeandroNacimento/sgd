<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <!-- Top Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <a href="{{ route('documents.index') }}" class="ds-card hover:bg-gray-50 transition block">
            <div class="ds-card-body flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">Total Documents</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $total_documents }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
        </a>

        @php
            $inReviewState = $documents_by_state['In Review'] ?? null;
            $inReviewCount = $inReviewState['count'] ?? 0;
            $inReviewId = $inReviewState['id'] ?? null;
        @endphp
        <a href="{{ $inReviewId ? route('documents.index', ['document_state_id' => $inReviewId]) : route('documents.index') }}" class="ds-card hover:bg-gray-50 transition block">
            <div class="ds-card-body flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">Awaiting Review</p>
                    <p class="mt-1 text-3xl font-semibold text-orange-600">{{ $inReviewCount }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </a>

        @php
            $publishedState = $documents_by_state['Published'] ?? null;
            $publishedCount = $publishedState['count'] ?? 0;
            $publishedId = $publishedState['id'] ?? null;
        @endphp
        <a href="{{ $publishedId ? route('documents.index', ['document_state_id' => $publishedId]) : route('documents.index') }}" class="ds-card hover:bg-gray-50 transition block">
            <div class="ds-card-body flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">Published</p>
                    <p class="mt-1 text-3xl font-semibold text-green-600">{{ $publishedCount }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Distribution by State -->
        <div class="ds-card">
            <div class="ds-card-header">
                <h2 class="text-lg font-semibold ds-text-primary">Documents by State</h2>
            </div>
            <div class="ds-card-body p-0">
                <ul class="divide-y divide-gray-200">
                    @forelse($documents_by_state as $name => $data)
                        <li>
                            <a href="{{ route('documents.index', ['document_state_id' => $data['id']]) }}" class="flex justify-between items-center p-4 hover:bg-gray-50 transition">
                                <span class="text-sm font-medium text-gray-700">{{ $name }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $data['count'] }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="p-4 text-sm text-gray-500 italic text-center">No states found.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Recently Created -->
        <div class="ds-card">
            <div class="ds-card-header">
                <h2 class="text-lg font-semibold ds-text-primary">Recent Documents</h2>
            </div>
            <div class="ds-card-body p-0">
                <ul class="divide-y divide-gray-200">
                    @forelse($recent_documents as $doc)
                        <li>
                            <a href="{{ route('documents.show', $doc) }}" class="block hover:bg-gray-50 p-4 transition">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-blue-600 truncate">{{ $doc->code }} - {{ $doc->title }}</p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $doc->documentState->name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            {{ $doc->category->name }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <p>Created {{ $doc->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="p-4 text-sm text-gray-500 italic text-center">No documents found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="ds-card mb-6">
        <div class="ds-card-header">
            <h2 class="text-lg font-semibold ds-text-primary">Recent Activity</h2>
        </div>
        <div class="ds-card-body">
            @if($recent_activities->count() > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($recent_activities as $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    <span class="font-medium text-gray-900">{{ $activity->causer->name ?? 'System' }}</span>
                                                    
                                                    @if($activity->event === 'document.created')
                                                        created document
                                                    @elseif($activity->event === 'document.updated')
                                                        updated document
                                                    @elseif($activity->event === 'document.deleted')
                                                        deleted document
                                                    @elseif($activity->event === 'attachment.uploaded')
                                                        uploaded attachment to
                                                    @elseif($activity->event === 'attachment.deleted')
                                                        deleted attachment from
                                                    @elseif($activity->event === 'workflow.transition')
                                                        transitioned document from <strong>{{ $activity->properties['from_state'] ?? 'Unknown' }}</strong> to <strong>{{ $activity->properties['to_state'] ?? 'Unknown' }}</strong>
                                                    @else
                                                        {{ $activity->description }}
                                                    @endif
                                                    
                                                    @if($activity->subject)
                                                        <a href="{{ route('documents.show', $activity->subject) }}" class="font-medium text-blue-600 hover:underline">{{ $activity->subject->code }}</a>
                                                    @else
                                                        <span class="font-medium text-gray-900">(Deleted Document)</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                <time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-sm text-gray-500 italic text-center py-4">No recent activity.</p>
            @endif
        </div>
    </div>
</x-app-layout>
