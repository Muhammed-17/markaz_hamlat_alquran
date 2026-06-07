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

    <div class="min-h-screen flex flex-col md:flex-row">

        <!-- شريط علوي للجوال -->
        <header class="md:hidden bg-[#0a5c36] text-white flex justify-between items-center p-4 shadow-md">
            <div class="flex items-center gap-2">
                <x-application-logo class="w-10 h-10 text-white fill-current" />
                <h1 class="font-bold text-lg">مركز حملة القرآن</h1>
            </div>
            <button id="menu-toggle" class="text-3xl focus:outline-none">☰</button>
        </header>

        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 md:hidden"></div>

        <!-- الشريط الجانبي -->
        <aside id="sidebar"
            class="w-52 bg-[#0a5c36] text-white min-h-screen fixed md:relative top-0 right-0 transform translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-50 p-6 space-y-6">

            <div class="flex justify-end md:hidden mb-2">
                <button id="close-menu" class="text-3xl focus:outline-none">×</button>
            </div>

            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-white/10 rounded-2xl flex items-center justify-center mb-1">
                    <x-application-logo class="w-20 h-20 text-white fill-current" />
                </div>
                <h1 class="text-lg font-black text-orange-400">مركز حملة القرآن</h1>
                <p class="text-xs text-blue-400 mt-1">بناء - إتقان - إبداع</p>
            </div>

            <nav class="space-y-2 text-sm">

                @if (Auth::user()->hasRole('guardian'))
                <a href="{{ route('guardian.dashboard') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('guardian.dashboard') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    المنصة العامة
                </a>
                <a href="{{ route('students.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('students.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    الطلاب
                </a>
                <a href="{{ route('guardian.notifications.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('guardian.notifications.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    <div class="flex items-center justify-between">
                        <span>الإشعارات</span>
                        @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                        @if ($unreadCount > 0)
                        <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </div>
                </a>

                @else
                <a href="{{ route('dashboard') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    لوحة التحكم
                </a>

                @can('view students')
                <a href="{{ route('students.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('students.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    الطلاب
                </a>
                @endcan

                @can('view teachers')
                <a href="{{ route('teachers.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('teachers.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    المعلمون
                </a>
                @endcan

                @can('view circles')
                <a href="{{ route('circles.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('circles.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    الحلقات
                </a>
                @endcan

                @can('view attendance')
                <a href="{{ route('attendance.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('attendance.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    الحضور والغياب
                </a>
                @endcan

                @can('view subscriptions')
                <a href="{{ route('subscriptions.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('subscriptions.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    اشتراكات الطلاب
                </a>
                @endcan

                @can('view notifications')
                <a href="{{ route('notifications.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('notifications.*') ? 'bg-[#0d7a48]' : 'hover:bg-[#0d7a48]' }}">
                    <div class="flex items-center justify-between">
                        <span>الإشعارات</span>
                        @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                        @if ($unreadCount > 0)
                        <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </div>
                </a>
                @endcan

                <!-- الإعدادات -->
                @canany(['view settings', 'manage roles', 'view centers', 'view subscription prices'])
                <div x-data="{ open: {{ (request()->routeIs('subscription-prices.*') || request()->routeIs('centers.*') || request()->routeIs('profile.*') || request()->routeIs('admin.settings.*') || request()->routeIs('admin.roles.*')) ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-[#0d7a48] transition-colors focus:outline-none {{ (request()->routeIs('subscription-prices.*') || request()->routeIs('centers.*') || request()->routeIs('profile.*')) ? 'bg-[#0d7a48]' : '' }}">
                        <span>الإعدادات</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-cloak class="pr-3 space-y-1 border-r border-white/10 mr-1">
                        @can('edit profile')
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 rounded-lg text-[13px] {{ request()->routeIs('profile.*') ? 'bg-[#0d7a48] font-bold' : 'hover:bg-[#0d7a48]' }}">
                            الملف الشخصي
                        </a>
                        @endcan

                        @can('view settings')
                        <a href="{{ route('admin.settings.index') }}"
                            class="block px-4 py-2 rounded-lg text-[13px] {{ request()->routeIs('admin.settings.*') ? 'bg-[#0d7a48] font-bold' : 'hover:bg-[#0d7a48]' }}">
                            إعدادات الإشعارات
                        </a>
                        @endcan

                        @can('view centers')
                        <a href="{{ route('centers.index') }}"
                            class="block px-4 py-2 rounded-lg text-[13px] {{ request()->routeIs('centers.*') ? 'bg-[#0d7a48] font-bold' : 'hover:bg-[#0d7a48]' }}">
                            الفروع
                        </a>
                        @endcan

                        @can('view subscription prices')
                        <a href="{{ route('subscription-prices.index') }}"
                            class="block px-4 py-2 rounded-lg text-[13px] {{ request()->routeIs('subscription-prices.*') ? 'bg-[#0d7a48] font-bold' : 'hover:bg-[#0d7a48]' }}">
                            أسعار الاشتراكات
                        </a>
                        @endcan

                        @can('manage roles')
                        <a href="{{ route('admin.roles.index') }}"
                            class="block px-4 py-2 rounded-lg text-[13px] {{ request()->routeIs('admin.roles.*') ? 'bg-[#0d7a48] font-bold' : 'hover:bg-[#0d7a48]' }}">
                            الصلاحيات
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany
                @endif

                <!-- تسجيل الخروج -->
                <div class="pt-6 border-t border-white/10 mt-6">
                    <div class="px-4 py-2 mb-2 text-xs text-orange-200">
                        أهلاً، {{ auth()->user()->name ?? 'مستخدم' }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <button type="button" onclick="confirmLogout()"
                            class="w-full text-right px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300 flex items-center gap-2 text-red-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        <main class="flex-1 p-4 md:p-6 transition-all duration-300">
            {{ $slot }}
        </main>
    </div>

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

    {{-- SweetAlert2 عام لكل الصفحات --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'تسجيل الخروج',
                text: 'هل أنت متأكد من رغبتك في تسجيل الخروج؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'نعم، خروج',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl font-bold',
                    confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
                    cancelButton: 'rounded-xl px-6 py-2.5 text-sm',
                }
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        {
            {
                --✅رسائل عامة لكل الصفحات--
            }
        }
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح',
            text: "{{ session('success') }}",
            confirmButtonColor: '#0a5c36',
            confirmButtonText: 'حسناً',
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-3xl font-bold',
                confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
            }
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: "{{ session('error') }}",
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'حسناً',
            customClass: {
                popup: 'rounded-3xl font-bold',
                confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
            }
        });
        @endif
    </script>

    @stack('scripts')
</body>

</html>
