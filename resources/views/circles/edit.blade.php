<x-layouts.markaz-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">
                تعديل الحلقة : {{ $circle->name }}
            </h1>
            <p class="text-gray-600">قم بتعديل بيانات الحلقة التعليمية أدناه.</p>
        </div>

        <form action="{{ route('circles.update', $circle) }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                @role('admin')
                    <!-- Circle Name -->
                    <x-custom-input name="name" type="text" :value="old('name', $circle->name)" label="اسم الحلقة *" />

                    <!-- Max Students -->
                    <x-custom-input name="max_students" type="number" :value="old('max_students', $circle->max_students)" label="أقصى عدد للطلاب" />

                    <!-- Type -->
                    <x-custom-select name="type" label="نوع الحلقة *">
                        <option value="group" @selected(old('type', $circle->type) == 'group')>جماعية</option>
                        <option value="individual" @selected(old('type', $circle->type) == 'individual')>فردية</option>
                    </x-custom-select>

                    <!-- Level -->
                    <x-custom-select name="level" label="مستوى الحلقة *">
                        <option value="build" @selected(old('level', $circle->level) == 'build')>بناء</option>
                        <option value="mastery" @selected(old('level', $circle->level) == 'mastery')>إتقان</option>
                        <option value="creativity" @selected(old('level', $circle->level) == 'creativity')>إبداع</option>
                    </x-custom-select>
                @else
                    <input type="hidden" name="name" value="{{ $circle->name }}">
                    <input type="hidden" name="type" value="{{ $circle->type }}">
                    <input type="hidden" name="level" value="{{ $circle->level }}">
                    <input type="hidden" name="max_students" value="{{ $circle->max_students }}">

                    <div
                        class="md:col-span-2 bg-emerald-50 p-4 rounded-lg mb-4 text-emerald-800 font-bold border border-emerald-100 text-right">
                        تعديل الحلقة: {{ $circle->name }}
                    </div>
                @endrole
                <!-- المعلم الأساسي -->
                <x-custom-select name="teacher_id" label="اختر المعلم">
                    <option value="">اختر المعلم</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected(old('teacher_id', $circle->mainTeacher->first()?->id) == $teacher->id)>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </x-custom-select>


                <!-- المعلم المساعد -->
                <x-custom-select name="assistant_teacher_id" label="المعلم المساعد">
                    <option value="">اختر المعلم المساعد</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected(old('assistant_teacher_id', $circle->assistantTeacher->first()?->id) == $teacher->id)>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </x-custom-select>


                <!-- المشرف -->
                @role('admin')
                    <div class="md:col-span-2">
                        <x-custom-select name="supervisor_id" label="المشرف">
                            <option value="">اختر المشرف</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" @selected(old('supervisor_id', $circle->supervisor?->id) == $supervisor->id)>
                                    {{ $supervisor->name }}
                                </option>
                            @endforeach
                        </x-custom-select>
                    </div>
                @else
                    <input type="hidden" name="supervisor_id" value="{{ $circle->supervisor?->id }}">
                @endrole

                <!-- Notes -->
                @role('admin')
                    <div class="md:col-span-2">
                        <x-custom-textarea name="notes" label="ملاحظات" rows="4" :value="old('notes', $circle->notes)" />
                    </div>
                @else
                    <input type="hidden" name="notes" value="{{ $circle->notes }}">
                @endrole

                <!-- Is Active -->
                <!-- <div class="md:col-span-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"@checked(old('is_active', $circle->is_active))>

                        <div class="w-11 h-6 bg-gray-200 rounded-full relative transition peer-checked:bg-emerald-600
                                        after:content-[''] after:absolute after:top-[2px] after:right-[2px]
                                        after:bg-white after:border after:border-gray-300
                                        after:rounded-full after:h-5 after:w-5
                                        after:transition-all
                                        peer-checked:after:-translate-x-full">
                        </div>

                        <span class="mr-3 text-sm font-bold text-gray-700">
                            تنشيط الحلقة
                        </span>
                    </label>
                </div> -->
            </div>

            <div class="flex justify-end gap-4 border-t pt-6">
                <a href="{{ route('circles.index') }}" class="text-gray-600">إلغاء</a>
                <button type="submit" class="px-8 py-2 bg-[#0a5c36] text-white rounded-lg">
                    تحديث الحلقة
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>
