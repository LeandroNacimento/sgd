<x-app-layout>
    <x-slot name="breadcrumbs">
        <a href="{{ route('documents.index') }}" class="hover:text-slate-900 ds-transition">{{ __('documents.back_to_documents') }}</a>
    </x-slot>
    <x-slot name="title">
        <div class="flex items-center gap-2">
            <span>{{ $document->code }} - {{ $version->title ?? __('documents.no_documents') }}</span>
            <x-ds.badge color="slate">{{ $version->semantic_version }}</x-ds.badge>
            <x-ds.status-badge :state="$version->documentState" :label="$version->stateLabel()" />
        </div>
    </x-slot>
    <x-slot name="actions">
        @if($document->current_version_id === $version->id)
            @can('update', $document)
                <x-ds.button href="{{ route('documents.edit', $document) }}" variant="secondary" size="sm">{{ __('documents.edit_button') }}</x-ds.button>
            @endcan
            @if($version->isPublished())
                @can('createVersion', $document)
                    <form action="{{ route('documents.versions.store', $document) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('documents.confirm_new_version') }}');">
                        @csrf
                        <x-ds.button type="submit" variant="primary" size="sm">{{ __('documents.workflow_new_version') }}</x-ds.button>
                    </form>
                @endcan
            @endif
            @can('delete', $document)
                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('documents.confirm_delete') }}');">
                    @csrf
                    @method('DELETE')
                    <x-ds.button type="submit" variant="danger" size="sm">{{ __('documents.delete_button') }}</x-ds.button>
                </form>
            @endcan
        @endif
    </x-slot>

    @if($document->current_version_id !== $version->id)
        <div class="mb-6 p-4 rounded-md bg-yellow-50 border-l-4 border-yellow-400 flex items-center justify-between shadow-sm">
            <div class="text-yellow-800">
                <strong>{{ __('documents.version_notice', ['version' => $version->semantic_version]) }}</strong>
            </div>
            <a href="{{ route('documents.show', $document) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">{{ __('documents.version_view_current') }}</a>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-md bg-red-50 border-l-4 border-red-500 text-red-700 shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 rounded-md bg-green-50 border-l-4 border-green-500 text-green-800 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ds.card>
                <x-slot name="header">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('documents.section_details') }}</h2>
                </x-slot>
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">{{ __('documents.field_title') }}</h3>
                        <p class="mt-1 text-slate-900">{{ $version->title }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">{{ __('documents.field_description') }}</h3>
                        <p class="mt-1 text-slate-900 whitespace-pre-wrap">{{ $version->description ?: '—' }}</p>
                    </div>
                </div>
            </x-ds.card>

            <x-ds.card noPadding="true">
                <x-slot name="header">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('documents.section_versions') }}</h2>
                </x-slot>
                <ul class="divide-y divide-slate-200">
                    @foreach($document->versions()->orderBy('version_number', 'desc')->get() as $histVersion)
                        <li class="p-4 hover:bg-slate-50 ds-transition {{ $histVersion->id === $version->id ? 'bg-blue-50/30' : '' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="font-medium text-slate-900">{{ $histVersion->semantic_version }}</span>
                                    <x-ds.status-badge :state="$histVersion->documentState" :label="$histVersion->stateLabel()" />
                                    @if($document->current_version_id === $histVersion->id)
                                        <x-ds.badge color="blue">{{ __('documents.version_current') }}</x-ds.badge>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 text-sm text-slate-500">
                                    <span>{{ $histVersion->created_at->format('d/m/Y H:i') }}</span>
                                    @if($histVersion->id !== $version->id)
                                        <a href="{{ route('documents.show', ['document' => $document, 'version_id' => $histVersion->id]) }}" class="text-blue-600 hover:text-blue-800 font-medium">{{ __('documents.version_view') }}</a>
                                    @else
                                        <span class="text-slate-400 cursor-default">{{ __('documents.version_viewing') }}</span>
                                    @endif
                                    
                                    @if($document->current_version_id !== $histVersion->id)
                                        @can('revertVersion', $document)
                                            <form action="{{ route('documents.versions.revert', [$document, $histVersion]) }}" method="POST" class="inline-block border-l pl-4 border-slate-200" onsubmit="return confirm('{{ __('documents.confirm_revert') }}');">
                                                @csrf
                                                <button type="submit" class="text-amber-600 hover:text-amber-800 font-medium">{{ __('documents.version_revert') }}</button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </x-ds.card>

            <x-ds.card>
                <x-slot name="header">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('documents.section_attachments') }} ({{ $version->semantic_version }})</h2>
                </x-slot>
                
                @php
                    $attachments = $version->getMedia('attachments');
                @endphp

                @if($attachments->count() > 0)
                    <ul class="divide-y divide-slate-200 border border-slate-200 rounded-md bg-white">
                        @foreach($attachments as $attachment)
                            <li class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center space-x-3 truncate">
                                    <div class="p-2 bg-slate-100 rounded-lg text-slate-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    </div>
                                    <span class="text-sm font-medium text-slate-900 truncate">{{ $attachment->file_name }}</span>
                                    <span class="text-xs text-slate-500">({{ number_format($attachment->size / 1024, 2) }} KB)</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <x-ds.button href="{{ route('documents.attachments.download', [$document, $attachment]) }}" variant="secondary" size="sm">{{ __('documents.attachment_download') }}</x-ds.button>
                                    @if($document->current_version_id === $version->id)
                                        @can('update', $document)
                                            <form action="{{ route('documents.attachments.destroy', [$document, $attachment]) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_delete_attachment') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <x-ds.button type="submit" variant="danger" size="sm">{{ __('documents.attachment_delete') }}</x-ds.button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <x-ds.empty-state title="{{ __('documents.attachment_none') }}" />
                @endif

                @if($document->current_version_id === $version->id)
                    @can('update', $document)
                        @if($attachments->count() < 5)
                            <div class="mt-6 pt-6 border-t border-slate-200">
                                <form action="{{ route('documents.attachments.store', $document) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-4">
                                    @csrf
                                    <input type="file" name="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" required>
                                    <x-ds.button type="submit" variant="primary" class="whitespace-nowrap">{{ __('documents.attachment_upload') }}</x-ds.button>
                                </form>
                                <p class="text-xs text-slate-500 mt-2">{{ __('documents.attachment_hint') }}</p>
                            </div>
                        @else
                            <div class="mt-6 pt-6 border-t border-slate-200 text-sm text-amber-600 bg-amber-50 p-4 rounded-md">
                                {{ __('documents.attachment_limit') }}
                            </div>
                        @endif
                    @endcan
                @endif
            </x-ds.card>

            @if(auth()->user()->can('is-admin') && isset($activities) && $activities->count() > 0)
                <x-ds.card>
                    <x-slot name="header">
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('documents.audit_trail') }} ({{ $version->semantic_version }})</h2>
                    </x-slot>
                    
                    @php
                        $timelineItems = $activities->map(function($activity) {
                            $metadata = [];
                            
                            $title = match($activity->event) {
                                'document.created' => __('documents.audit_event_created'),
                                'document.updated' => __('documents.audit_event_updated'),
                                'document.deleted' => __('documents.audit_event_deleted'),
                                'attachment.uploaded' => __('documents.audit_event_attachment_uploaded', ['filename' => $activity->properties['filename'] ?? '']),
                                'attachment.deleted' => __('documents.audit_event_attachment_deleted', ['filename' => $activity->properties['filename'] ?? '']),
                                'workflow.transition' => __('documents.audit_event_workflow_transition'),
                                default => $activity->description
                            };
                            
                            if($activity->event === 'document.updated' && $activity->properties->count() > 0) {
                                $changes = [];
                                foreach($activity->properties as $key => $value) {
                                    $changes[] = __('documents.audit_field_updated', ['field' => ucfirst($key)]);
                                }
                                $metadata[__('documents.audit_changes')] = $changes;
                            }
                            
                            if($activity->event === 'workflow.transition') {
                                $fromKey = $activity->properties['from_state'] ?? '?';
                                $toKey = $activity->properties['to_state'] ?? '?';
                                
                                $from = App\Enums\DocumentStateName::tryFrom($fromKey)?->label() ?? $fromKey;
                                $to = App\Enums\DocumentStateName::tryFrom($toKey)?->label() ?? $toKey;

                                $metadata['transition'] = __('documents.audit_transition', [
                                    'from' => $from,
                                    'to'   => $to,
                                ]);
                            }

                            return new \App\DTOs\TimelineItemData(
                                type: 'audit',
                                title: $title,
                                timestamp: $activity->created_at,
                                actor: $activity->causer->name ?? __('documents.audit_causer'),
                                description: null,
                                metadata: $metadata,
                                url: null
                            );
                        });
                    @endphp
                    <x-ds.timeline :items="$timelineItems" />
                </x-ds.card>
            @endif
        </div>

        <div class="space-y-6">
            @if($document->current_version_id === $version->id)
                <x-ds.card>
                    <x-slot name="header">
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('documents.section_workflow') }}</h2>
                    </x-slot>
                    <div class="space-y-3">
                        @can('submitForReview', $document)
                            <form action="{{ route('documents.workflow.submitForReview', $document) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_submit_review') }}');">
                                @csrf
                                <x-ds.button type="submit" variant="primary" class="w-full">{{ __('documents.workflow_submit_review') }}</x-ds.button>
                            </form>
                        @endcan

                        @can('publish', $document)
                            <form action="{{ route('documents.workflow.publish', $document) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_publish') }}');">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center font-medium rounded-md ds-transition px-4 py-2 text-sm bg-green-600 text-white hover:bg-green-700">{{ __('documents.workflow_publish') }}</button>
                            </form>
                        @endcan

                        @can('reject', $document)
                            <form action="{{ route('documents.workflow.reject', $document) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_reject') }}');">
                                @csrf
                                <x-ds.button type="submit" variant="danger" class="w-full">{{ __('documents.workflow_reject') }}</x-ds.button>
                            </form>
                        @endcan

                        @can('archive', $document)
                            <form action="{{ route('documents.workflow.archive', $document) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_archive') }}');">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center font-medium rounded-md ds-transition px-4 py-2 text-sm bg-slate-600 text-white hover:bg-slate-700">{{ __('documents.workflow_archive') }}</button>
                            </form>
                        @endcan

                        @if($version->isArchived())
                            <div class="text-sm text-slate-500 italic text-center p-3 bg-slate-50 rounded-md">
                                {{ __('documents.workflow_archived_note') }}
                            </div>
                        @endif

                        @if($version->isDraft() && !auth()->user()->can('is-operator'))
                            <div class="text-sm text-slate-500 italic text-center p-3 bg-slate-50 rounded-md border border-slate-100">
                                {{ __('documents.workflow_draft_note') }}
                            </div>
                        @endif
                    </div>
                </x-ds.card>
            @endif

            <x-ds.card>
                <x-slot name="header">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('documents.section_metadata') }}</h2>
                </x-slot>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">{{ __('documents.meta_state') }}</h3>
                        <div class="mt-1">
                            <x-ds.status-badge :state="$version->documentState" :label="$version->stateLabel()" />
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">{{ __('documents.meta_priority') }}</h3>
                        <p class="mt-1 text-slate-900 font-medium">{{ __('documents.priorities.' . $document->priority->value) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">{{ __('documents.meta_category') }}</h3>
                        <p class="mt-1 text-slate-900 font-medium">{{ $document->category->label() }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">{{ __('documents.meta_responsible') }}</h3>
                        <p class="mt-1 text-slate-900 font-medium">{{ $document->responsibleUser->name }}</p>
                    </div>
                    
                    <hr class="border-slate-200">
                    
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">{{ __('documents.meta_version_created') }}</h3>
                        <p class="mt-1 text-slate-900 text-sm">{{ $version->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500">{{ __('documents.meta_version_updated') }}</h3>
                        <p class="mt-1 text-slate-900 text-sm">{{ $version->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </x-ds.card>
        </div>
    </div>
</x-app-layout>
