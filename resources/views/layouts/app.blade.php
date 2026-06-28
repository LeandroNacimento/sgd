<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="text-gray-900 antialiased">
        <div id="app-layout" class="min-h-screen ds-bg-page">
            <x-topbar />
            <x-sidebar />

            <main class="pt-16 ml-0 md:ml-64 p-6 transition-all duration-200">
                @isset($header)
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold ds-text-primary">
                            {{ $header }}
                        </h1>
                    </div>
                @endisset

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
