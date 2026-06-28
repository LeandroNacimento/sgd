<header class="fixed top-0 inset-x-0 h-16 z-40 ds-bg-surface border-b ds-border-ui flex items-center justify-between px-4 sm:px-6 md:px-8">
    <div class="flex items-center">
        <button id="sidebar-toggle" class="md:hidden mr-4 p-2 rounded-md ds-text-secondary hover:ds-text-primary hover:ds-bg-page focus:outline-none">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div class="flex items-center gap-4">
        <span class="text-sm font-medium ds-text-primary">
            {{ Auth::user()->name }}
        </span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="ds-btn ds-btn-secondary ds-btn-sm">
                {{ __('navigation.logout') }}
            </button>
        </form>
    </div>
</header>
