<x-layouts.markaz-layout>
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="mb-6">
            <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">
                {{ $competitionExaminer ? 'تعديل مستويات المختبر' : 'إضافة مختبر إلى المسابقة' }}
            </h1>
            <p class="text-gray-600">{{ $competition->name }}</p>
        </div>

        <form action="{{ $competitionExaminer
                ? route('competitions.examiners.update', [$competition, $competitionExaminer])
                : route('competitions.examiners.store', $competition) }}"
            method="POST">
            @csrf
            @if ($competitionExaminer) @method('PUT') @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                <div class="flex items-center gap-3 mb-2 border-b border-gray-50 pb-4">
                    <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">بيانات المختبر</h2>
                </div>

                {{-- اختيار المختبر --}}
                <div class="space-y-2">
                    <label for="examiner_id" class="block text-sm font-bold text-gray-700">المختبر</label>

                    @if ($competitionExaminer)
                    <input type="text" value="{{ $selectedExaminer->user->name ?? '' }}" disabled
                        class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-2xl text-sm text-gray-500">
                    @else
                    <x-searchable-select
                        name="examiner_id"
                        placeholder="اختر المختبر"
                        search-placeholder="ابحث بالاسم أو رقم الهاتف..."
                        :default-value="old('examiner_id', '')"
                        :options="$examinerOptions ?? []" />

                    @error('examiner_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @endif
                </div>

                {{-- المستويات --}}
                <div class="space-y-2">
                    <span class="block text-sm font-bold text-gray-700">المستويات</span>
                    <div class="rounded-2xl border border-gray-200 divide-y divide-gray-100 overflow-hidden">
                        @forelse ($levels as $competitionLevel)
                        <label class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition cursor-pointer">
                            <input type="checkbox" name="competition_level_ids[]" value="{{ $competitionLevel->id }}"
                                @checked(in_array($competitionLevel->id, $selectedLevelIds))
                            class="w-4 h-4 rounded border-gray-300 text-[#0a5c36] focus:ring-emerald-200">
                            <span class="text-sm text-gray-700">{{ $competitionLevel->level->name ?? '-' }}</span>
                        </label>
                        @empty
                        <p class="px-4 py-6 text-center text-gray-400 text-sm">لا توجد مستويات مضافة لهذه المسابقة.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 border-t pt-6 mt-6">
                <a href="{{ route('competitions.examiners', $competition) }}"
                    class="px-6 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition-all font-bold">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-8 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d] text-white rounded-xl font-bold transition-all shadow-md">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>