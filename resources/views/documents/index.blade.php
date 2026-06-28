<x-app-layout>
    <x-slot name="title">{{ __('documents.title') }}</x-slot>
    <x-slot name="actions">
        @can('create', App\Models\Document::class)
            <x-ds.button href="{{ route('documents.create') }}" variant="primary" size="sm">
                {{ __('documents.new_document') }}
            </x-ds.button>
        @endcan
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200 text-green-800 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <x-ds.card noPadding="true" class="mb-6">
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('documents.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label for="search" class="block text-sm font-medium text-slate-700 mb-1">{{ __('documents.filter_search') }}</label>
                    <x-ds.input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('documents.filter_search_placeholder') }}" />
                </div>

                <div class="w-full md:w-48">
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1">{{ __('documents.filter_category') }}</label>
                    <x-ds.select id="category_id" name="category_id">
                        <option value="">{{ __('documents.filter_all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->label() }}
                            </option>
                        @endforeach
                    </x-ds.select>
                </div>

                <div class="w-full md:w-48">
                    <label for="document_state_id" class="block text-sm font-medium text-slate-700 mb-1">{{ __('documents.filter_state') }}</label>
                    <x-ds.select id="document_state_id" name="document_state_id">
                        <option value="">{{ __('documents.filter_all_states') }}</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ request('document_state_id') == $state->id ? 'selected' : '' }}>
                                {{ \App\Enums\DocumentStateName::tryFrom($state->name)?->label() ?? $state->name }}
                            </option>
                        @endforeach
                    </x-ds.select>
                </div>

                <div class="w-full md:w-48">
                    <label for="priority" class="block text-sm font-medium text-slate-700 mb-1">{{ __('documents.filter_priority') }}</label>
                    <x-ds.select id="priority" name="priority">
                        <option value="">{{ __('documents.filter_all_priorities') }}</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->value }}" {{ request('priority') == $priority->value ? 'selected' : '' }}>
                                {{ __('documents.priorities.' . $priority->value) }}
                            </option>
                        @endforeach
                    </x-ds.select>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <x-ds.button type="submit" variant="primary">{{ __('documents.filter_button') }}</x-ds.button>
                    @if(request()->anyFilled(['search', 'category_id', 'document_state_id', 'priority']))
                        <x-ds.button href="{{ route('documents.index') }}" variant="secondary">{{ __('documents.filter_clear') }}</x-ds.button>
                    @endif
                </div>
            </form>
        </div>
        
        <x-ds.table :headers="[
            __('documents.column_code'),
            __('documents.column_title'),
            __('documents.column_category'),
            __('documents.column_state'),
            __('documents.column_priority'),
            __('documents.column_actions')
        ]">
            @forelse($documents as $doc)
                <tr class="hover:bg-slate-50 ds-transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $doc->code }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $doc->currentVersion->title ?? __('documents.no_documents') }}
                        <span class="text-xs text-slate-400 ml-1">({{ $doc->currentVersion->semantic_version ?? 'v1.0' }})</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $doc->category->label() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-ds.status-badge :state="$doc->currentVersion->documentState" :label="$doc->currentVersion->stateLabel()" />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ __('documents.priorities.' . $doc->priority->value) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('documents.show', $doc) }}" class="text-blue-600 hover:text-blue-900">{{ __('documents.view_button') }}</a>
                            @can('update', $doc)
                                <a href="{{ route('documents.edit', $doc) }}" class="text-slate-500 hover:text-slate-700">{{ __('documents.edit_button') }}</a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12">
                        <x-ds.empty-state title="{{ __('documents.no_documents') }}" />
                    </td>
                </tr>
            @endforelse
        </x-ds.table>
        
        @if($documents->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $documents->links() }}
            </div>
        @endif
    </x-ds.card>
</x-app-layout>
