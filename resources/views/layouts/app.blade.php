<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('img/nexusdocs-favicon.svg') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-slate-900 selection:bg-blue-500 selection:text-white">
        <div id="app-layout" class="min-h-screen">
            <x-topbar>
                @if(isset($title))
                    <x-slot name="title">{{ $title }}</x-slot>
                @endif
                @if(isset($breadcrumbs))
                    <x-slot name="breadcrumbs">{{ $breadcrumbs }}</x-slot>
                @endif
                @if(isset($actions))
                    <x-slot name="actions">{{ $actions }}</x-slot>
                @endif
            </x-topbar>
            
            <x-sidebar />

            <!-- Main Content Container with SaaS Standard Spacing -->
            <main class="pt-16 md:pl-64 transition-all duration-200 min-h-screen">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <script>
            // Sidebar Toggle Logic
            document.addEventListener('DOMContentLoaded', () => {
                const toggle = document.getElementById('sidebar-toggle');
                const sidebar = document.getElementById('sidebar');
                
                if(toggle && sidebar) {
                    toggle.addEventListener('click', () => {
                        sidebar.classList.toggle('-translate-x-full');
                    });
                }
            });
        </script>
    </body>
</html>
