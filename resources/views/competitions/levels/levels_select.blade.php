<x-layouts.markaz-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">اختيار مستويات المسابقة</h1>
            <p class="text-gray-600">{{ $competition->name }}</p>
        </div>

        <form action="{{ route('competitions.levels.update', $competition) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 divide-y divide-gray-100 overflow-hidden">
                @forelse ($levels as $level)
                <label class="flex items-center gap-3 px-6 py-4 hover:bg-gray-50 transition cursor-pointer">
                    <input type="checkbox" name="level_ids[]" value="{{ $level->id }}"
                        @checked(in_array($level->id, $selectedLevelIds))
                    class="w-4 h-4 rounded border-gray-300 text-[#0a5c36] focus:ring-emerald-200">
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $level->name }}</p>
                        @if ($level->memorization_part)
                        <p class="text-xs text-gray-500">{{ $level->memorization_part }}</p>
                        @elseif ($level->memorization_from_part || $level->memorization_to_part)
                        <p class="text-xs text-gray-500">
                            من {{ $level->memorization_from_part ?? '...' }} إلى {{ $level->memorization_to_part ?? '...' }}
                        </p>
                        @endif
                    </div>
                </label>
                @empty
                <p class="px-6 py-10 text-center text-gray-400 text-sm">لا توجد مستويات مسجلة بالنظام.</p>
                @endforelse
            </div>

            <div class="flex justify-end gap-4 border-t pt-6 mt-6">
                <a href="{{ route('competitions.levels', $competition) }}"
                    class="px-6 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition-all font-bold">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-8 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d] text-white rounded-xl font-bold transition-all shadow-md">
                    حفظ المستويات
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>