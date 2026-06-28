<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="ds-card max-w-2xl">
        <div class="ds-card-header">
            <h2 class="text-lg font-semibold ds-text-primary">
                Welcome to {{ config('app.name', 'SGD') }}
            </h2>
        </div>
        <div class="ds-card-body ds-text-secondary">
            <p>
                You're logged in as <strong>{{ Auth::user()->name }}</strong>. Use the sidebar to navigate through the application.
            </p>
        </div>
    </div>
</x-app-layout>
