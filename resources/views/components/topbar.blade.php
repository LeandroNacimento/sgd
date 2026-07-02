<header class="fixed top-0 right-0 left-0 md:left-64 h-16 z-40 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 md:px-8">
    <div class="flex items-center flex-1">
        <button id="sidebar-toggle" class="md:hidden mr-4 p-2 rounded-md text-slate-500 hover:text-slate-900 hover:bg-slate-100 focus:outline-none">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 overflow-hidden whitespace-nowrap">
            @if(isset($breadcrumbs))
                <div class="hidden sm:flex text-sm text-slate-500">
                    {{ $breadcrumbs }}
                </div>
            @endif
            
            @if(isset($title))
                @if(isset($breadcrumbs))
                    <span class="hidden sm:block text-slate-300">/</span>
                @endif
                <h1 class="text-lg font-semibold text-slate-900 truncate">
                    {{ $title }}
                </h1>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-4 ml-4">
        @if(isset($actions))
            <div class="hidden md:flex items-center gap-2">
                {{ $actions }}
            </div>
            <div class="hidden md:block w-px h-6 bg-slate-200 mx-2"></div>
        @endif

        <div class="flex items-center gap-3">
            <!-- Notifications Bell -->
            <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-full transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="absolute inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 border border-white rounded-full top-0 right-0">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>

            <span class="text-sm font-medium text-slate-700 hidden sm:block">
                {{ Auth::user()->name }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ds.button type="submit" variant="secondary" size="sm">
                    {{ __('navigation.logout') }}
                </x-ds.button>
            </form>
        </div>
    </div>
</header>
