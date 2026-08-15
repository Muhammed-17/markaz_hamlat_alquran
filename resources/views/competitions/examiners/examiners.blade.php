<x-layouts.markaz-layout>
    <div dir="rtl"
        class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
        <div class="text-right w-full md:w-auto z-10">
            <h1 class="text-3xl font-black mb-2">مختبرو المسابقة</h1>
            <p class="text-emerald-100/80 text-sm font-medium">{{ $competition->name }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <a href="{{ route('competitions.index', $competition) }}"
                class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl flex items-center gap-2 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                رجوع للمسابقة
            </a>
            <a href="{{ route('competitions.examiners.create', $competition) }}"
                class="w-full md:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                إضافة مختبر
            </a>
        </div>

        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">اسم المختبر</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المستويات المكلف بها</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 text-left">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($examiners as $competitionExaminer)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-800">
                            {{ $competitionExaminer->examiner->user->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse ($competitionExaminer->competitionExaminerLevels as $competitionExaminerLevel)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#0a5c36]">
                                    {{ $competitionExaminerLevel->competitionLevel->level->name ?? '-' }}
                                </span>
                                @empty
                                <span class="text-xs text-gray-400">لا توجد مستويات</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-left">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('competitions.examiners.edit', [$competition, $competitionExaminer]) }}"
                                    class="text-blue-500 hover:text-blue-700 transition" title="تعديل المستويات">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <a href="{{ route('competitions.questions', [$competition, $competitionExaminer]) }}"
                                    class="text-emerald-600 hover:text-emerald-800 transition" title="أسئلة المختبر">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </a>
                                <form action="{{ route('competitions.examiners.destroy', [$competition, $competitionExaminer]) }}" method="POST"
                                    onsubmit="confirmDelete(event, { name: '{{ e($competitionExaminer->examiner->user->name ?? 'المختبر') }}', type: 'المختبر' })"
                                    class="text-red-400 hover:text-red-600 transition">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">لا يوجد مختبرون مضافون بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.markaz-layout>
