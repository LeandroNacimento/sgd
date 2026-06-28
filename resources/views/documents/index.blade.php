<x-app-layout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold ds-text-primary">{{ __('documents.title') }}</h1>
        @can('create', App\Models\Document::class)
            <a href="{{ route('documents.create') }}" class="ds-btn ds-btn-primary">
                {{ __('documents.new_document') }}
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded ds-bg-surface border-l-4 border-green-500 ds-text-primary shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="ds-card mb-6">
        <div class="ds-card-body p-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('documents.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label for="search" class="ds-form-label text-xs">{{ __('documents.filter_search') }}</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('documents.filter_search_placeholder') }}" class="ds-form-input ds-form-input-sm w-full">
                </div>

                <div class="w-full md:w-48">
                    <label for="category_id" class="ds-form-label text-xs">{{ __('documents.filter_category') }}</label>
                    <select id="category_id" name="category_id" class="ds-form-input ds-form-input-sm w-full">
                        <option value="">{{ __('documents.filter_all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-48">
                    <label for="document_state_id" class="ds-form-label text-xs">{{ __('documents.filter_state') }}</label>
                    <select id="document_state_id" name="document_state_id" class="ds-form-input ds-form-input-sm w-full">
                        <option value="">{{ __('documents.filter_all_states') }}</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ request('document_state_id') == $state->id ? 'selected' : '' }}>
                                {{ \App\Enums\DocumentStateName::tryFrom($state->name)?->label() ?? $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-48">
                    <label for="priority" class="ds-form-label text-xs">{{ __('documents.filter_priority') }}</label>
                    <select id="priority" name="priority" class="ds-form-input ds-form-input-sm w-full">
                        <option value="">{{ __('documents.filter_all_priorities') }}</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->value }}" {{ request('priority') == $priority->value ? 'selected' : '' }}>
                                {{ __('documents.priorities.' . $priority->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit" class="ds-btn ds-btn-primary whitespace-nowrap">{{ __('documents.filter_button') }}</button>
                    @if(request()->anyFilled(['search', 'category_id', 'document_state_id', 'priority']))
                        <a href="{{ route('documents.index') }}" class="ds-btn ds-btn-secondary whitespace-nowrap">{{ __('documents.filter_clear') }}</a>
                    @endif
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="ds-bg-page border-b ds-border-ui text-sm ds-text-secondary uppercase">
                        <th class="p-4 font-semibold">{{ __('documents.column_code') }}</th>
                        <th class="p-4 font-semibold">{{ __('documents.column_title') }}</th>
                        <th class="p-4 font-semibold">{{ __('documents.column_category') }}</th>
                        <th class="p-4 font-semibold">{{ __('documents.column_state') }}</th>
                        <th class="p-4 font-semibold">{{ __('documents.column_priority') }}</th>
                        <th class="p-4 font-semibold">{{ __('documents.column_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y ds-border-ui text-sm">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-medium ds-text-primary">{{ $doc->code }}</td>
                            <td class="p-4 ds-text-secondary">{{ $doc->currentVersion->title ?? __('documents.no_documents') }} <span class="text-xs text-gray-500 ml-1">({{ $doc->currentVersion->semantic_version ?? 'v1.0' }})</span></td>
                            <td class="p-4 ds-text-secondary">{{ $doc->category->label() }}</td>
                            <td class="p-4">
                                @php
                                    $stateKey = strtolower($doc->currentVersion->documentState->name ?? 'draft');
                                    $badgeClass = match($stateKey) {
                                        'draft'      => 'ds-badge-draft',
                                        'in review'  => 'ds-badge-in-review',
                                        'published'  => 'ds-badge-published',
                                        'archived'   => 'ds-badge-archived',
                                        default      => 'ds-badge-draft'
                                    };
                                @endphp
                                <span class="ds-badge {{ $badgeClass }}">{{ $doc->currentVersion->stateLabel() }}</span>
                            </td>
                            <td class="p-4 ds-text-secondary">{{ __('documents.priorities.' . $doc->priority->value) }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('documents.show', $doc) }}" class="ds-text-brand hover:underline">{{ __('documents.view_button') }}</a>
                                    @can('update', $doc)
                                        <a href="{{ route('documents.edit', $doc) }}" class="ds-text-secondary hover:underline">{{ __('documents.edit_button') }}</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center ds-text-muted">{{ __('documents.no_documents') }}</td>
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
