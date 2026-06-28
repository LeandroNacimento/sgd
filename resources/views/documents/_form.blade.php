<div class="space-y-4">
    <div>
        <label for="title" class="ds-form-label">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $document->currentVersion->title ?? '') }}" class="ds-form-input" required>
        @error('title')
            <span class="ds-form-error">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="description" class="ds-form-label">Description</label>
        <textarea id="description" name="description" rows="4" class="ds-form-input">{{ old('description', $document->currentVersion->description ?? '') }}</textarea>
        @error('description')
            <span class="ds-form-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="category_id" class="ds-form-label">Category</label>
            <select id="category_id" name="category_id" class="ds-form-input" required>
                <option value="">Select a category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $document->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="ds-form-error">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="priority" class="ds-form-label">Priority</label>
            <select id="priority" name="priority" class="ds-form-input" required>
                <option value="">Select a priority</option>
                @foreach($priorities as $priority)
                    <option value="{{ $priority->value }}" {{ old('priority', (isset($document) ? $document->priority->value : '')) == $priority->value ? 'selected' : '' }}>
                        {{ ucfirst($priority->value) }}
                    </option>
                @endforeach
            </select>
            @error('priority')
                <span class="ds-form-error">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
