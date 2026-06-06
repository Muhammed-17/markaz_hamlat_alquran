<x-layouts.markaz-layout>
    <!-- Header Card -->
    <div dir="rtl"
        class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
        <!-- العنوان أولًا في HTML -->
        <div class="text-right w-full md:w-auto z-10">
            <h1 class="text-3xl font-black mb-2">إدارة الحلقات</h1>
            <p class="text-emerald-100/80 text-sm font-medium">عرض وإدارة الحلقات الدراسية في المركز</p>
        </div>

        <!-- الزر ثانيًا في HTML -->
        <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
            @role('admin')
                <a href="{{ route('circles.create') }}"
                    class="w-full md:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    جديد
                </a>
            @endrole
        </div>

        <!-- Decorative Element -->
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الاسم</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">النوع</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المستوى</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">أقصى عدد</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المعلم</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المعلم المساعد</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المشرف</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 text-left">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($circles as $circle)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">
                                {{ $circle->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                @if ($circle->type == 'group')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600">
                                        جماعية
                                    </span>
                                @elseif ($circle->type == 'individual')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-600">
                                        فردية
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $levelStyles = match ($circle->level) {
                                        'build' => 'bg-blue-50 text-blue-600',
                                        'mastery' => 'bg-purple-50 text-purple-600',
                                        'creativity' => 'bg-amber-50 text-amber-600',
                                        default => 'bg-gray-50 text-gray-600',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $levelStyles }}">
                                    @if ($circle->level == 'build')
                                        بناء
                                    @elseif ($circle->level == 'mastery')
                                        إتقان
                                    @elseif ($circle->level == 'creativity')
                                        إبداع
                                    @endif
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $circle->max_students }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $circle->mainTeacher->first()?->name ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $circle->assistantTeacher->first()?->name ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $circle->supervisor?->name ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-left">
                                <div class="flex items-center justify-end gap-3">
                                    <!-- Edit -->
                                    @role(['admin', 'supervisor'])
                                        <a href="{{ route('circles.edit', $circle) }}"
                                            class="text-blue-500 hover:text-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endrole

                                    <!-- Delete -->
                                    @role('admin')
                                        <form method="POST" action="{{ route('circles.destroy', $circle->id) }}"
                                            class="text-red-400 hover:text-red-600 transition">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                لا توجد حلقات مسجلة حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.markaz-layout>
