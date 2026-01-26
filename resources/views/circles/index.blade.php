<x-layouts.markaz-layout>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">إدارة الحلقات</h1>
            <p class="text-gray-600">هنا يمكنك عرض وإدارة جميع الحلقات المتاحة في المركز.</p>
        </div>
        <div>
            <a href="{{ route('circles.create') }}" class="inline-flex items-center px-4 py-2 bg-[#0a5c36] hover:bg-[#084a2c] text-white font-semibold rounded-lg shadow-md transition-all duration-200 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>إضافة حلقة جديدة</span>
            </a>
        </div>
    </div>

    <!-- Circles Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الاسم</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">النوع</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المستوى</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">أقصى عدد</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الحالة</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 text-left">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($circles as $circle)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $circle->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $circle->type == 'Individual' ? 'فردية' : 'جماعية' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                    @if($circle->level == 'Foundation') bg-blue-50 text-blue-600 
                                    @elseif($circle->level == 'Advanced') bg-purple-50 text-purple-600
                                    @else bg-amber-50 text-amber-600 @endif">
                                    {{ $circle->level }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $circle->max_students }}
                            </td>
                            <td class="px-6 py-4">
                                @if($circle->is_active)
                                    <span class="flex items-center gap-1.5 text-emerald-600">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        <span class="text-sm font-medium">نشطة</span>
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 text-gray-400">
                                        <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                        <span class="text-sm font-medium">غير نشطة</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-left">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('circles.edit', $circle) }}" class="text-blue-500 hover:text-blue-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button class="text-red-400 hover:text-red-600 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                لا توجد حلقات مسجلة حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.markaz-layout>
