<x-layouts.markaz-layout>
    <div dir="rtl"
        class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
        <div class="text-right w-full md:w-auto z-10">
            <h1 class="text-3xl font-black mb-2">أسئلة المستويات</h1>
            <p class="text-emerald-100/80 text-sm font-medium">{{ $competition->name }}</p>
        </div>

        <a href="{{ route('competitions.levels', $competition) }}"
            class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl flex items-center gap-2 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            رجوع للمستويات
        </a>

        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="mb-6" x-data="{
    goToLevel(levelId) {
        if (!levelId) {
            window.location.href = '{{ route('competitions.level-questions', $competition) }}';
            return;
        }
        window.location.href = `{{ route('competitions.level-questions', $competition) }}?level_id=${levelId}`;
    }
}" x-init="window.addEventListener('searchable-change', (e) => {
    if (e.detail.name === 'level_select') goToLevel(e.detail.value);
})">
        <x-searchable-select
            name="level_select"
            placeholder="اختر المستوى..."
            search-placeholder="ابحث عن مستوى..."
            :default-value="$activeLevelId ?? ''"
            :options="$levels->map(fn($competitionLevel) => [
            'value' => $competitionLevel->id,
            'label' => $competitionLevel->level->name ?? '-',
        ])->values()->toArray()" />
    </div>

    @if ($activeLevelId)
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800">الأسئلة</h2>

        @can('create level questions')
        <a href="{{ route('competitions.level-questions.create', [$competition, 'level_id' => $activeLevelId]) }}"
            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl flex items-center gap-2 transition-all text-sm shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            إضافة سؤال
        </a>
        @endcan
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الاسم</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">النوع</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الدرجة</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المختبر</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 text-left">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($questions as $question)
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                        onclick="window.location='{{ route('competitions.level-questions.show', [$competition, $question]) }}'">
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $question->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $question->typeLabel() }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $question->score }}</td>
                        <td class="px-6 py-4">
                            @if ($question->competitionExaminers->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach ($question->competitionExaminers as $competitionExaminer)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#0a5c36]">
                                    {{ $competitionExaminer->examiner->user->name ?? '-' }}
                                </span>
                                @endforeach
                            </div>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">غير محدد</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-left" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-3">
                                @can('view level questions')
                                <a href="{{ route('competitions.level-questions.show', [$competition, $question]) }}"
                                    class="text-emerald-600 hover:text-emerald-800 transition" title="عرض السؤال">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @endcan

                                @can('edit level questions')
                                <a href="{{ route('competitions.level-questions.edit', [$competition, $question]) }}"
                                    class="text-blue-500 hover:text-blue-700 transition" title="تعديل السؤال">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan
                                @can('delete level questions')
                                <form action="{{ route('competitions.level-questions.destroy', [$competition, $question]) }}" method="POST"
                                    onsubmit="confirmDelete(event, { name: '{{ e($question->name) }}', type: 'السؤال' })"
                                    class="text-red-400 hover:text-red-600 transition">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="حذف السؤال">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">لا توجد أسئلة لهذا المستوى.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</x-layouts.markaz-layout>