<x-layouts.markaz-layout>
    @php
    $egyptNow = now()->setTimezone('Africa/Cairo');
    $isNewSchoolYear = ($egyptNow->month >= 7 && $egyptNow->month <= 9);

        $gradeMap=[ 'الأول'=> 'الثاني',
        'الثاني' => 'الثالث',
        'الثالث' => 'الرابع',
        'الرابع' => 'الخامس',
        'الخامس' => 'السادس',
        'السادس' => 'لا يوجد',
        'لا يوجد' => 'لا يوجد',
        'دراسات عليا' => 'دراسات عليا',
        ];

        $savedGrade = $student->school_grade ?? '';
        $currentGrade = ($isNewSchoolYear && isset($gradeMap[$savedGrade])) ? $gradeMap[$savedGrade] : $savedGrade;
        $gradeChanged = $isNewSchoolYear && isset($gradeMap[$savedGrade]) && $gradeMap[$savedGrade] !== $savedGrade;

        $levelLabels = [
        'construction' => ['label' => 'مستوى البناء', 'icon' => '🌱', 'color' => 'emerald'],
        'mastery' => ['label' => 'مستوى الإتقان', 'icon' => '⭐', 'color' => 'amber'],
        'creativity' => ['label' => 'مستوى الإبداع', 'icon' => '🏆', 'color' => 'indigo'],
        ];
        $levelInfo = $levelLabels[$student->center_entry_level] ?? ['label' => '—', 'icon' => '📖', 'color' => 'gray'];

        // ✅ fix #3 — نستخدم المتغيرات من الـ controller مباشرة بدل إعادة الحساب
        // $paidMonthsCount, $unpaidMonthsCount, $totalPaidAmount, $suspendedPastDebt
        // كلها جاية من الـ controller

        // ✅ fix #4 — unpaidMonthsCount من feeTimeline
        $unpaidMonthsCount = collect($feeTimeline)->where('is_paid', false)->count();

        $hobbies = $student->hobbies ?? [];
        if (is_string($hobbies)) { $hobbies = json_decode($hobbies, true) ?? []; }

        $readingLabels = [
        'مبتدئ' => 'مبتدئ (لا يقرأ)',
        'مقبول' => 'مقبول (يقرأ ببطء)',
        'متمكن' => 'متمكن (بدون أحكام)',
        'متقن' => 'متقن (توجد أحكام)',
        ];

        $whatsappNumber = $student->whatsapp_number ? preg_replace('/[^0-9]/', '', $student->whatsapp_number) : null;
        $phoneNumber = $student->second_phone ?? null;
        @endphp
        <div x-data="{ activeTab: '{{ auth()->user()->hasRole('guardian') ? 'personal' : 'overview' }}' }" class="space-y-8 animate-in fade-in duration-500">

            {{-- Breadcrumb --}}
            <nav class="flex items-center text-sm font-medium text-gray-500 gap-2 mb-4">
                <a href="{{ route('dashboard') }}" class="hover:text-emerald-600 transition">الرئيسية</a>
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('students.index') }}" class="hover:text-emerald-600 transition">الطلاب</a>
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900">{{ $student->name }}</span>
            </nav>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- بطاقة الرأس                                            --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div class="bg-white rounded-[2.5rem] p-6 lg:p-10 shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-60"></div>

                <div class="relative flex flex-col lg:flex-row items-center lg:items-start gap-8">

                    {{-- الصورة --}}
                    <div class="relative">
                        <div class="w-32 h-32 lg:w-40 lg:h-40 rounded-3xl bg-emerald-100 flex items-center justify-center overflow-hidden border-4 border-white shadow-xl">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background={{ $student->gender === 'ذكر' ? '10b981' : 'f43f5e' }}&color=fff&size=256"
                                alt="Avatar" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute -bottom-2 -left-2 bg-emerald-500 text-white p-1.5 rounded-xl shadow-lg border-2 border-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>

                    {{-- المعلومات الرئيسية --}}
                    <div class="flex-1 text-center lg:text-right">
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 mb-3">
                            <h1 class="text-3xl lg:text-4xl font-black text-gray-900">{{ $student->name }}</h1>

                            @if($student->student_code)
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold"># {{ $student->student_code }}</span>
                            @endif

                            {{-- ✅ fix #1 — قيم عربية --}}
                            @if($student->status === 'مقيد')
                            <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-bold flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> مقيد
                            </span>
                            @elseif($student->status === 'متوقف')
                            <span class="px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-bold flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span> موقوف
                            </span>
                            @elseif($student->status === 'مسافر')
                            <span class="px-4 py-1.5 bg-cyan-100 text-cyan-700 rounded-full text-sm font-bold flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-cyan-500 rounded-full animate-pulse"></span> مسافر
                            </span>
                            @endif

                            {{-- ✅ fix #6 — قيم عربية --}}
                            @if(isset($student->decision))
                            @if($student->decision === 'مقبول')
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-100">✓ مقبول</span>
                            @elseif($student->decision === 'مرفوض')
                            <span class="px-3 py-1 bg-red-50 text-red-700 rounded-full text-xs font-bold border border-red-100">✗ مرفوض</span>
                            @else
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-bold border border-amber-100">⏳ تحت الاختبار</span>
                            @endif
                            @endif

                            {{-- ✅ fix #4 — يستخدم unpaidMonthsCount المحسوب من feeTimeline --}}
                            @if($student->status !== 'متوقف' && $unpaidMonthsCount > 0)
                            <span class="px-4 py-1.5 bg-rose-100 text-rose-700 rounded-full text-sm font-bold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                متأخر مالياً
                            </span>
                            @endif
                        </div>

                        {{-- معلومات سريعة --}}
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-y-3 gap-x-6 text-gray-600 font-medium text-sm mb-6">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center text-emerald-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span>{{ $student->circle?->name ?? 'غير محدد' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span>{{ $levelInfo['icon'] }} {{ $levelInfo['label'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $student->join_date?->format('d M Y') ?? '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ $student->date_of_birth ? $student->date_of_birth->age : '—' }} سنة</span>
                            </div>
                        </div>

                        {{-- ═══════════════════════════════════════════ --}}
                        {{-- أزرار التواصل الرئيسية                     --}}
                        {{-- ═══════════════════════════════════════════ --}}
                        <div class="flex flex-wrap gap-3">

                            {{-- زر واتساب --}}
                            @if($whatsappNumber)
                            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold rounded-2xl transition-all shadow-lg shadow-green-500/20 active:scale-95">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.555 4.122 1.524 5.856L.057 23.887l6.169-1.449C7.906 23.467 9.909 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.658-.523-5.168-1.432l-.371-.22-3.822.899.943-3.72-.242-.386C2.514 15.554 2 13.832 2 12 2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                                </svg>
                                واتساب
                                @if($student->whatsapp_owner)
                                <span class="text-xs opacity-80">({{ $student->whatsapp_owner }})</span>
                                @endif
                            </a>
                            @endif

                            {{-- زر الاتصال --}}
                            @if($phoneNumber)
                            <a href="tel:{{ $phoneNumber }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-2xl transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.5c.683.204.85.826.85 1.498V19a2 2 0 01-2 2h-3c-8.284 0-15-6.716-15-15v-3z" />
                                </svg>
                                اتصال
                                @if($student->additional_contact_owner)
                                <span class="text-xs opacity-80">({{ $student->additional_contact_owner }})</span>
                                @endif
                            </a>
                            @endif

                            {{-- زر رسالة واتساب مخصصة --}}
                            @if($whatsappNumber)
                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('السلام عليكم، بخصوص الطالب ' . $student->name . '...') }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-2xl transition-all shadow-sm active:scale-95">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                إرسال رسالة
                            </a>
                            @endif

                            {{-- زر تعديل --}}
                            @can('update', $student)
                            <a href="{{ route('students.edit', $student->id) }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-2xl transition-all shadow-sm active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                تعديل
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="flex items-center gap-6 mt-10 border-b border-gray-100 -mx-6 px-6 lg:-mx-10 lg:px-10 overflow-x-auto">
                    @unlessrole('guardian')
                    <button @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600'"
                        class="pb-4 font-bold transition-all px-2 whitespace-nowrap">نظرة عامة</button>
                    @endunlessrole
                    <button @click="activeTab = 'personal'"
                        :class="activeTab === 'personal' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600'"
                        class="pb-4 font-bold transition-all px-2 whitespace-nowrap">البيانات الشخصية</button>
                    <button @click="activeTab = 'academic'"
                        :class="activeTab === 'academic' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600'"
                        class="pb-4 font-bold transition-all px-2 whitespace-nowrap">المستوى القرآني</button>
                    <button @click="activeTab = 'attendance'"
                        :class="activeTab === 'attendance' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600'"
                        class="pb-4 font-bold transition-all px-2 whitespace-nowrap">الحضور والغياب</button>
                    <button @click="activeTab = 'fees'"
                        :class="activeTab === 'fees' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600'"
                        class="pb-4 font-bold transition-all px-2 whitespace-nowrap">الرسوم المالية</button>
                    <button @click="activeTab = 'care'"
                        :class="activeTab === 'care' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600'"
                        class="pb-4 font-bold transition-all px-2 whitespace-nowrap">الرعاية والسلوك</button>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- Tab: نظرة عامة                                         --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            @unlessrole('guardian')
            <div x-show="activeTab === 'overview'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- العمود الجانبي --}}
                <div class="space-y-6">

                    {{-- بطاقة التواصل السريع --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                        <h3 class="font-black text-gray-900 mb-5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.5c.683.204.85.826.85 1.498V19a2 2 0 01-2 2h-3c-8.284 0-15-6.716-15-15v-3z" />
                                </svg>
                            </div>
                            التواصل السريع
                        </h3>

                        <div class="space-y-3">
                            {{-- واتساب --}}
                            @if($whatsappNumber)
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <span class="block text-xs font-bold text-gray-500 mb-0.5">واتساب</span>
                                        <span class="font-black text-gray-800 text-sm" dir="ltr">{{ $student->whatsapp_number }}</span>
                                        @if($student->whatsapp_owner)
                                        <span class="block text-xs text-gray-400">{{ $student->whatsapp_owner }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank"
                                        class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold rounded-xl transition-all active:scale-95">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.555 4.122 1.524 5.856L.057 23.887l6.169-1.449C7.906 23.467 9.909 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.658-.523-5.168-1.432l-.371-.22-3.822.899.943-3.72-.242-.386C2.514 15.554 2 13.832 2 12 2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                                        </svg>
                                        فتح واتساب
                                    </a>
                                    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('السلام عليكم، بخصوص الطالب ' . $student->name . '...') }}"
                                        target="_blank"
                                        class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl transition-all active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        إرسال رسالة
                                    </a>
                                </div>
                            </div>
                            @endif

                            {{-- رقم الاتصال --}}
                            @if($phoneNumber)
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <span class="block text-xs font-bold text-gray-500 mb-0.5">رقم الاتصال</span>
                                        <span class="font-black text-gray-800 text-sm" dir="ltr">{{ $phoneNumber }}</span>
                                        @if($student->additional_contact_owner)
                                        <span class="block text-xs text-gray-400">{{ $student->additional_contact_owner }}</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="tel:{{ $phoneNumber }}"
                                    class="w-full flex items-center justify-center gap-1.5 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold rounded-xl transition-all active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.5c.683.204.85.826.85 1.498V19a2 2 0 01-2 2h-3c-8.284 0-15-6.716-15-15v-3z" />
                                    </svg>
                                    اتصال الآن
                                </a>
                            </div>
                            @endif

                            {{-- لو مفيش أرقام --}}
                            @if(!$whatsappNumber && !$phoneNumber)
                            <div class="text-center py-4 text-gray-400 text-sm">
                                لا توجد أرقام تواصل مسجلة
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- تحليل الأداء --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="font-black text-gray-900">تحليل الأداء</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-500 font-medium">نسبة الالتزام</span>
                                <span class="text-emerald-600 font-black">{{ $attendanceRate }}%</span>
                            </div>
                            <div class="relative w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="absolute top-0 left-0 h-full bg-emerald-500 rounded-full transition-all duration-1000"
                                    style="width:{{ $attendanceRate }}%"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <div class="bg-gray-50 rounded-2xl p-4 text-center">
                                    <span class="block text-2xl font-black text-emerald-600">{{ $presentCount }}</span>
                                    <span class="text-xs text-gray-500 font-bold">يوم حضور</span>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-4 text-center">
                                    <span class="block text-2xl font-black text-rose-500">{{ $absentCount }}</span>
                                    <span class="text-xs text-gray-500 font-bold">يوم غياب</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- معلومات ولي الأمر --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                        <h3 class="font-black text-gray-900 mb-5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            معلومات ولي الأمر
                        </h3>
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl mb-4">
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 shadow-sm overflow-hidden flex-shrink-0">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->guardian->name ?? 'ولي أمر') }}&background=6b7280&color=fff"
                                    class="w-full h-full object-cover" alt="Guardian">
                            </div>
                            <div>
                                <span class="block font-black text-gray-900">{{ $student->guardian->name ?? 'غير متوفر' }}</span>
                                <span class="block text-xs text-gray-500 font-bold">{{ $student->applicant ?? 'ولي أمر' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- الوضع المالي --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                        <h3 class="font-black text-gray-900 mb-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            الوضع المالي
                        </h3>
                        @if($unpaidMonthsCount > 0)
                        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 text-rose-700">
                            <p class="text-sm font-black">يوجد اشتراكات غير مدفوعة</p>
                            <p class="text-xs font-bold opacity-80 mt-1">إجمالي المتأخرات: {{ $unpaidMonthsCount }} شهر</p>
                        </div>
                        @else
                        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-emerald-700">
                            <p class="text-sm font-black">الاشتراكات مدفوعة بالكامل ✓</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- العمود الرئيسي --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- تقدم الحفظ --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-black text-gray-900 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                السورة الحالية
                            </h2>
                            <span class="text-2xl font-black text-emerald-600">{{ $student->constructionDetail?->current_surah ?? '—' }}</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <span class="block text-gray-500 font-bold text-xs mb-1">مستوى القراءة</span>
                                <span class="font-black text-gray-800">{{ $readingLabels[$student->reading ?? ''] ?? ($student->reading ?? '—') }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <span class="block text-gray-500 font-bold text-xs mb-1">مستوى الالتحاق</span>
                                <span class="font-black text-gray-800">{{ $levelInfo['icon'] }} {{ $levelInfo['label'] }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <span class="block text-gray-500 font-bold text-xs mb-1">النظام المتبع</span>
                                <span class="font-black text-gray-800">{{ $student->constructionDetail?->study_system ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- البيانات الإدارية --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                        <h2 class="text-xl font-black text-gray-900 mb-6">البيانات الإدارية</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <span class="text-gray-500 block font-bold text-xs mb-1">تاريخ الالتحاق</span>
                                <span class="font-black text-gray-800">{{ $student->join_date?->format('Y-m-d') ?? '—' }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <span class="text-gray-500 block font-bold text-xs mb-1">مقدم الطلب</span>
                                <span class="font-black text-gray-800">{{ $student->applicant ?? '—' }}{{ $student->applicant === 'أخرى' ? ' — ' . $student->applicant_other : '' }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <span class="text-gray-500 block font-bold text-xs mb-1">المشرف المسجّل</span>
                                <span class="font-black text-gray-800">{{ $student->supervisor?->user?->name ?? '—' }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <span class="text-gray-500 block font-bold text-xs mb-1">رسوم الحجز</span>
                                <span class="font-black text-gray-800">{{ $student->subscription_fees ? $student->subscription_fees . ' ج.م' : '—' }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <span class="text-gray-500 block font-bold text-xs mb-1">الأدوات المستلمة</span>
                                <span class="font-black text-gray-800">{{ $student->received_tools ?? '—' }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <span class="text-gray-500 block font-bold text-xs mb-1">خروج الطالب</span>
                                <span class="font-black text-gray-800">{{ $student->student_exit_status ?? '—' }}</span>
                            </div>
                        </div>
                        @if($student->notes)
                        <div class="mt-4 bg-amber-50 border border-amber-100 rounded-2xl p-4">
                            <span class="block font-black text-amber-700 text-sm mb-1">ملاحظات المشرف</span>
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $student->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endunlessrole

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- Tab: البيانات الشخصية                                  --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 'personal'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-black text-[#0a5c36] text-lg flex items-center gap-2"><span class="text-xl">👤</span> البيانات الأساسية</h3>
                    @php
                    $personalFields = [
                    ['label' => 'الاسم رباعياً', 'value' => $student->name],
                    ['label' => 'النوع', 'value' => $student->gender],
                    ['label' => 'تاريخ الميلاد', 'value' => $student->date_of_birth?->format('Y-m-d')],
                    ['label' => 'العمر', 'value' => ($student->date_of_birth ? $student->date_of_birth->age . ' سنة' : null)],
                    ['label' => 'كود الطالب', 'value' => $student->student_code],
                    ['label' => 'العنوان', 'value' => $student->address],
                    ['label' => 'المركز / الفرع', 'value' => $student->center?->name],
                    ];
                    @endphp
                    @foreach($personalFields as $field)
                    @if($field['value'])
                    <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                        <span class="text-gray-500 text-sm font-bold">{{ $field['label'] }}</span>
                        <span class="font-black text-gray-800 text-sm">{{ $field['value'] }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>

                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-black text-[#0a5c36] text-lg flex items-center gap-2"><span class="text-xl">🎓</span> البيانات الدراسية</h3>

                    @if($gradeChanged)
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
                        <span class="text-2xl">📅</span>
                        <div>
                            <p class="font-black text-amber-800 text-sm">تنبيه: موسم العام الدراسي الجديد</p>
                            <p class="text-amber-700 text-xs mt-1 leading-relaxed">
                                الصف المسجّل: <strong>{{ $savedGrade }}</strong> ← الصف الحالي: <strong>{{ $currentGrade }}</strong>
                            </p>
                            @can('update', $student)
                            <a href="{{ route('students.edit', $student->id) }}" class="inline-block mt-2 text-xs font-bold text-amber-700 underline">تحديث الصف الآن ←</a>
                            @endcan
                        </div>
                    </div>
                    @endif

                    @php
                    $academicFields = [
                    ['label' => 'المرحلة الدراسية', 'value' => $student->educational_stage],
                    ['label' => 'نوع التعليم', 'value' => $student->education_type],
                    ['label' => 'الصف الدراسي (مسجّل)', 'value' => $savedGrade],
                    ['label' => 'الصف الحالي', 'value' => $currentGrade, 'highlight' => $gradeChanged],
                    ['label' => 'المؤسسة التعليمية', 'value' => $student->previous_school],
                    ];
                    @endphp
                    @foreach($academicFields as $field)
                    @if($field['value'])
                    <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                        <span class="text-gray-500 text-sm font-bold">{{ $field['label'] }}</span>
                        <span class="font-black text-sm {{ ($field['highlight'] ?? false) ? 'text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg' : 'text-gray-800' }}">
                            {{ $field['value'] }}
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>

                {{-- بيانات التواصل --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-black text-[#0a5c36] text-lg flex items-center gap-2"><span class="text-xl">📱</span> بيانات التواصل</h3>
                    <div class="space-y-3">
                        @if($whatsappNumber)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm font-bold">واتساب</span>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-gray-800 text-sm" dir="ltr">{{ $student->whatsapp_number }}</span>
                                <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank"
                                    class="p-1.5 bg-[#25D366] text-white rounded-lg hover:bg-[#20bd5a] transition">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.555 4.122 1.524 5.856L.057 23.887l6.169-1.449C7.906 23.467 9.909 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.658-.523-5.168-1.432l-.371-.22-3.822.899.943-3.72-.242-.386C2.514 15.554 2 13.832 2 12 2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($phoneNumber)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm font-bold">رقم إضافي</span>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-gray-800 text-sm" dir="ltr">{{ $phoneNumber }}</span>
                                <a href="tel:{{ $phoneNumber }}"
                                    class="p-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.5c.683.204.85.826.85 1.498V19a2 2 0 01-2 2h-3c-8.284 0-15-6.716-15-15v-3z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endif

                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm font-bold">ولي الأمر</span>
                            <span class="font-black text-gray-800 text-sm">{{ $student->guardian?->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm font-bold">مقدم الطلب</span>
                            <span class="font-black text-gray-800 text-sm">{{ $student->applicant === 'أخرى' ? $student->applicant_other : ($student->applicant ?? '—') }}</span>
                        </div>
                    </div>
                </div>

                {{-- الرعاية والسمات --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-black text-[#0a5c36] text-lg flex items-center gap-2"><span class="text-xl">💚</span> الرعاية والسمات</h3>
                    @php
                    $careFields = [
                    ['label' => 'الحالة الصحية', 'value' => $student->health_status === 'أخرى' ? $student->health_status_other : $student->health_status],
                    ['label' => 'صعوبات التعلم', 'value' => $student->learning_difficulties === 'أخرى' ? $student->learning_difficulties_other : $student->learning_difficulties],
                    ['label' => 'السمات الشخصية', 'value' => $student->personal_traits === 'أخرى' ? $student->personal_traits_other : $student->personal_traits],
                    ['label' => 'خروج الطالب', 'value' => $student->student_exit_status],
                    ['label' => 'تفاصيل الخروج', 'value' => $student->exit_details],
                    ];
                    @endphp
                    @foreach($careFields as $field)
                    @if($field['value'])
                    <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                        <span class="text-gray-500 text-sm font-bold">{{ $field['label'] }}</span>
                        <span class="font-black text-gray-800 text-sm">{{ $field['value'] }}</span>
                    </div>
                    @endif
                    @endforeach

                    @if(!empty($hobbies))
                    <div class="pt-2">
                        <span class="text-gray-500 text-sm font-bold block mb-2">الهوايات</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($hobbies as $hobby)
                            @if($hobby !== 'أخرى')
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-100">{{ $hobby }}</span>
                            @endif
                            @endforeach
                            @if(in_array('أخرى', $hobbies) && $student->hobby_other)
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">{{ $student->hobby_other }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- Tab: المستوى القرآني                                   --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 'academic'" x-cloak class="space-y-8">
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                    <h2 class="text-xl font-black text-[#0a5c36] mb-6 flex items-center gap-2">
                        <span class="text-2xl">🎤</span> تقييم التلاوة ومستوى الالتحاق
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-2xl p-5 text-center border-2 border-emerald-100">
                            <span class="text-3xl block mb-2">{{ $levelInfo['icon'] }}</span>
                            <span class="block text-gray-500 text-xs font-bold mb-1">مستوى الالتحاق</span>
                            <span class="font-black text-gray-900">{{ $levelInfo['label'] }}</span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-5 text-center">
                            <span class="block text-gray-500 text-xs font-bold mb-1">مستوى القراءة</span>
                            <span class="font-black text-gray-900 text-lg">{{ $student->reading ?? '—' }}</span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-5 text-center">
                            <span class="block text-gray-500 text-xs font-bold mb-1">السورة الحالية</span>
                            <span class="font-black text-gray-900 text-lg">{{ $student->constructionDetail?->current_surah ?? '—' }}</span>
                        </div>
                    </div>

                    @if($student->center_entry_level === 'construction' && $student->constructionDetail)
                    <div class="border border-emerald-100 rounded-2xl p-6 bg-emerald-50/30 space-y-4">
                        <h3 class="font-black text-emerald-700">🌱 تفاصيل مستوى البناء</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                            @foreach([
                            ['label' => 'الحلقة', 'value' => $student->constructionDetail->group_name],
                            ['label' => 'النظام المتبع', 'value' => $student->constructionDetail->study_system],
                            ['label' => 'مستوى الحفظ', 'value' => $student->constructionDetail->placement_evaluation],
                            ['label' => 'خطة الحفظ الجديد', 'value' => $student->constructionDetail->new_memorization_plan],
                            ['label' => 'خطة المراجعة', 'value' => $student->constructionDetail->old_memorization_plan === 'أخرى' ? $student->constructionDetail->old_memorization_plan_other : $student->constructionDetail->old_memorization_plan],
                            ] as $f)
                            @if($f['value'])
                            <div class="bg-white rounded-xl p-3 border border-emerald-50">
                                <span class="text-gray-500 font-bold text-xs block mb-1">{{ $f['label'] }}</span>
                                <span class="font-black text-gray-800">{{ $f['value'] }}</span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($student->center_entry_level === 'mastery' && $student->itqanDetail)
                    <div class="border border-amber-100 rounded-2xl p-6 bg-amber-50/30 space-y-4">
                        <h3 class="font-black text-amber-700">⭐ تفاصيل مستوى الإتقان</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                            @foreach([
                            ['label' => 'جهة الحفظ السابقة', 'value' => $student->itqanDetail->previous_memorization_side],
                            ['label' => 'عدد الختمات', 'value' => $student->itqanDetail->previous_khatamat_count],
                            ['label' => 'مقدار المراجعة', 'value' => $student->itqanDetail->current_review_amount],
                            ['label' => 'التقييم الذاتي', 'value' => $student->itqanDetail->self_evaluation ? $student->itqanDetail->self_evaluation . '/10' : null],
                            ['label' => 'متن التجويد', 'value' => $student->itqanDetail->tajweed_matn === 'أخرى' ? $student->itqanDetail->tajweed_matn_other : $student->itqanDetail->tajweed_matn],
                            ['label' => 'المسار المرغوب', 'value' => $student->itqanDetail->desired_path],
                            ] as $f)
                            @if($f['value'])
                            <div class="bg-white rounded-xl p-3 border border-amber-50">
                                <span class="text-gray-500 font-bold text-xs block mb-1">{{ $f['label'] }}</span>
                                <span class="font-black text-gray-800">{{ $f['value'] }}</span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($student->center_entry_level === 'creativity' && $student->ibdaDetail)
                    <div class="border border-indigo-100 rounded-2xl p-6 bg-indigo-50/30 space-y-4">
                        <h3 class="font-black text-indigo-700">🏆 تفاصيل مستوى الإبداع</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            @if($student->ibdaDetail->previous_licenses_and_chains)
                            <div class="bg-white rounded-xl p-4 border border-indigo-50 md:col-span-2">
                                <span class="text-gray-500 font-bold text-xs block mb-2">الإجازات والأسانيد السابقة</span>
                                <p class="text-gray-800 leading-relaxed">{{ $student->ibdaDetail->previous_licenses_and_chains }}</p>
                            </div>
                            @endif
                            @foreach([
                            ['label' => 'الرواية المراد دراستها', 'value' => $student->ibdaDetail->desired_narration_and_path],
                            ['label' => 'الوقت المناسب', 'value' => $student->ibdaDetail->preferred_time],
                            ['label' => 'المعلم المفضل', 'value' => $student->ibdaDetail->supervisor_name],
                            ] as $f)
                            @if($f['value'])
                            <div class="bg-white rounded-xl p-3 border border-indigo-50">
                                <span class="text-gray-500 font-bold text-xs block mb-1">{{ $f['label'] }}</span>
                                <span class="font-black text-gray-800">{{ $f['value'] }}</span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- Tab: الحضور والغياب                                    --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 'attendance'" x-cloak>
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 lg:p-8 border-b border-gray-50 flex flex-wrap items-center justify-between gap-4">
                        <h3 class="text-[#0a5c36] font-black text-xl">سجل الحضور والغياب</h3>
                        @can('create attendance')
                        <a href="{{ route('attendance.create') }}"
                            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 rounded-xl text-white font-bold transition-all flex items-center gap-2 text-sm shadow-lg shadow-emerald-900/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            تسجيل حضور
                        </a>
                        @endcan
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50 font-black text-gray-400 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-5">التاريخ</th>
                                    <th class="px-6 py-5">الحالة</th>
                                    <th class="px-6 py-5">الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @php
                                $statusClasses = ['present' => 'bg-emerald-100 text-emerald-700', 'absent' => 'bg-rose-100 text-rose-700', 'late' => 'bg-amber-100 text-amber-700', 'excused' => 'bg-blue-100 text-blue-700'];
                                $statusLabels = ['present' => 'حاضر', 'absent' => 'غائب', 'late' => 'متأخر', 'excused' => 'بعذر'];
                                @endphp
                                @forelse($student->attendances->sortByDesc('date') as $att)
                                <tr class="hover:bg-emerald-50/30 transition-all">
                                    <td class="px-6 py-5 font-medium text-gray-800">
                                        {{ $att->date->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $statusClasses[$att->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $statusLabels[$att->status] ?? $att->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-gray-500 text-sm">{{ $att->notes ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-400 font-medium">لا توجد سجلات حضور</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- Tab: الرسوم المالية                                    --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 'fees'" x-cloak class="space-y-6">

                @if($student->status === 'متوقف' && $suspendedPastDebt > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="text-amber-800">
                        <p class="font-black text-sm">ملاحظة إدارية</p>
                        <p class="text-sm font-medium mt-1">الطالب موقوف ولديه رصيد مستحق: <strong>{{ number_format($suspendedPastDebt, 2) }} ج.م</strong></p>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-emerald-100">
                        <span class="text-gray-500 text-xs font-bold block mb-2">الأشهر المدفوعة</span>
                        <p class="text-3xl font-black text-emerald-600">{{ $paidMonthsCount }}</p>
                    </div>
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-rose-100">
                        <span class="text-gray-500 text-xs font-bold block mb-2">الأشهر المتأخرة</span>
                        <p class="text-3xl font-black text-rose-600">{{ $unpaidMonthsCount }}</p>
                    </div>
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-blue-100">
                        <span class="text-gray-500 text-xs font-bold block mb-2">إجمالي المدفوع</span>
                        <p class="text-3xl font-black text-blue-600">{{ number_format($totalPaidAmount, 2) }} <span class="text-sm text-gray-400">ج.م</span></p>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 lg:p-8 border-b border-gray-50 flex flex-wrap items-center justify-between gap-4">
                        <h3 class="text-[#0a5c36] font-black text-xl">سجل الدفعات</h3>
                        @can('recordPayment', $student)
                        <a href="{{ route('subscriptions.create') }}"
                            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 rounded-xl text-white font-bold transition-all flex items-center gap-2 text-sm shadow-lg shadow-emerald-900/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            تسجيل دفعة جديدة
                        </a>
                        @endcan
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50 font-black text-gray-400 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-5">الشهر</th>
                                    <th class="px-6 py-5">المبلغ</th>
                                    <th class="px-6 py-5">تاريخ الدفع</th>
                                    <th class="px-6 py-5">طريقة الدفع</th>
                                    <th class="px-6 py-5">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($feeTimeline ?? [] as $entry)
                                <tr class="{{ $entry->is_paid ? 'hover:bg-emerald-50/30' : 'bg-rose-50/40 hover:bg-rose-50/60' }} transition-all">
                                    <td class="px-6 py-5 font-medium {{ $entry->is_paid ? 'text-gray-800' : 'text-rose-800' }}">
                                        {{ $entry->month->locale('ar')->isoFormat('MMMM YYYY') }}
                                    </td>
                                    <td class="px-6 py-5 font-black {{ $entry->is_paid ? 'text-gray-800' : 'text-rose-600' }}">
                                        {{ $entry->subscription ? number_format($entry->subscription->amount, 2) : '—' }} ج.م
                                    </td>
                                    <td class="px-6 py-5 text-gray-500 text-sm">{{ $entry->subscription?->paid_at?->format('Y/m/d') ?? '—' }}</td>
                                    <td class="px-6 py-5">
                                        @if($entry->subscription)
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">
                                            {{ ['cash' => 'نقدي', 'transfer' => 'تحويل بنكي'][$entry->subscription->payment_method] ?? $entry->subscription->payment_method }}
                                        </span>
                                        @else
                                        <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($entry->is_paid)
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">مدفوع</span>
                                        @else
                                        <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-lg text-xs font-bold">غير مدفوع</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">لا توجد اشتراكات مسجلة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- Tab: الرعاية والسلوك                                   --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 'care'" x-cloak class="space-y-6">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <h3 class="text-[#0a5c36] font-black text-xl mb-6">الملاحظات السلوكية والتأديبية</h3>
                </div>
            </div>

        </div>

        @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-in {
                animation: fadeIn 0.5s ease-out forwards;
            }
        </style>
        @endpush

</x-layouts.markaz-layout>