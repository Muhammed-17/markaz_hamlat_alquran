<x-layouts.markaz-layout>
    @section('title', 'بيانات المعلم: ' . $teacher->name)

    <div class="max-w-3xl mx-auto space-y-6">

        {{-- ─── Header ─── --}}
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('teachers.index') }}"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-gray-800">{{ $teacher->name }}</h1>

                        {{-- 🌟 شارة الكادر الإداري بجانب الاسم مباشرة إن وجد --}}
                        @if($teacher->is_administrative)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold rounded-lg shadow-sm">
                            👑 كادر إداري
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">بيانات ملف المعلم والكادر التعليمي</p>
                </div>
            </div>

            {{-- أزرار الإجراءات --}}
            <div class="flex items-center gap-2">
                @can('manage teachers')
                {{-- تعديل --}}
                <a href="{{ route('teachers.edit', $teacher) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    تعديل
                </a>

                {{-- تفعيل / تعطيل الحساب من خلال علاقة المستخدم الأساسية --}}
                @if($teacher->user)
                <form method="POST" action="{{ route('users.toggleStatus', $teacher->user) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold rounded-xl transition-colors
                               {{ $teacher->user->status === 'active'
                                   ? 'bg-orange-50 hover:bg-orange-100 text-orange-600'
                                   : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }}">
                        @if($teacher->user->status === 'active')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        تعطيل الحساب
                        @else
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        تفعيل الحساب
                        @endif
                    </button>
                </form>
                @endif

                {{-- حذف الحساب نهائياً --}}
                @if($teacher->circles->isEmpty())
                <form method="POST" action="{{ route('teachers.destroy', $teacher) }}"
                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المعلم نهائياً من النظام؟')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-bold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        حذف
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>

        {{-- ─── بطاقة البيانات الأساسية ─── --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-50">
                <div class="p-2.5 bg-emerald-50 text-[#0a5c36] rounded-xl text-lg">💼</div>
                <div>
                    <h2 class="font-black text-gray-800">البيانات المهنية والشخصية</h2>
                    <p class="text-xs text-gray-400">معلومات المعلم المعتمدة داخل المركز</p>
                </div>
                {{-- badge الحالة عبر موديل الـ User المرتبط --}}
                <div class="mr-auto">
                    @if($teacher->user && $teacher->user->status === 'active')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-bold rounded-xl">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                        نشط
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 text-orange-600 border border-orange-100 text-xs font-bold rounded-xl">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400 inline-block"></span>
                        غير نشط
                    </span>
                    @endif
                </div>
            </div>

            {{-- مؤشرات التفاعل السريع للـ المعلم وحالة الاتصال --}}
            <div class="grid grid-cols-2 gap-4 p-3.5 bg-gray-50 rounded-2xl">
                <div class="text-right">
                    <p class="text-xs text-gray-400">الدور والوظيفة</p>
                    <span class="text-sm font-black text-gray-700 mt-0.5 inline-block">
                        {{ $teacher->user?->roles->first()?->display_name ?? 'معلم / محفظ' }}
                    </span>
                </div>

                {{-- مؤشر حالة الاتصال الفورية --}}
                <div class="text-left flex flex-col justify-center items-end">
                    <span class="text-sm font-bold text-gray-600 flex flex-col items-end">
                        <span class="text-[10px] text-gray-400 font-normal">آخر ظهور:</span>
                        <span class="text-xs font-medium text-gray-500 mt-0.5">
                            {{ $teacher->user?->last_seen_at ? $teacher->user->last_seen_at->locale('ar')->diffForHumans() : 'لم يسجل دخول قريب' }}
                        </span>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- الاسم --}}
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400">الاسم الكامل</p>
                    <p class="text-sm font-bold text-gray-800">{{ $teacher->name }}</p>
                </div>

                {{-- الموبايل --}}
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400">رقم الهاتف</p>
                    <p class="text-sm font-medium text-gray-700 font-mono">
                        {{ $teacher->mobile ?? '—' }}
                    </p>
                </div>

                {{-- الإيميل --}}
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400">البريد الإلكتروني للحساب</p>
                    <p class="text-sm font-medium text-gray-700 font-mono">
                        {{ $teacher->user?->email ?? '—' }}
                    </p>
                </div>

                {{-- الفرع التابع له --}}
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400">الفرع الرئيسي</p>
                    <p class="text-sm font-medium text-gray-700">
                        {{ $teacher->center?->name ?? '—' }}
                    </p>
                </div>

                {{-- نوع الكادر --}}
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400">هل له صفة إدارية</p>
                    <p class="text-sm font-bold {{ $teacher->is_administrative ? 'text-amber-700' : 'text-blue-700' }}">
                        {{ $teacher->is_administrative ? 'له صفة إدارية' : 'ليس له' }}
                    </p>
                </div>

                {{-- تاريخ التعيين / الإنضمام --}}
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400">تاريخ الإنضمام للمركز</p>
                    <p class="text-sm font-medium text-gray-700">
                        {{ $teacher->created_at ? $teacher->created_at->format('Y/m/d') : '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ─── بطاقة الحلقات المسندة والمسؤول عنها ─── --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-50">
                <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl text-lg">🕌</div>
                <div>
                    <h2 class="font-black text-gray-800">الحلقات التعليمية المسندة</h2>
                    <p class="text-xs text-gray-400">
                        @if($teacher->circles->isEmpty())
                        لا توجد حلقات مرتبطة بهذا المعلم حالياً
                        @else
                        يشرف ويقوم على إدارة {{ $teacher->circles->count() }} حلقة تعليمية
                        @endif
                    </p>
                </div>
            </div>

            @if($teacher->circles->isEmpty())
            <div class="py-8 text-center">
                <div class="text-4xl mb-2">🏝️</div>
                <p class="text-gray-400 text-sm font-medium">لم يتم ربط المعلم بأي حلقة حتى الآن</p>
            </div>
            @else
            <div class="space-y-2">
                @foreach($teacher->circles as $circle)
                <div class="flex items-center justify-between p-3.5 bg-gray-50 hover:bg-gray-100 rounded-2xl transition-colors">
                    <div class="flex items-center gap-3">
                        @if($circle->pivot && $circle->pivot->role)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-lg 
                            {{ $circle->pivot->role === 'main' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 
                              ($circle->pivot->role === 'assistant' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-gray-100 text-gray-600') }}">
                            {{ $circle->pivot->role === 'main' ? 'معلم رئيسي' : ($circle->pivot->role === 'assistant' ? 'مساعد' : 'مشرف') }}
                        </span>
                        @endif
                        <span class="text-sm font-bold text-gray-800">{{ $circle->name }}</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4 text-xs text-gray-400 font-medium">
                            {{-- عرض مستوى الحلقة --}}
                            <span>
                                المستوى: <strong class="text-gray-700">{{ $circle->level_arabic }}</strong>
                            </span>

                            {{-- فاصل بصري يظهر في الشاشات الكبيرة فقط --}}
                            <span class="hidden sm:inline text-gray-300">•</span>

                            {{-- عرض نوع الحلقة (جماعية / فردية) مع تمييز لوني خفيف --}}
                            <span>
                                النوع:
                                <strong class="{{ $circle->type === 'group' ? 'text-blue-600 bg-blue-50' : 'text-purple-600 bg-purple-50' }} px-2 py-0.5 rounded-md text-[11px] font-bold">
                                    {{ $circle->type_arabic }}
                                </strong>
                            </span>
                        </div>

                        <!-- @can('view circles')
                        <a href="{{ route('circles.show', $circle->id) }}"
                            class="p-1.5 text-gray-400 hover:text-[#0a5c36] hover:bg-emerald-50 rounded-lg transition-colors"
                            title="عرض تفاصيل الحلقة">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        @endcan -->
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</x-layouts.markaz-layout>