<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('documents.index') }}" class="ds-text-secondary hover:underline text-sm mb-2 inline-block">{{ __('documents.back_to_documents') }}</a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold ds-text-primary">{{ $document->code }} - {{ $version->title ?? __('documents.no_documents') }}</h1>
                <span class="px-2.5 py-0.5 rounded-md text-sm font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                    {{ $version->semantic_version }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            @if($document->current_version_id === $version->id)
                @can('update', $document)
                    <a href="{{ route('documents.edit', $document) }}" class="ds-btn ds-btn-secondary">{{ __('documents.edit_button') }}</a>
                @endcan
                @if($version->isPublished())
                    @can('update', $document)
                        <form action="{{ route('documents.versions.store', $document) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('documents.confirm_new_version') }}');">
                            @csrf
                            <button type="submit" class="ds-btn bg-blue-600 hover:bg-blue-700 text-white">{{ __('documents.workflow_new_version') }}</button>
                        </form>
                    @endcan
                @endif
                @can('delete', $document)
                    <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('documents.confirm_delete') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ds-btn ds-btn-danger">{{ __('documents.delete_button') }}</button>
                    </form>
                @endcan
            @endif
        </div>
    </div>

    @if($document->current_version_id !== $version->id)
        <div class="mb-6 p-4 rounded ds-bg-surface border-l-4 border-yellow-500 text-yellow-800 shadow-sm flex items-center justify-between">
            <div>
                <strong>{{ __('documents.version_notice', ['version' => $version->semantic_version]) }}</strong>
            </div>
            <a href="{{ route('documents.show', $document) }}" class="ds-text-brand hover:underline text-sm">{{ __('documents.version_view_current') }}</a>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            @if($errors->any())
                <div class="p-4 rounded ds-bg-surface border-l-4 border-red-500 text-red-700 shadow-sm">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="p-4 rounded ds-bg-surface border-l-4 border-green-500 ds-text-primary shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="ds-card">
                <div class="ds-card-header">
                    <h2 class="text-lg font-semibold ds-text-primary">{{ __('documents.section_details') }}</h2>
                </div>
                <div class="ds-card-body space-y-4">
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">{{ __('documents.field_title') }}</h3>
                        <p class="ds-text-primary mt-1">{{ $version->title }}</p>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">{{ __('documents.field_description') }}</h3>
                        <p class="ds-text-primary mt-1 whitespace-pre-wrap">{{ $version->description ?: '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="ds-card">
                <div class="ds-card-header flex justify-between items-center">
                    <h2 class="text-lg font-semibold ds-text-primary">{{ __('documents.section_versions') }}</h2>
                </div>
                <div class="ds-card-body p-0">
                    <ul class="divide-y divide-gray-200">
                        @foreach($document->versions()->orderBy('version_number', 'desc')->get() as $histVersion)
                            <li class="p-4 hover:bg-gray-50 {{ $histVersion->id === $version->id ? 'bg-blue-50/50' : '' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="font-medium ds-text-primary">{{ $histVersion->semantic_version }}</span>
                                        @php
                                            $hBadge = match(strtolower($histVersion->documentState->name)) {
                                                'draft'     => 'ds-badge-draft',
                                                'in review' => 'ds-badge-in-review',
                                                'published' => 'ds-badge-published',
                                                'archived'  => 'ds-badge-archived',
                                                default     => 'ds-badge-draft'
                                            };
                                        @endphp
                                        <span class="ds-badge {{ $hBadge }}">{{ $histVersion->stateLabel() }}</span>
                                        @if($document->current_version_id === $histVersion->id)
                                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider bg-blue-100 px-2 py-0.5 rounded">{{ __('documents.version_current') }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span>{{ $histVersion->created_at->format('d/m/Y H:i') }}</span>
                                        @if($histVersion->id !== $version->id)
                                            <a href="{{ route('documents.show', ['document' => $document, 'version_id' => $histVersion->id]) }}" class="ds-text-brand hover:underline">{{ __('documents.version_view') }}</a>
                                        @else
                                            <span class="text-gray-400 cursor-default">{{ __('documents.version_viewing') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="ds-card">
                <div class="ds-card-header">
                    <h2 class="text-lg font-semibold ds-text-primary">{{ __('documents.section_attachments') }} ({{ $version->semantic_version }})</h2>
                </div>
                <div class="ds-card-body space-y-4">
                    @php
                        $attachments = $version->getMedia('attachments');
                    @endphp

                    @if($attachments->count() > 0)
                        <ul class="divide-y divide-gray-200 border border-gray-200 rounded">
                            @foreach($attachments as $attachment)
                                <li class="p-3 flex justify-between items-center bg-gray-50">
                                    <div class="flex items-center space-x-3 truncate">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <span class="text-sm font-medium text-gray-700 truncate">{{ $attachment->file_name }}</span>
                                        <span class="text-xs text-gray-500">({{ number_format($attachment->size / 1024, 2) }} KB)</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('documents.attachments.download', [$document, $attachment]) }}" class="ds-btn ds-btn-secondary py-1 px-2 text-xs">{{ __('documents.attachment_download') }}</a>
                                        @if($document->current_version_id === $version->id)
                                            @can('update', $document)
                                                <form action="{{ route('documents.attachments.destroy', [$document, $attachment]) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_delete_attachment') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="ds-btn ds-btn-danger py-1 px-2 text-xs">{{ __('documents.attachment_delete') }}</button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 italic">{{ __('documents.attachment_none') }}</p>
                    @endif

                    @if($document->current_version_id === $version->id)
                        @can('update', $document)
                            @if($attachments->count() < 5)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <form action="{{ route('documents.attachments.store', $document) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-4">
                                        @csrf
                                        <input type="file" name="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" required>
                                        <button type="submit" class="ds-btn ds-btn-primary whitespace-nowrap">{{ __('documents.attachment_upload') }}</button>
                                    </form>
                                    <p class="text-xs text-gray-500 mt-2">{{ __('documents.attachment_hint') }}</p>
                                </div>
                            @else
                                <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-yellow-600">
                                    {{ __('documents.attachment_limit') }}
                                </div>
                            @endif
                        @endcan
                    @endif
                </div>
            </div>

            @if(auth()->user()->can('is-admin') && isset($activities) && $activities->count() > 0)
                <div class="ds-card mt-6">
                    <div class="ds-card-header">
                        <h2 class="text-lg font-semibold ds-text-primary">{{ __('documents.audit_trail') }} ({{ $version->semantic_version }})</h2>
                    </div>
                    <div class="ds-card-body">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($activities as $activity)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            <span class="font-medium text-gray-900">{{ $activity->causer->name ?? __('documents.audit_causer') }}</span>
                                                            {{ $activity->description }}
                                                        </p>
                                                        @if($activity->event === 'document.updated' && $activity->properties->count() > 0)
                                                            <div class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded border border-gray-100">
                                                                <span class="font-medium">{{ __('documents.audit_changes') }}:</span>
                                                                <ul class="list-disc list-inside mt-1">
                                                                    @foreach($activity->properties as $key => $value)
                                                                        <li>{{ __('documents.audit_field_updated', ['field' => ucfirst($key)]) }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                        @if($activity->event === 'workflow.transition')
                                                            <div class="mt-2 text-xs text-blue-700 bg-blue-50 p-2 rounded border border-blue-100 font-medium">
                                                                {{ __('documents.audit_transition', [
                                                                    'from' => $activity->properties['from_state'] ?? '?',
                                                                    'to'   => $activity->properties['to_state']   ?? '?',
                                                                ]) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                        <time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->format('d/m/Y H:i') }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if($document->current_version_id === $version->id)
                <div class="ds-card">
                    <div class="ds-card-header">
                        <h2 class="text-lg font-semibold ds-text-primary">{{ __('documents.section_workflow') }}</h2>
                    </div>
                    <div class="ds-card-body space-y-3">
                        @can('submitForReview', $document)
                            <form action="{{ route('documents.workflow.submitForReview', $document) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_submit_review') }}');">
                                @csrf
                                <button type="submit" class="ds-btn ds-btn-primary w-full text-center justify-center">{{ __('documents.workflow_submit_review') }}</button>
                            </form>
                        @endcan

                        @can('publish', $document)
                            <form action="{{ route('documents.workflow.publish', $document) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_publish') }}');">
                                @csrf
                                <button type="submit" class="ds-btn bg-green-600 hover:bg-green-700 text-white w-full text-center justify-center border border-transparent shadow-sm rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">{{ __('documents.workflow_publish') }}</button>
                            </form>
                        @endcan

                        @can('reject', $document)
                            <form action="{{ route('documents.workflow.reject', $document) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_reject') }}');">
                                @csrf
                                <button type="submit" class="ds-btn ds-btn-danger w-full text-center justify-center">{{ __('documents.workflow_reject') }}</button>
                            </form>
                        @endcan

                        @can('archive', $document)
                            <form action="{{ route('documents.workflow.archive', $document) }}" method="POST" onsubmit="return confirm('{{ __('documents.confirm_archive') }}');">
                                @csrf
                                <button type="submit" class="ds-btn bg-gray-600 hover:bg-gray-700 text-white w-full text-center justify-center border border-transparent shadow-sm rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">{{ __('documents.workflow_archive') }}</button>
                            </form>
                        @endcan

                        @if($version->isArchived())
                            <div class="text-sm text-gray-500 italic text-center p-2 bg-gray-50 rounded">
                                {{ __('documents.workflow_archived_note') }}
                            </div>
                        @endif

                        @if($version->isDraft() && !auth()->user()->can('is-operator'))
                            <div class="text-sm text-gray-500 italic text-center p-2 bg-gray-50 rounded">
                                {{ __('documents.workflow_draft_note') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="ds-card">
                <div class="ds-card-header">
                    <h2 class="text-lg font-semibold ds-text-primary">{{ __('documents.section_metadata') }}</h2>
                </div>
                <div class="ds-card-body space-y-4">
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">{{ __('documents.meta_state') }}</h3>
                        @php
                            $mBadge = match(strtolower($version->documentState->name)) {
                                'draft'     => 'ds-badge-draft',
                                'in review' => 'ds-badge-in-review',
                                'published' => 'ds-badge-published',
                                'archived'  => 'ds-badge-archived',
                                default     => 'ds-badge-draft'
                            };
                        @endphp
                        <div class="mt-1">
                            <span class="ds-badge {{ $mBadge }}">{{ $version->stateLabel() }}</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">{{ __('documents.meta_priority') }}</h3>
                        <p class="ds-text-primary mt-1">{{ __('documents.priorities.' . $document->priority->value) }}</p>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">{{ __('documents.meta_category') }}</h3>
                        <p class="ds-text-primary mt-1">{{ $document->category->label() }}</p>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">{{ __('documents.meta_responsible') }}</h3>
                        <p class="ds-text-primary mt-1">{{ $document->responsibleUser->name }}</p>
                    </div>
                    <hr class="ds-border-ui">
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">{{ __('documents.meta_version_created') }}</h3>
                        <p class="ds-text-primary mt-1">{{ $version->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">{{ __('documents.meta_version_updated') }}</h3>
                        <p class="ds-text-primary mt-1">{{ $version->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
