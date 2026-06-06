<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'مركز حملة القرآن') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-[Cairo] bg-gray-50">

<div 
    x-data="{
        collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggle() {
            this.collapsed = !this.collapsed
            localStorage.setItem('sidebarCollapsed', this.collapsed)
        }
    }" 
    class="min-h-screen flex">

    <!-- Sidebar -->
    <aside :class="collapsed ? 'w-20' : 'w-64'"
           class="bg-[#0a5c36] text-white min-h-screen fixed right-0 top-0 p-4 transition-all duration-300 flex flex-col">

        <!-- زر الطي -->
        <button @click="toggle()"
                class="mb-6 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg p-2 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform"
                 :class="collapsed ? 'rotate-180' : ''"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 19l-7-7 7-7M20 19l-7-7 7-7"/>
            </svg>
        </button>

        <!-- الشعار -->
        <div class="flex flex-col items-center mb-8" x-show="!collapsed" x-transition>
            <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-3">
                <x-application-logo class="w-10 h-10 text-white fill-current"/>
            </div>
            <h1 class="text-lg font-black leading-tight text-center">مركز حملة القرآن</h1>
        </div>

        <!-- القائمة -->
        <nav class="space-y-2 text-sm flex-1">

            @role('admin')
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('dashboard') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span x-show="!collapsed" x-transition>لوحة التحكم</span>
            </a>
            @endrole

            <a href="{{ route('students.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('students.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z"/>
                </svg>
                <span x-show="!collapsed" x-transition>الطلاب</span>
            </a>

            <a href="{{ route('circles.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('circles.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20H7m10 0v-2a4 4 0 00-8 0v2"/>
                </svg>
                <span x-show="!collapsed" x-transition>الحلقات</span>
            </a>

            <a href="{{ route('attendance.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('attendance.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5h6M9 9h6m-6 4h6m2 4H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2z"/>
                </svg>
                <span x-show="!collapsed" x-transition>الحضور والغياب</span>
            </a>

        </nav>
    </aside>

    <!-- المحتوى -->
    <main :class="collapsed ? 'mr-20' : 'mr-64'"
          class="flex-1 p-6 transition-all duration-300">
        {{ $slot }}
    </main>

</div>

</body>
</html>
