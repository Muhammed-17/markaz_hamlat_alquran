<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'مركز حملة القرآن') }}</title>

    <!-- خط Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-[Cairo] bg-gray-50">

    <!-- الحاوية الرئيسية -->
    <div class="min-h-screen flex flex-col md:flex-row">


        <!-- شريط علوي للجوال -->
        <header class="md:hidden bg-[#0a5c36] text-white flex justify-between items-center p-4 shadow-md">
            <div class="flex items-center gap-2">
                <x-application-logo class="w-10 h-10 text-white fill-current" />
                <h1 class="font-bold text-lg">مركز حملة القرآن</h1>
            </div>
            <button id="menu-toggle" class="text-3xl focus:outline-none">☰</button>
        </header>

        <!-- الخلفية الشفافة (عند فتح القائمة في الجوال) -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 md:hidden"></div>

        <!-- الشريط الجانبي -->
        <aside id="sidebar"
            class="w-52 bg-[#0a5c36] text-white min-h-screen fixed md:relative top-0 right-0 transform translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-50 p-6 space-y-6">

            <!-- زر إغلاق في الجوال -->
            <div class="flex justify-end md:hidden mb-2">
                <button id="close-menu" class="text-3xl focus:outline-none">×</button>
            </div>

            <!-- الشعار -->
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-white/10 rounded-2xl flex items-center justify-center mb-1">
                    <x-application-logo class="w-20 h-20 text-white fill-current" />
                </div>
                <h1 class="text-lg font-black text-orange-400">مركز حملة القرآن</h1>
                <p class="text-xs text-blue-400 mt-1">بناء - إتقان - إبداع</p>
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

                @if (Auth::user()->hasAnyRole('admin'))
                    <a href="{{ route('teachers.index') }}"
                        class="block px-4 py-2 rounded-lg {{ request()->routeIs('teachers.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                        المعلمين
                    </a>
                @endif

                @if (Auth::user()->hasAnyRole(['admin', 'supervisor']))
                    <a href="{{ route('circles.index') }}"
                        class="block px-4 py-2 rounded-lg {{ request()->routeIs('circles.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                        الحلقات
                    </a>
                @endif

                <a href="{{ route('attendance.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('attendance.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    الحضور والغياب
                </a>

                <a href="{{ route('subscriptions.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('subscriptions.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    اشتراكات الطلاب
                </a>

                @if (Auth::user()->hasRole('admin'))
                    <a href="{{ route('subscription-prices.index') }}"
                        class="block px-4 py-2 rounded-lg {{ request()->routeIs('subscription-prices.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                        إعدادات الاشتراكات
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('profile.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    الملف الشخصي
                </a>

                <div class="opacity-60 cursor-not-allowed px-4 py-2">
                    المستخدمين
                </div>

                <!-- تسجيل الخروج -->
                <div class="pt-6 border-t border-white/10 mt-6">
                    <div class="px-4 py-2 mb-2 text-xs text-orange-200">
                        أهلاً، {{ auth()->user()->name ?? 'مستخدم' }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}"
                        onsubmit="return confirm('هل أنت متأكد من رغبتك في تسجيل الخروج؟')">
                        @csrf
                        <button type="submit"
                            class="w-full text-right px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300 flex items-center gap-2 text-red-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>تسجيل الخروج</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- المحتوى الرئيسي -->
        <main class="flex-1 p-4 md:p-6 md:mr-0 md:ml-0 transition-all duration-300">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="max-w-4xl mx-auto mt-3 mb-2">
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="max-w-4xl mx-auto mt-6">
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-sm">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>

    <!-- سكربت فتح/إغلاق القائمة -->
    <script>
        const toggle = document.getElementById('menu-toggle');
        const closeBtn = document.getElementById('close-menu');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const body = document.body;

        function openMenu() {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            body.classList.add('overflow-hidden');
        }

        function closeMenu() {
            sidebar.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            body.classList.remove('overflow-hidden');
        }

        toggle.addEventListener('click', openMenu);
        closeBtn.addEventListener('click', closeMenu);
        overlay.addEventListener('click', closeMenu);
    </script>

</body>

</html>
