<x-layouts.markaz-layout>
    <div dir="rtl"
        class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
        <div class="text-right w-full md:w-auto z-10">
            <h1 class="text-3xl font-black mb-2">اختيار الأسئلة</h1>
            <p class="text-emerald-100/80 text-sm font-medium">
                {{ $competition->name }} — {{ $competitionExaminer->examiner->user->name ?? '' }}
            </p>
        </div>

        <a href="{{ route('competitions.examiners', $competition) }}"
            class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl flex items-center gap-2 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            رجوع للمختبرين
        </a>

        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    {{-- اختيار المستوى --}}
    <div class="mb-6" x-data="{
        baseUrl: '{{ route('competitions.questions', [$competition, $competitionExaminer]) }}',
        goToLevel(levelId) {
            if (!levelId) return;
            window.location.href = this.baseUrl + '?level_id=' + levelId;
        }
    }"
        @searchable-change.window="if ($event.detail.name === 'level_selector') goToLevel($event.detail.value)">

        <label class="block text-sm font-bold text-gray-700 mb-2">المستوى</label>

        @if ($levels->isEmpty())
        <p class="text-center text-gray-400 text-sm py-6">لا توجد مستويات مسندة لهذا المختبر.</p>
        @else
        <x-searchable-select
            name="level_selector"
            placeholder="اختر المستوى..."
            search-placeholder="ابحث عن مستوى..."
            :default-value="$activeLevelId ?? ''"
            :options="$levels->map(fn($competitionLevel) => [
                'value' => $competitionLevel->competition_level_id,
                'label' => $competitionLevel->competitionLevel->level->name ?? '-',
            ])->values()->toArray()" />
        @endif
    </div>

    @if ($activeLevelId)
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800">أسئلة هذا المستوى</h2>
        <a href="{{ route('competitions.level-questions.create', [$competition, 'level_id' => $activeLevelId]) }}"
            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl flex items-center gap-2 transition-all text-sm shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            إضافة سؤال جديد للمستوى
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الاسم</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">النوع</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الدرجة</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الحالة</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 text-left">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($questions as $question)
                    @php
                    $otherExaminers = $question->competitionExaminers->reject(
                        fn($ce) => $ce->id === $competitionExaminer->id
                    );
                    $claimedByMe = $question->competitionExaminers->contains('id', $competitionExaminer->id);
                    $claimedByOthers = $otherExaminers->isNotEmpty();
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $question->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $question->typeLabel() }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $question->score }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @if ($claimedByMe)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#0a5c36]">مُختار من قِبلك</span>
                                @endif

                                @foreach ($otherExaminers as $otherExaminer)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">
                                    لدى {{ $otherExaminer->examiner->user->name ?? 'مختبر آخر' }}
                                </span>
                                @endforeach

                                @if (!$claimedByMe && !$claimedByOthers)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700">متاح</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-left">
                            <form action="{{ route('competitions.questions.toggle-claim', [$competition, $competitionExaminer, $question]) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition
                                        {{ $claimedByMe
                                            ? 'bg-red-50 text-red-600 hover:bg-red-100'
                                            : 'bg-[#0a5c36] text-white hover:bg-[#084d2d]' }}">
                                    {{ $claimedByMe ? 'إلغاء الاختيار' : 'اختيار السؤال' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">لا توجد أسئلة لهذا المستوى بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</x-layouts.markaz-layout>