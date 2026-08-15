<x-layouts.markaz-layout>
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">عرض السؤال</h1>
                <p class="text-gray-600">{{ $competition->name }}</p>
            </div>
            <a href="{{ route('competitions.level-questions', [$competition, 'level_id' => $question->competition_level_id]) }}"
                class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-xl flex items-center gap-2 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                رجوع للأسئلة
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="flex items-center gap-3 mb-2 border-b border-gray-50 pb-4">
                <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">بيانات السؤال</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-500">المستوى</p>
                    <p class="text-gray-800 font-semibold">{{ $question->competitionLevel->level->name ?? '-' }}</p>
                </div>

                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-500">النوع</p>
                    <p class="text-gray-800 font-semibold">{{ $question->typeLabel() }}</p>
                </div>

                <div class="space-y-1 md:col-span-2">
                    <p class="text-sm font-bold text-gray-500">اسم السؤال</p>
                    <p class="text-gray-800 font-semibold">{{ $question->name }}</p>
                </div>

                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-500">الدرجة</p>
                    <p class="text-gray-800 font-semibold">{{ $question->score }}</p>
                </div>

                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-500">الحالة</p>
                    @if ($question->competition_examiner_id)
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#0a5c36]">
                        مُختار — {{ $question->competitionExaminer->examiner->user->name ?? '-' }}
                    </span>
                    @else
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700">متاح</span>
                    @endif
                </div>

                @if ($question->type === 'memorization')
                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-500">من</p>
                    <p class="text-gray-800 font-semibold">
                        {{ $question->memorizationFromSurah->name_arabic ?? '-' }}
                        (آية {{ $question->memorization_from_ayah }})
                    </p>
                </div>

                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-500">إلى</p>
                    <p class="text-gray-800 font-semibold">
                        {{ $question->memorizationToSurah->name_arabic ?? '-' }}
                        (آية {{ $question->memorization_to_ayah }})
                    </p>
                </div>
                @endif

                @if ($question->content)
                <div class="space-y-1 md:col-span-2">
                    <p class="text-sm font-bold text-gray-500">المحتوى</p>
                    <p class="text-gray-800 whitespace-pre-line">{{ $question->content }}</p>
                </div>
                @endif

                @if ($question->notes)
                <div class="space-y-1 md:col-span-2">
                    <p class="text-sm font-bold text-gray-500">ملاحظات</p>
                    <p class="text-gray-800 whitespace-pre-line">{{ $question->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="flex justify-end gap-4 border-t border-gray-100 pt-6">
            @can('delete competitions')
            <form method="POST" action="{{ route('competitions.level-questions.destroy', [$competition, $question]) }}"
                onsubmit="confirmDelete(event, { name: '{{ e($question->name) }}', type: 'السؤال' })">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-6 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl font-bold transition-all">
                    حذف السؤال
                </button>
            </form>
            @endcan

            @can('update', $competition)
            <a href="{{ route('competitions.level-questions.edit', [$competition, $question]) }}"
                class="px-8 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d] text-white rounded-xl font-bold transition-all shadow-md">
                تعديل السؤال
            </a>
            @endcan
        </div>
    </div>
</x-layouts.markaz-layout>