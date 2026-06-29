<x-markaz-layout>
    <x-slot name="title">سجل حضور أبنائي</x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">سجل حضور أبنائي</h1>
            <p class="text-gray-500 mt-1">متابعة الحضور والغياب للطلاب المرتبطين بك</p>
        </div>

        <!-- Month Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('attendance.own') }}" method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">الشهر</label>
                    <input type="month" name="month" value="{{ $month }}"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    عرض
                </button>
            </form>
        </div>

        @forelse($summary as $item)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <!-- Student Header -->
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $item['student']->name }}</h2>
                            <p class="text-gray-500">{{ $item['student']->circle->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold
                                @if($item['attendance_rate'] >= 90) text-green-600
                                @elseif($item['attendance_rate'] >= 70) text-yellow-600
                                @else text-red-600 @endif">
                            {{ $item['attendance_rate'] }}%
                        </div>
                        <div class="text-sm text-gray-500">نسبة الحضور</div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $item['present'] }}</div>
                    <div class="text-sm text-green-700">حاضر</div>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ $item['absent'] }}</div>
                    <div class="text-sm text-red-700">غائب</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $item['late'] }}</div>
                    <div class="text-sm text-yellow-700">متأخر</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $item['excused'] }}</div>
                    <div class="text-sm text-blue-700">بعذر</div>
                </div>
            </div>

            <!-- Daily Records -->
            <div class="px-6 pb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">تفاصيل الأيام</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-right text-gray-600">التاريخ</th>
                                <th class="px-4 py-2 text-right text-gray-600">اليوم</th>
                                <th class="px-4 py-2 text-right text-gray-600">الحالة</th>
                                <th class="px-4 py-2 text-right text-gray-600">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($item['student']->attendances as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $record->date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ $record->date->format('l') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($record->status === 'present') bg-green-100 text-green-800
                                                @elseif($record->status === 'absent') bg-red-100 text-red-800
                                                @elseif($record->status === 'late') bg-yellow-100 text-yellow-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                        {{ $record->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $record->notes ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                    لا توجد سجلات حضور في هذه الفترة
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="text-gray-500 text-lg">لا يوجد طلاب مرتبطين بك حالياً</p>
        </div>
        @endforelse
    </div>
</x-markaz-layout>