<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- حماية لمنع خطأ 419 --}}

    <title>@yield('title', config('app.name', 'مركز حملة القرآن'))</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

        @include('layouts.navigation')

        @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow py-3">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">

                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200">
                        {{ $header }}
                    </div>

                    <div>
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'مركز حملة القرآن') }} Logo" class="h-10 w-auto">
                    </div>

                </div>
            </div>
        </header>
        @endisset

        <main class="py-6">
            {{ $slot }}
        </main>
    </div>
</body>

</html>