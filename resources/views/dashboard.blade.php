<x-app-layout>
    <x-slot name="title">
        {{ __('dashboard.title') }}
    </x-slot>

    <!-- Top Row: KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-ds.kpi-card title="{{ __('dashboard.total_documents') }}" value="{{ $total_documents }}" color="blue"
            href="{{ route('documents.index') }}">
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </x-slot>
        </x-ds.kpi-card>

        @php
            $inReviewState = $documents_by_state['In Review'] ?? null;
            $inReviewCount = $inReviewState['count'] ?? 0;
            $inReviewId = $inReviewState['id'] ?? null;
        @endphp
        <x-ds.kpi-card title="{{ __('dashboard.awaiting_review') }}" value="{{ $inReviewCount }}" color="orange"
            href="{{ $inReviewId ? route('documents.index', ['document_state_id' => $inReviewId]) : route('documents.index') }}">
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot>
        </x-ds.kpi-card>

        @php
            $publishedState = $documents_by_state['Published'] ?? null;
            $publishedCount = $publishedState['count'] ?? 0;
            $publishedId = $publishedState['id'] ?? null;
        @endphp
        <x-ds.kpi-card title="{{ __('dashboard.published') }}" value="{{ $publishedCount }}" color="green"
            href="{{ $publishedId ? route('documents.index', ['document_state_id' => $publishedId]) : route('documents.index') }}">
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot>
        </x-ds.kpi-card>
    </div>

    <!-- Middle Row: Distribution & Recent Docs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <x-ds.card noPadding="true">
            <x-slot name="header">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('dashboard.by_state') }}</h2>
            </x-slot>
            <ul class="divide-y divide-slate-200">
                @forelse($documents_by_state as $name => $data)
                    <li>
                        <a href="{{ route('documents.index', ['document_state_id' => $data['id']]) }}"
                            class="flex justify-between items-center p-4 hover:bg-slate-50 ds-transition">
                            <span class="text-sm font-medium text-slate-700">
                                {{ \App\Enums\DocumentStateName::tryFrom($name)?->label() ?? $name }}
                            </span>
                            <span
                                class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">{{ $data['count'] }}</span>
                        </a>
                    </li>
                @empty
                    <li class="p-4 text-sm text-slate-500 italic text-center">{{ __('dashboard.no_states') }}</li>
                @endforelse
            </ul>
        </x-ds.card>

        <x-ds.card noPadding="true">
            <x-slot name="header">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('dashboard.recent_documents') }}</h2>
            </x-slot>
            <ul class="divide-y divide-slate-200">
                @forelse($recent_documents as $doc)
                    <li>
                        <a href="{{ route('documents.show', $doc) }}" class="block hover:bg-slate-50 p-4 ds-transition">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-blue-600 truncate">{{ $doc->code }} -
                                    {{ $doc->currentVersion->title }}</p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <x-ds.status-badge :state="$doc->currentVersion->documentState"
                                        :label="$doc->currentVersion->stateLabel()" />
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-slate-500">
                                        {{ $doc->category->label() }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-slate-500 sm:mt-0">
                                    <p>{{ __('dashboard.created_ago', ['time' => $doc->created_at->diffForHumans()]) }}</p>
                                </div>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="p-4"><x-ds.empty-state title="{{ __('dashboard.no_documents') }}" /></li>
                @endforelse
            </ul>
        </x-ds.card>
    </div>

    <!-- Bottom Row: Recent Activity -->
    <x-ds.card>
        <x-slot name="header">
            <h2 class="text-lg font-semibold text-slate-900">{{ __('dashboard.recent_activity') }}</h2>
        </x-slot>
        @if($recent_activities->count() > 0)
            @php
                $timelineItems = $recent_activities->map(function ($activity) {
                    $metadata = [];
                    $title = $activity->description;

                    if ($activity->event === 'document.created')
                        $title = __('dashboard.activity_created');
                    elseif ($activity->event === 'document.updated')
                        $title = __('dashboard.activity_updated');
                    elseif ($activity->event === 'document.deleted')
                        $title = __('dashboard.activity_deleted');
                    elseif ($activity->event === 'attachment.uploaded')
                        $title = __('dashboard.activity_uploaded');
                    elseif ($activity->event === 'attachment.deleted')
                        $title = __('dashboard.activity_att_deleted');
                    elseif ($activity->event === 'workflow.transition') {
                        $fromKey = $activity->properties['from_state'] ?? '?';
                        $toKey   = $activity->properties['to_state']   ?? '?';

                        $from = \App\Enums\DocumentStateName::tryFrom($fromKey)?->label() ?? $fromKey;
                        $to   = \App\Enums\DocumentStateName::tryFrom($toKey)?->label()   ?? $toKey;

                        $title = __('dashboard.activity_transitioned', [
                            'from' => $from,
                            'to'   => $to,
                        ]);
                    }

                    return new \App\DTOs\TimelineItemData(
                        type: 'audit',
                        title: $title,
                        timestamp: $activity->created_at,
                        actor: $activity->causer->name ?? __('documents.audit_causer'),
                        description: $activity->subject && $activity->subject->document ? $activity->subject->document->code . ' (' . $activity->subject->semantic_version . ')' : __('dashboard.deleted_document'),
                        metadata: $metadata,
                        url: $activity->subject && $activity->subject->document ? route('documents.show', $activity->subject->document) : null
                    );
                });
            @endphp
            <x-ds.timeline :items="$timelineItems" />
        @else
            <x-ds.empty-state title="{{ __('dashboard.no_activity') }}" />
        @endif
    </x-ds.card>
</x-app-layout>