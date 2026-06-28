<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out ds-bg-surface border-r ds-border-ui">
    <div class="h-16 flex items-center px-6 border-b ds-border-ui">
        <a href="/" class="text-xl font-bold ds-text-primary">
            {{ config('app.name', 'Laravel') }}
        </a>
    </div>

    <div class="py-4 px-3">
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" class="ds-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Dashboard
            </a>
        </nav>
    </div>
</aside>
