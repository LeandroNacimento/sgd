<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('documents.index') }}" class="ds-text-secondary hover:underline text-sm mb-2 inline-block">&larr; Back to Documents</a>
            <h1 class="text-2xl font-bold ds-text-primary">{{ $document->code }}</h1>
        </div>
        <div class="flex gap-2">
            @can('update', $document)
                <a href="{{ route('documents.edit', $document) }}" class="ds-btn ds-btn-secondary">Edit</a>
            @endcan
            @can('delete', $document)
                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this document?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ds-btn ds-btn-danger">Delete</button>
                </form>
            @endcan
        </div>
    </div>

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
                    <h2 class="text-lg font-semibold ds-text-primary">Details</h2>
                </div>
                <div class="ds-card-body space-y-4">
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">Title</h3>
                        <p class="ds-text-primary mt-1">{{ $document->title }}</p>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">Description</h3>
                        <p class="ds-text-primary mt-1 whitespace-pre-wrap">{{ $document->description ?: 'No description provided.' }}</p>
                    </div>
                </div>
            </div>

            <div class="ds-card">
                <div class="ds-card-header">
                    <h2 class="text-lg font-semibold ds-text-primary">Attachments</h2>
                </div>
                <div class="ds-card-body space-y-4">
                    @php
                        $attachments = $document->getMedia('attachments');
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
                                        <a href="{{ route('documents.attachments.download', [$document, $attachment]) }}" class="ds-btn ds-btn-secondary py-1 px-2 text-xs">Download</a>
                                        @can('update', $document)
                                            <form action="{{ route('documents.attachments.destroy', [$document, $attachment]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this attachment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ds-btn ds-btn-danger py-1 px-2 text-xs">Delete</button>
                                            </form>
                                        @endcan
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 italic">No attachments found.</p>
                    @endif

                    @can('update', $document)
                        @if($attachments->count() < 5)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <form action="{{ route('documents.attachments.store', $document) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-4">
                                    @csrf
                                    <input type="file" name="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" required>
                                    <button type="submit" class="ds-btn ds-btn-primary whitespace-nowrap">Upload File</button>
                                </form>
                                <p class="text-xs text-gray-500 mt-2">Allowed types: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)</p>
                            </div>
                        @else
                            <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-yellow-600">
                                Maximum of 5 attachments reached.
                            </div>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
            
            @if(auth()->user()->can('is-admin') && isset($activities) && $activities->count() > 0)
                <div class="ds-card mt-6">
                    <div class="ds-card-header">
                        <h2 class="text-lg font-semibold ds-text-primary">Audit Trail</h2>
                    </div>
                    <div class="ds-card-body">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($activities as $index => $activity)
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
                                                            <span class="font-medium text-gray-900">{{ $activity->causer->name ?? 'System' }}</span>
                                                            {{ $activity->description }}
                                                        </p>
                                                        @if($activity->event === 'document.updated' && $activity->properties->count() > 0)
                                                            <div class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded border border-gray-100">
                                                                <span class="font-medium">Changes:</span>
                                                                <ul class="list-disc list-inside mt-1">
                                                                    @foreach($activity->properties as $key => $value)
                                                                        <li>{{ ucfirst($key) }} updated</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                        @if($activity->event === 'workflow.transition')
                                                            <div class="mt-2 text-xs text-blue-700 bg-blue-50 p-2 rounded border border-blue-100 font-medium">
                                                                Transition: {{ $activity->properties['from_state'] ?? 'Unknown' }} &rarr; {{ $activity->properties['to_state'] ?? 'Unknown' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                        <time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->format('M j, Y H:i') }}</time>
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
            <div class="ds-card">
                <div class="ds-card-header">
                    <h2 class="text-lg font-semibold ds-text-primary">Workflow Actions</h2>
                </div>
                <div class="ds-card-body space-y-3">
                    @can('submitForReview', $document)
                        <form action="{{ route('documents.workflow.submitForReview', $document) }}" method="POST" onsubmit="return confirm('Submit this document for review?');">
                            @csrf
                            <button type="submit" class="ds-btn ds-btn-primary w-full text-center justify-center">Submit for Review</button>
                        </form>
                    @endcan

                    @can('publish', $document)
                        <form action="{{ route('documents.workflow.publish', $document) }}" method="POST" onsubmit="return confirm('Publish this document?');">
                            @csrf
                            <button type="submit" class="ds-btn bg-green-600 hover:bg-green-700 text-white w-full text-center justify-center border border-transparent shadow-sm rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">Publish Document</button>
                        </form>
                    @endcan

                    @can('reject', $document)
                        <form action="{{ route('documents.workflow.reject', $document) }}" method="POST" onsubmit="return confirm('Reject and return to draft?');">
                            @csrf
                            <button type="submit" class="ds-btn ds-btn-danger w-full text-center justify-center">Reject to Draft</button>
                        </form>
                    @endcan

                    @can('archive', $document)
                        <form action="{{ route('documents.workflow.archive', $document) }}" method="POST" onsubmit="return confirm('Archive this document? It cannot be modified afterwards.');">
                            @csrf
                            <button type="submit" class="ds-btn bg-gray-600 hover:bg-gray-700 text-white w-full text-center justify-center border border-transparent shadow-sm rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">Archive Document</button>
                        </form>
                    @endcan

                    @if($document->isArchived())
                        <div class="text-sm text-gray-500 italic text-center p-2 bg-gray-50 rounded">
                            This document is archived and read-only.
                        </div>
                    @endif
                    
                    @if($document->isDraft() && !auth()->user()->can('is-operator'))
                        <div class="text-sm text-gray-500 italic text-center p-2 bg-gray-50 rounded">
                            Draft documents can only be submitted by operators.
                        </div>
                    @endif
                </div>
            </div>
            <div class="ds-card">
                <div class="ds-card-header">
                    <h2 class="text-lg font-semibold ds-text-primary">Metadata</h2>
                </div>
                <div class="ds-card-body space-y-4">
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">State</h3>
                        @php
                            $badgeClass = match(strtolower($document->documentState->name)) {
                                'draft' => 'ds-badge-draft',
                                'in review' => 'ds-badge-in-review',
                                'published' => 'ds-badge-published',
                                'archived' => 'ds-badge-archived',
                                default => 'ds-badge-draft'
                            };
                        @endphp
                        <div class="mt-1">
                            <span class="ds-badge {{ $badgeClass }}">{{ $document->documentState->name }}</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">Priority</h3>
                        <p class="ds-text-primary mt-1">{{ ucfirst($document->priority->value) }}</p>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">Category</h3>
                        <p class="ds-text-primary mt-1">{{ $document->category->name }}</p>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">Responsible User</h3>
                        <p class="ds-text-primary mt-1">{{ $document->responsibleUser->name }}</p>
                    </div>
                    <hr class="ds-border-ui">
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">Created At</h3>
                        <p class="ds-text-primary mt-1">{{ $document->created_at->format('M j, Y H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="ds-text-secondary text-sm font-medium">Last Updated</h3>
                        <p class="ds-text-primary mt-1">{{ $document->updated_at->format('M j, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
