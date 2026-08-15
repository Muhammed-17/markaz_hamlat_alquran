<x-layouts.markaz-layout>
    <x-slot name="title">المشاركون</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <span>/</span>
        <a href="{{ route('admin.competitions.index') }}" class="hover:text-[#0a5c36]">المسابقات</a>
        <span>/</span>
        <span>{{ $competitionLevel->level?->name }}</span>
    </nav>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-black text-gray-800">المشاركون — {{ $competitionLevel->level?->name }}</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-4 p-4">
        <form method="GET" class="flex items-center gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث باسم المشارك..."
                class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-xl outline-none transition-all text-sm">
            <button type="submit" class="px-5 py-2.5 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-xl text-sm transition-all">
                بحث
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="px-6 py-3 text-right font-bold">الاسم</th>
                        <th class="px-6 py-3 text-right font-bold">نوع المشارك</th>
                        <th class="px-6 py-3 text-right font-bold">الحالة</th>
                        <th class="px-6 py-3 text-right font-bold">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($participants as $participant)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $participant->participant_name }}</td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $participant->participant_type === 'student' ? 'طالب' : 'مشارك خارجي' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($participant->exam_status === 'registered')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">مسجَّل</span>
                            @elseif($participant->exam_status === 'testing')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">جاري الاختبار</span>
                            @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">مكتمل</span>
                            @endif

                            @if($participant->is_manual_result)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 mr-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700"
                                title="تم إدخال هذه النتيجة يدويًا بدون اختبار أسئلة">
                                يدوي
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($participant->exam_status === 'completed')
                                <a href="{{ route('admin.participants.result', $participant) }}"
                                    class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs transition-all">
                                    عرض النتيجة
                                </a>
                                @elseif($participant->exam_status === 'testing')
                                <a href="{{ route('admin.exam.show', $participant) }}"
                                    class="px-4 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg text-xs transition-all">
                                    متابعة
                                </a>
                                @else
                                <a href="{{ route('admin.exam.show', $participant) }}"
                                    class="px-4 py-1.5 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-lg text-xs transition-all">
                                    بدء الاختبار
                                </a>
                                @endif

                                {{-- إدخال/تعديل نتيجة يدوي — متاح دائمًا بغض النظر عن حالة الاختبار --}}
                                <a href="{{ route('admin.participants.manual-result.form', $participant) }}"
                                    class="px-4 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold rounded-lg text-xs transition-all">
                                    إدخال نتيجة يدوي
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400">لا يوجد مشاركون.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$participants" />
    </div>
</x-layouts.markaz-layout>