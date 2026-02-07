<x-layouts.markaz-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#0a5c36]">إعدادات أسعار الاشتراكات</h1>
                <p class="text-gray-500 mt-1">تحديد رسوم الاشتراك لكل مرحلة وحلقة</p>
            </div>
        </div>

        <!-- Add/Update Form -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">إضافة / تعديل سعر</h2>
            <form id="priceForm" action="{{ route('subscription-prices.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf

                <!-- Education Level -->
                <x-custom-select name="education_level" label="المرحلة الدراسي">
                    <option value="preschool" {{ old('education_level') == 'preschool' ? 'selected' : '' }}>حضانة
                    </option>
                    <option value="primary" {{ old('education_level') == 'primary' ? 'selected' : '' }}>ابتدائية
                    </option>
                    <option value="secondary" {{ old('education_level') == 'secondary' ? 'selected' : '' }}>إعدادية
                    </option>
                    <option value="high_school" {{ old('education_level') == 'high_school' ? 'selected' : '' }}>ثانوية
                    </option>
                    <option value="university" {{ old('education_level') == 'university' ? 'selected' : '' }}>جامعية
                    </option>
                    <option value="other" {{ old('education_level') == 'other' ? 'selected' : '' }}>أخرى</option>
                </x-custom-select>


                <x-custom-select name="circle_level" label="مستوى الحلقة">
                    <option value="build" {{ old(('circle_level')) == 'build' ? 'selected' : ''}}>بناء</option>
                    <option value="mastery" {{ old(('circle_level')) == 'mastery' ? 'selected' : ''}}>إتقان</option>
                    <option value="creativity" {{ old(('circle_level')) == 'creativity' ? 'selected' : ''}}>إبداع</option>
                </x-custom-select>

                <x-custom-input name="amount" type="number" value="{{ old('name') }}" placeholder="0.00" label="قيمة الاشتراك (ج.م)" />

                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-[#10b981] hover:bg-[#059669] text-white px-6 py-2 rounded-lg font-bold transition h-[42px] flex-1">
                        حفظ
                    </button>
                    <button type="button" onclick="resetForm()"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg font-bold transition h-[42px]">
                        مسح
                    </button>
                </div>
            </form>
            @if ($errors->any())
                <div class="mt-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Prices Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="py-4 px-6 font-medium">المرحلة الدراسية</th>
                        <th class="py-4 px-6 font-medium">مستوى الحلقة</th>
                        <th class="py-4 px-6 font-medium">قيمة الاشتراك</th>
                        <th class="py-4 px-6 font-medium">تاريخ التحديث</th>
                        <th class="py-4 px-6 font-medium">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($prices as $price)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-4 px-6 text-gray-800 font-medium">
                                @if ($price->education_level == 'preschool')
                                   حضانة
                                @elseif ($price->education_level == 'primary')
                                    ابتدائية
                                @elseif ($price->education_level == 'secondary')
                                    اعدادية
                                @elseif ($price->education_level == 'high_school')
                                    ثانوية
                                @elseif ($price->education_level == 'university')
                                    جامعية
                                @elseif ($price->education_level== 'other')
                                    أخرى
                                @endif
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                @if ($price->circle_level == "build")
                                    بناء
                                @elseif ($price->circle_level == "mastery")
                                    إتقان
                                @elseif ($price->circle_level == "creativity")
                                    إبداع
                                @endif
                            </td>
                            <td class="py-4 px-6 text-[#10b981] font-bold">{{ number_format($price->amount, 2) }} ج.م
                            </td>
                            <td class="py-4 px-6 text-gray-400 text-sm">{{ $price->updated_at->format('Y/m/d') }}</td>
                            <td class="py-4 px-6 flex items-center gap-3">
                                <button
                                    onclick="editPrice('{{ $price->education_level }}', '{{ $price->circle_level }}', '{{ $price->amount }}')"
                                    class="text-blue-400 hover:text-blue-600 transition" title="تعديل">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form action="{{ route('subscription-prices.destroy', $price) }}" method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 transition"
                                        title="حذف">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">لا توجد أسعار محددة حالياً</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editPrice(edu, circle, amount) {
            document.getElementById('eduLevel').value = edu;
            document.getElementById('circleLevel').value = circle;
            document.getElementById('amount').value = amount;
            document.getElementById('priceForm').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function resetForm() {
            document.getElementById('priceForm').reset();
        }
    </script>
</x-layouts.markaz-layout>
