<x-layouts.markaz-layout>
    <div class="max-w-4xl mx-auto py-8">

        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-[#0a5c36]">إضافة طالب جديد</h1>
                <p class="text-gray-500 mt-2">تسجيل طالب جديد في حلقات مركز حملة القرآن</p>
            </div>
            <a href="{{ route('students.index') }}"
                class="flex items-center gap-2 text-gray-500 hover:text-[#0a5c36] transition-colors font-bold">
                <span>العودة للقائمة</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
        </div>

        <form action="{{ route('students.store') }}" method="POST" novalidate id="registrationForm">
            @csrf
            @include('students.form')
        </form>

    </div>
</x-layouts.markaz-layout>