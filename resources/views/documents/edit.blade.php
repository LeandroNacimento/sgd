<x-app-layout>
    <div class="mb-6">
        <a href="{{ route('documents.index') }}" class="ds-text-secondary hover:underline text-sm mb-2 inline-block">{{ __('documents.back_to_documents') }}</a>
        <h1 class="text-2xl font-bold ds-text-primary">{{ __('documents.edit_document') }}: {{ $document->code }}</h1>
    </div>

    <div class="ds-card max-w-3xl">
        <div class="ds-card-body">
            <form action="{{ route('documents.update', $document) }}" method="POST">
                @csrf
                @method('PUT')

                @include('documents._form')

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('documents.index') }}" class="ds-btn ds-btn-secondary">{{ __('documents.cancel') }}</a>
                    <button type="submit" class="ds-btn ds-btn-primary">{{ __('documents.update_button') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
