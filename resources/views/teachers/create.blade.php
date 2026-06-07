<x-layouts.markaz-layout>
    <div class="max-w-3xl mx-auto py-8">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-[#0a5c36]">إضافة معلم جديد</h1>
                <p class="text-gray-500 mt-2">تعيين مستخدم كمعلم أو مشرف في المركز</p>
            </div>
            <a href="{{ route('teachers.index') }}"
                class="flex items-center gap-2 text-gray-500 hover:text-[#0a5c36] transition-colors font-bold">
                <span>العودة للقائمة</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
        </div>

        <form action="{{ route('teachers.store') }}" method="POST" class="space-y-8">
            @csrf
            @include('teachers.form')
            <div class="flex items-center justify-end gap-4 mt-12">
                <a href="{{ route('teachers.index') }}"
                    class="px-8 py-3 rounded-2xl text-gray-500 hover:bg-gray-100 font-bold transition-all">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-12 py-3 bg-[#0a5c36] hover:bg-[#084d2d] text-white rounded-2xl font-black shadow-md transition-all">
                    حفظ البيانات
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>