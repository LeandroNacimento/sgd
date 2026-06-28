<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out bg-white border-r border-slate-200">
    <div class="h-16 flex items-center px-6 border-b border-slate-200">
        <a href="/" class="text-xl font-bold text-slate-900 tracking-tight">
            {{ config('app.name', 'Laravel') }}
        </a>
    </div>

    <div class="py-6 px-4">
        <nav class="space-y-1">
            @php
                $isDashboard = request()->routeIs('dashboard');
                $isDocuments = request()->routeIs('documents.*');
            @endphp
            <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md ds-transition {{ $isDashboard ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="flex-shrink-0 w-5 h-5 {{ $isDashboard ? 'text-blue-700' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                {{ __('navigation.dashboard') }}
            </a>
            <a href="{{ route('documents.index') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md ds-transition {{ $isDocuments ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="flex-shrink-0 w-5 h-5 {{ $isDocuments ? 'text-blue-700' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('navigation.documents') }}
            </a>
        </nav>
    </div>
</aside>
