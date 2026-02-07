<x-layouts.markaz-layout>
    <div class="space-y-6">
        <!-- Header Card -->
        <div
            class="bg-[#0a4d31] rounded-3xl p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">إعدادات الملف الشخصي</h1>
                <p class="text-emerald-100/80 text-sm font-medium">إدارة معلومات حسابك وكلمة المرور</p>
            </div>

            <div class="flex items-center gap-4 z-10">
                <div
                    class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/20 shadow-inner">
                    <svg class="w-8 h-8 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>

            <!-- Decorative background element -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Sections -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Profile Info Section -->
                <div
                    class="p-6 sm:p-10 bg-white shadow-sm border border-gray-100 rounded-3xl transition-all hover:border-emerald-100">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Password Section -->
                <div
                    class="p-6 sm:p-10 bg-white shadow-sm border border-gray-100 rounded-3xl transition-all hover:border-emerald-100">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account Section -->
                <div
                    class="p-6 sm:p-10 bg-red-50/30 shadow-sm border border-red-100 rounded-3xl transition-all hover:border-red-200 mt-12">
                    <div class="max-w-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 sticky top-6">
                    <h3 class="text-[#0a5c36] font-bold mb-6 text-right pb-3 border-b border-gray-50">نصائح الأمان</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-right">
                            <div
                                class="shrink-0 w-6 h-6 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-gray-600 text-sm">استخدم كلمة مرور قوية تحتوي على أحرف وأرقام ورموز.</p>
                        </li>
                        <li class="flex items-start gap-3 text-right">
                            <div
                                class="shrink-0 w-6 h-6 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-gray-600 text-sm">لا تشارك بيانات الدخول الخاصة بك مع أي شخص آخر.</p>
                        </li>
                        <li class="flex items-start gap-3 text-right">
                            <div
                                class="shrink-0 w-6 h-6 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-gray-600 text-sm">تأكد من صحة البريد الإلكتروني لاستلام التنبيهات الهامة.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.markaz-layout>
