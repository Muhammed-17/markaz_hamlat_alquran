<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'مركز حملة القرآن') }}</title>

    <!-- Font Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-[Cairo] bg-gray-50">
<div class="min-h-screen flex">

    <!-- Sidebar ثابت -->
    <aside class="w-64 bg-[#0a5c36] text-white min-h-screen fixed right-0 top-0 p-6 space-y-6">

        <!-- الشعار -->
        <div class="text-center">
            <div class="w-20 h-20 mx-auto bg-white/10 rounded-2xl flex items-center justify-center mb-1">
                <x-application-logo class="w-20 h-20 text-white fill-current"/>
            </div>
            <h1 class="text-1xl font-black text-orange-400">مركز</h1>
            <h1 class="text-1xl font-black text-orange-400">حملة القرآن</h1>
            <p class="text-xs text-blue-500 mt-1">بناء - إتقان - إبداع</p>
        </div>

        <!-- القائمة -->
        <nav class="space-y-2 text-sm">

            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                لوحة التحكم
            </a>

            <a href="{{ route('students.index') }}"
               class="block px-4 py-2 rounded-lg {{ request()->routeIs('students.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                الطلاب
            </a>

            <a href="{{ route('circles.index') }}"
               class="block px-4 py-2 rounded-lg {{ request()->routeIs('circles.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                الحلقات
            </a>

            <a href="{{ route('attendance.index') }}"
               class="block px-4 py-2 rounded-lg {{ request()->routeIs('attendance.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                الحضور والغياب
            </a>

            <div class="opacity-60 cursor-not-allowed px-4 py-2">
                اشتراكات الطلاب
            </div>

            <div class="opacity-60 cursor-not-allowed px-4 py-2">
                رواتب المعلمين
            </div>

            <div class="opacity-60 cursor-not-allowed px-4 py-2">
                التقارير
            </div>

            <div class="opacity-60 cursor-not-allowed px-4 py-2">
                المستخدمين
            </div>

        </nav>
    </aside>


    <!-- المحتوى الرئيسي -->
    <main class="flex-1 mr-64 p-6">
        {{ $slot }}
    </main>

</div>
</body>
</html>
