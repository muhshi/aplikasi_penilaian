<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Reverted to Laravel default styles */
            </style>
        @endif
    </head>
    <body class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-2xl font-bold">Welcome to Laravel</h1>
            <p class="mt-2">This is the original welcome page.</p>
            <a href="{{ url('/') }}" class="mt-4 inline-block text-blue-600 hover:underline underline-offset-4">Go to Landing Page</a>
        </div>
    </body>
</html>
