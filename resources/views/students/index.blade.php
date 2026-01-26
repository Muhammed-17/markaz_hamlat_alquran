<x-layouts.markaz-layout>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#0a5c36]">إدارة الطلاب</h1>
            <p class="text-gray-500 mt-1">{{ count($students) }} طالب مسجل في النظام</p>
        </div>

        <a href="{{ route('students.create') }}"
           class="bg-[#10b981] hover:bg-[#059669] text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <span>إضافة طالب جديد</span>
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white p-4 rounded-xl border border-gray-100 flex gap-4">
        <input type="text" placeholder="بحث باسم الطالب أو المستوى..."
               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#10b981]/50">
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-x-auto">
        <table class="w-full text-right min-w-[900px]">
            <thead class="bg-gray-50 text-gray-500 text-sm">
                <tr>
                    <th class="py-4 px-6 font-medium rounded-tr-xl">اسم الطالب</th>
                    <th class="py-4 px-6 font-medium">العمر</th>
                    <th class="py-4 px-6 font-medium">الحلقة</th>
                    <th class="py-4 px-6 font-medium">المستوى الحالي</th>
                    <th class="py-4 px-6 font-medium">الحالة</th>
                    <th class="py-4 px-6 font-medium rounded-tl-xl"></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @foreach($students as $student)
                <tr class="hover:bg-gray-50/50">

                    <td class="py-4 px-6 font-medium text-gray-800">
                        {{ $student->name }}
                    </td>

                    <td class="py-4 px-6 text-gray-600">
                        {{ $student->age }}
                    </td>

                    <td class="py-4 px-6 text-gray-600">
                        {{ $student->circle->name ?? '—' }}
                    </td>

                    <td class="py-4 px-6 text-gray-600">
                        {{ $student->level }}
                    </td>

                    <td class="py-4 px-6">
                        @if($student->status === 'active')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-md text-sm">نشط</span>
                        @else
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-md text-sm">موقوف</span>
                        @endif
                    </td>

                    <!-- Actions -->
                    <td class="py-4 px-6 relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="text-gray-400 hover:text-gray-600">
                            ⋮
                        </button>

                        <div x-show="open" x-transition
                             class="absolute left-0 mt-2 w-40 bg-white rounded-md shadow-lg z-50 border py-1"
                             style="display:none;">

                            <a href="{{ route('students.show',$student->id) }}"
                               class="block px-4 py-2 text-sm hover:bg-gray-50">عرض</a>

                            <a href="{{ route('students.edit',$student->id) }}"
                               class="block px-4 py-2 text-sm hover:bg-gray-50">تعديل</a>

                            <form method="POST" action="{{ route('students.destroy',$student->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    حذف
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

</x-layouts.markaz-layout>
