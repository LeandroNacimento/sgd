<div class="space-y-6">
    <div>
        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">{{ __('documents.field_title') }}</label>
        <x-ds.input type="text" id="title" name="title" value="{{ old('title', $document->currentVersion->title ?? '') }}" required :error="$errors->has('title')" />
        @error('title')
            <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">{{ __('documents.field_description') }}</label>
        <textarea id="description" name="description" rows="4" class="block w-full rounded-md shadow-sm sm:text-sm ds-transition border-slate-300 focus:border-blue-500 focus:ring-blue-500 {{ $errors->has('description') ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '' }}">{{ old('description', $document->currentVersion->description ?? '') }}</textarea>
        @error('description')
            <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1">{{ __('documents.field_category') }}</label>
            <x-ds.select id="category_id" name="category_id" required :error="$errors->has('category_id')">
                <option value="">{{ __('documents.select_category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $document->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->label() }}
                    </option>
                @endforeach
            </x-ds.select>
            @error('category_id')
                <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="priority" class="block text-sm font-medium text-slate-700 mb-1">{{ __('documents.field_priority') }}</label>
            <x-ds.select id="priority" name="priority" required :error="$errors->has('priority')">
                <option value="">{{ __('documents.select_priority') }}</option>
                @foreach($priorities as $priority)
                    <option value="{{ $priority->value }}" {{ old('priority', (isset($document) ? $document->priority->value : '')) == $priority->value ? 'selected' : '' }}>
                        {{ __('documents.priorities.' . $priority->value) }}
                    </option>
                @endforeach
            </x-ds.select>
            @error('priority')
                <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
