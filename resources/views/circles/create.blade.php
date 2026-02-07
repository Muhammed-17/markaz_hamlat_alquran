<x-layouts.markaz-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">إضافة حلقة جديدة</h1>
            <p class="text-gray-600">قم بتعبئة البيانات أدناه لإنشاء حلقة تعليمية جديدة في المركز.</p>
        </div>

        <form action="{{ route('circles.store') }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                <!-- Circle Name -->
                <x-custom-input name="name" type="text" value="{{ old('name') }}" label="اسم الحلقة *" />


                <!-- Max Students -->
                <x-custom-input name="max_students" type="number" value="{{ old('max_students',20) }}" label="أقصى عدد للطلاب" />
                
                <!-- Type -->
                <x-custom-select name="type" label="نوع الحلقة *">
                    <option value="group">جماعية</option>
                    <option value="individual">فردية</option>
                </x-custom-select>

                <!-- Level -->
                <x-custom-select name="level" label="مستوى الحلقة *">
                    <option value="build">بناء</option>
                    <option value="mastery">إتقان</option>
                    <option value="creativity">إبداع</option>
                </x-custom-select>

                <!-- المعلم -->
                <x-custom-select name="teacher_id" label="اختر المعلم">
                    <option value="">اختر المعلم</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </x-custom-select>

                <!-- المعلم المساعد -->
                <x-custom-select name="assistant_teacher_id" label="المعلم المساعد">
                    <option value="">اختر المعلم المساعد</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </x-custom-select>


                <!-- المشرف -->
                <div class="md:col-span-2">
                    <x-custom-select name="supervisor_id" label="المشرف">
                        <option value="">اختر المشرف</option>
                        @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                        @endforeach
                    </x-custom-select>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <x-custom-textarea name="notes" label="ملاحظات" rows="4">{{ old('notes') }}</x-custom-textarea>
                </div>

            </div>

            <div class="flex justify-end gap-4 border-t pt-6">
                <a href="{{ route('circles.index') }}" class="text-gray-600">إلغاء</a>
                <button type="submit"
                    class="px-8 py-2 bg-[#0a5c36] text-white rounded-lg">
                    حفظ الحلقة
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>