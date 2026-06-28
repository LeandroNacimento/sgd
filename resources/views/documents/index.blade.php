<x-app-layout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold ds-text-primary">Documents</h1>
        @can('create', App\Models\Document::class)
            <a href="{{ route('documents.create') }}" class="ds-btn ds-btn-primary">
                New Document
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded ds-bg-surface border-l-4 border-green-500 ds-text-primary shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="ds-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="ds-bg-page border-b ds-border-ui text-sm ds-text-secondary uppercase">
                        <th class="p-4 font-semibold">Code</th>
                        <th class="p-4 font-semibold">Title</th>
                        <th class="p-4 font-semibold">Category</th>
                        <th class="p-4 font-semibold">State</th>
                        <th class="p-4 font-semibold">Priority</th>
                        <th class="p-4 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y ds-border-ui text-sm">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-medium ds-text-primary">{{ $doc->code }}</td>
                            <td class="p-4 ds-text-secondary">{{ $doc->title }}</td>
                            <td class="p-4 ds-text-secondary">{{ $doc->category->name }}</td>
                            <td class="p-4">
                                @php
                                    $badgeClass = match(strtolower($doc->documentState->name)) {
                                        'draft' => 'ds-badge-draft',
                                        'in review' => 'ds-badge-in-review',
                                        'published' => 'ds-badge-published',
                                        'archived' => 'ds-badge-archived',
                                        default => 'ds-badge-draft'
                                    };
                                @endphp
                                <span class="ds-badge {{ $badgeClass }}">{{ $doc->documentState->name }}</span>
                            </td>
                            <td class="p-4 ds-text-secondary">{{ ucfirst($doc->priority->value) }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('documents.show', $doc) }}" class="ds-text-brand hover:underline">View</a>
                                    @can('update', $doc)
                                        <a href="{{ route('documents.edit', $doc) }}" class="ds-text-secondary hover:underline">Edit</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center ds-text-muted">No documents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
            <div class="p-4 border-t ds-border-ui">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
