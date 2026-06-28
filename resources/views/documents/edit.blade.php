<x-app-layout>
    <x-slot name="title">{{ __('documents.edit_document') }}: {{ $document->code }}</x-slot>
    <x-slot name="breadcrumbs">
        <a href="{{ route('documents.index') }}" class="hover:text-slate-900 ds-transition">{{ __('documents.back_to_documents') }}</a>
    </x-slot>

    <div class="max-w-3xl">
        <x-ds.card>
            <form action="{{ route('documents.update', $document) }}" method="POST">
                @csrf
                @method('PUT')

                @include('documents._form')

                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <x-ds.button href="{{ route('documents.index') }}" variant="secondary">{{ __('documents.cancel') }}</x-ds.button>
                    <x-ds.button type="submit" variant="primary">{{ __('documents.update_button') }}</x-ds.button>
                </div>
            </form>
        </x-ds.card>
    </div>
</x-app-layout>
