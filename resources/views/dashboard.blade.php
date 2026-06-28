<x-app-layout>
    <x-slot name="header">
        {{ __('dashboard.title') }}
    </x-slot>

    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <a href="{{ route('documents.index') }}" class="ds-card hover:bg-gray-50 transition block">
            <div class="ds-card-body flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.total_documents') }}</p>
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
            $inReviewId    = $inReviewState['id'] ?? null;
        @endphp
        <a href="{{ $inReviewId ? route('documents.index', ['document_state_id' => $inReviewId]) : route('documents.index') }}" class="ds-card hover:bg-gray-50 transition block">
            <div class="ds-card-body flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.awaiting_review') }}</p>
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
            $publishedId    = $publishedState['id'] ?? null;
        @endphp
        <a href="{{ $publishedId ? route('documents.index', ['document_state_id' => $publishedId]) : route('documents.index') }}" class="ds-card hover:bg-gray-50 transition block">
            <div class="ds-card-body flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.published') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-green-600">{{ $publishedCount }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Distribución por estado -->
        <div class="ds-card">
            <div class="ds-card-header">
                <h2 class="text-lg font-semibold ds-text-primary">{{ __('dashboard.by_state') }}</h2>
            </div>
            <div class="ds-card-body p-0">
                <ul class="divide-y divide-gray-200">
                    @forelse($documents_by_state as $name => $data)
                        <li>
                            <a href="{{ route('documents.index', ['document_state_id' => $data['id']]) }}" class="flex justify-between items-center p-4 hover:bg-gray-50 transition">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ \App\Enums\DocumentStateName::tryFrom($name)?->label() ?? $name }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $data['count'] }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="p-4 text-sm text-gray-500 italic text-center">{{ __('dashboard.no_states') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Documentos recientes -->
        <div class="ds-card">
            <div class="ds-card-header">
                <h2 class="text-lg font-semibold ds-text-primary">{{ __('dashboard.recent_documents') }}</h2>
            </div>
            <div class="ds-card-body p-0">
                <ul class="divide-y divide-gray-200">
                    @forelse($recent_documents as $doc)
                        <li>
                            <a href="{{ route('documents.show', $doc) }}" class="block hover:bg-gray-50 p-4 transition">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-blue-600 truncate">{{ $doc->code }} - {{ $doc->currentVersion->title }}</p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $doc->currentVersion->stateLabel() }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            {{ $doc->category->label() }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <p>{{ __('dashboard.created_ago', ['time' => $doc->created_at->diffForHumans()]) }}</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="p-4 text-sm text-gray-500 italic text-center">{{ __('dashboard.no_documents') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Actividad reciente -->
    <div class="ds-card mb-6">
        <div class="ds-card-header">
            <h2 class="text-lg font-semibold ds-text-primary">{{ __('dashboard.recent_activity') }}</h2>
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
                                                    <span class="font-medium text-gray-900">{{ $activity->causer->name ?? __('documents.audit_causer') }}</span>

                                                    @if($activity->event === 'document.created')
                                                        {{ __('dashboard.activity_created') }}
                                                    @elseif($activity->event === 'document.updated')
                                                        {{ __('dashboard.activity_updated') }}
                                                    @elseif($activity->event === 'document.deleted')
                                                        {{ __('dashboard.activity_deleted') }}
                                                    @elseif($activity->event === 'attachment.uploaded')
                                                        {{ __('dashboard.activity_uploaded') }}
                                                    @elseif($activity->event === 'attachment.deleted')
                                                        {{ __('dashboard.activity_att_deleted') }}
                                                    @elseif($activity->event === 'workflow.transition')
                                                        {!! __('dashboard.activity_transitioned', [
                                                            'from' => $activity->properties['from_state'] ?? '?',
                                                            'to'   => $activity->properties['to_state']   ?? '?',
                                                        ]) !!}
                                                    @else
                                                        {{ $activity->description }}
                                                    @endif

                                                    @if($activity->subject && $activity->subject->document)
                                                        <a href="{{ route('documents.show', $activity->subject->document) }}" class="font-medium text-blue-600 hover:underline">{{ $activity->subject->document->code }} ({{ $activity->subject->semantic_version }})</a>
                                                    @else
                                                        <span class="font-medium text-gray-900">{{ __('dashboard.deleted_document') }}</span>
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
                <p class="text-sm text-gray-500 italic text-center py-4">{{ __('dashboard.no_activity') }}</p>
            @endif
        </div>
    </div>
</x-app-layout>
