<x-layouts.markaz-layout>
    <x-slot name="title">{{ $center->name }}</x-slot>

    {{-- ─── هيدر المركز ─── --}}
    <div dir="rtl" class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden shadow-xl mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
            <div>
                <h1 class="text-3xl font-black mb-2">{{ $center->name }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-emerald-100/80 text-sm">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $circles->count() }} حلقة
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        {{ $totalStudents }} طالب
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        أُنشئ في {{ $center->created_at?->format('Y-m-d') ?? '—' }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('centers.index') }}"
                    class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    رجوع
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    {{-- ─── تبويبات التنقل ─── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6" id="center-tabs">
        <div class="flex border-b border-gray-100 overflow-x-auto">
            <button onclick="switchTab('info')" id="tab-btn-info"
                class="tab-btn px-6 py-4 text-sm font-bold whitespace-nowrap transition-colors flex items-center gap-2 border-b-2 border-[#0a5c36] text-[#0a5c36]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                معلومات الفرع
            </button>

            <button onclick="switchTab('circles')" id="tab-btn-circles"
                class="tab-btn px-6 py-4 text-sm font-bold whitespace-nowrap transition-colors flex items-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                الحلقات ({{ $circles->count() }})
            </button>

            <button onclick="switchTab('lessons')" id="tab-btn-lessons"
                class="tab-btn px-6 py-4 text-sm font-bold whitespace-nowrap transition-colors flex items-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                الدروس التربوية ({{ ($activeLesson ? 1 : 0) + $lessonsHistory->count() }})
            </button>
        </div>

        {{-- ─── تبويب معلومات الفرع ─── --}}
        <div id="tab-info" class="tab-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="block text-xs font-bold text-gray-400 mb-1">اسم الفرع</label>
                        <p class="text-sm font-medium text-gray-800">{{ $center->name }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="block text-xs font-bold text-gray-400 mb-1">تاريخ الإنشاء</label>
                        <p class="text-sm font-medium text-gray-800">{{ $center->created_at?->format('Y-m-d') ?? '—' }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="block text-xs font-bold text-gray-400 mb-1">عدد الحلقات</label>
                        <p class="text-sm font-medium text-gray-800">{{ $circles->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="block text-xs font-bold text-gray-400 mb-1">عدد الطلاب الإجمالي</label>
                        <p class="text-sm font-medium text-gray-800">{{ $totalStudents }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── تبويب الحلقات ─── --}}
        <div id="tab-circles" class="tab-content p-6" style="display: none;">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-800">حلقات الفرع</h3>
                <a href="{{ route('circles.create', ['center_id' => $center->id]) }}"
                    class="px-4 py-2 bg-[#0a5c36] hover:bg-[#08492a] text-white text-sm font-bold rounded-lg flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة حلقة
                </a>
            </div>

            @if($circles->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-sm font-bold text-gray-600">الاسم</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-600">النوع</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-600">المستوى</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-600 text-center">عدد الطلاب</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-600">المعلم الرئيسي</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-600 text-left">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($circles as $circle)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-bold text-gray-800">{{ $circle->name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $circle->type === 'group' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                                    {{ $circle->type_arabic }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $circle->level_arabic }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $circle->students->count() }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $circle->mainTeachers->first()?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('circles.show', $circle) }}" class="text-emerald-600 hover:text-emerald-800 transition text-sm font-medium">
                                    عرض
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p>لا توجد حلقات مسجلة لهذا الفرع</p>
            </div>
            @endif
        </div>

        {{-- ─── تبويب الدروس التربوية ─── --}}
        <div id="tab-lessons" class="tab-content p-6" style="display: none;">
            <h3 class="text-lg font-bold text-gray-800 mb-4">إضافة درس تربوي جديد</h3>

            <form action="{{ route('centers.educational-lessons.store', $center) }}" method="POST" class="bg-gray-50 rounded-xl p-5 mb-8 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">عنوان الدرس *</label>
                    <input type="text" name="title" required
                        class="w-full p-2.5 px-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36]"
                        placeholder="مثال: درس الصدق">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">وصف الدرس</label>
                    <textarea name="description" rows="3"
                        class="w-full p-2.5 px-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] resize-none"
                        placeholder="وصف تفصيلي للدرس..."></textarea>
                </div>
                @error('title')
                <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
                <button type="submit"
                    class="px-6 py-2.5 bg-[#0a5c36] hover:bg-[#08492a] text-white font-bold rounded-xl text-sm transition-all">
                    إضافة الدرس
                </button>
            </form>

            <h3 class="text-lg font-bold text-gray-800 mb-4">الدرس النشط حالياً</h3>

            @if($activeLesson)
            <div class="border-2 border-[#0a5c36] bg-emerald-50/50 rounded-xl p-5 mb-8 relative">
                <span class="absolute top-4 left-4 px-3 py-1 bg-[#0a5c36] text-white text-xs font-bold rounded-full">نشط حالياً</span>

                {{-- عرض الدرس --}}
                <div id="lesson-view-{{ $activeLesson->id }}">
                    <h4 class="font-bold text-gray-800 text-base mb-2 mt-6">{{ $activeLesson->title }}</h4>
                    @if($activeLesson->description)
                    <p class="text-sm text-gray-600 leading-relaxed mb-3">{{ $activeLesson->description }}</p>
                    @endif
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-400">أُضيف في {{ $activeLesson->created_at?->format('Y-m-d H:i') }}</p>
                        <button type="button" onclick="toggleLessonEdit({{ $activeLesson->id }})"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0a5c36] hover:text-[#08492a] transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            تعديل
                        </button>
                    </div>
                </div>

                {{-- فورم التعديل (مخفي افتراضياً) --}}
                <form id="lesson-edit-{{ $activeLesson->id }}" method="POST"
                    action="{{ route('educational-lessons.update', $activeLesson) }}"
                    class="mt-6 space-y-3" style="display: none;">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1.5">عنوان الدرس *</label>
                        <input type="text" name="title" required value="{{ $activeLesson->title }}"
                            class="w-full p-2.5 px-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1.5">وصف الدرس</label>
                        <textarea name="description" rows="3"
                            class="w-full p-2.5 px-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] resize-none">{{ $activeLesson->description }}</textarea>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="px-5 py-2 bg-[#0a5c36] hover:bg-[#08492a] text-white font-bold rounded-xl text-sm transition-all">
                            حفظ التعديل
                        </button>
                        <button type="button" onclick="toggleLessonEdit({{ $activeLesson->id }})"
                            class="px-5 py-2 border border-gray-200 text-gray-600 hover:bg-gray-50 font-bold rounded-xl text-sm transition-all">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="text-center py-10 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200 mb-8">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p>لا توجد دروس تربوية مسجلة لهذا الفرع</p>
            </div>
            @endif

            @if($lessonsHistory->isNotEmpty())
            <h3 class="text-lg font-bold text-gray-800 mb-4">سجل الدروس السابقة</h3>
            <div class="space-y-3">
                @foreach($lessonsHistory as $lesson)
                <div class="border border-gray-100 rounded-xl p-4 hover:shadow-sm transition-shadow bg-white">
                    {{-- عرض الدرس --}}
                    <div id="lesson-view-{{ $lesson->id }}">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-gray-700 text-sm">{{ $lesson->title }}</h4>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400">{{ $lesson->created_at?->format('Y-m-d') }}</span>
                                <button type="button" onclick="toggleLessonEdit({{ $lesson->id }})"
                                    class="text-emerald-500 hover:text-emerald-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('educational-lessons.destroy', $lesson) }}"
                                    onsubmit="confirmDelete(event, { name: '{{ e($lesson->title) }}', type: 'الدرس التربوي' })" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($lesson->description)
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $lesson->description }}</p>
                        @endif
                    </div>

                    {{-- فورم التعديل (مخفي افتراضياً) --}}
                    <form id="lesson-edit-{{ $lesson->id }}" method="POST"
                        action="{{ route('educational-lessons.update', $lesson) }}"
                        class="space-y-3" style="display: none;">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-1.5">عنوان الدرس *</label>
                            <input type="text" name="title" required value="{{ $lesson->title }}"
                                class="w-full p-2.5 px-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-1.5">وصف الدرس</label>
                            <textarea name="description" rows="2"
                                class="w-full p-2.5 px-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] resize-none">{{ $lesson->description }}</textarea>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="px-4 py-1.5 bg-[#0a5c36] hover:bg-[#08492a] text-white font-bold rounded-lg text-xs transition-all">
                                حفظ
                            </button>
                            <button type="button" onclick="toggleLessonEdit({{ $lesson->id }})"
                                class="px-4 py-1.5 border border-gray-200 text-gray-600 hover:bg-gray-50 font-bold rounded-lg text-xs transition-all">
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('info');
        });

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
            const tabContent = document.getElementById('tab-' + tabName);
            if (tabContent) tabContent.style.display = 'block';

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-[#0a5c36]', 'text-[#0a5c36]');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            const activeBtn = document.getElementById('tab-btn-' + tabName);
            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-gray-500');
                activeBtn.classList.add('border-[#0a5c36]', 'text-[#0a5c36]');
            }
        }

        // ⬅️ إضافة: تبديل عرض/تعديل الدرس التربوي
        function toggleLessonEdit(lessonId) {
            const viewEl = document.getElementById('lesson-view-' + lessonId);
            const editEl = document.getElementById('lesson-edit-' + lessonId);

            if (editEl.style.display === 'none') {
                viewEl.style.display = 'none';
                editEl.style.display = 'block';
            } else {
                viewEl.style.display = 'block';
                editEl.style.display = 'none';
            }
        }
    </script>
</x-layouts.markaz-layout>