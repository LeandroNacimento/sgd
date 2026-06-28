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
        </div>
        
        <div class="space-y-6">
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
