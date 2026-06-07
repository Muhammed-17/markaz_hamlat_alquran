<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@isset($header)
<header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex items-center justify-between">

        <div class="text-xl font-bold text-gray-800 dark:text-gray-200">
            {{ $header }}
        </div>

        <div>
            <img src="{{ asset('assets/images/logo.svg') }}" alt="{{ config('app.name') }} Logo" class="h-10 w-auto">
        </div>

    </div>
</header>
@endisset

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Navigation Bar - Here we integrate the logo using primary color -->
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow py-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Logo Placement Placeholder -->
                <div class="flex items-center justify-between">
                    {{-- Assuming 'assets/images/logo.svg' is the correct path --}}
                    <img src="{{ asset('assets/images/logo.svg') }}" alt="{{ config('app.name', 'Laravel') }} Logo" class="h-10 w-auto">
                    <!-- Rest of header content goes here if needed -->
                </div>
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>