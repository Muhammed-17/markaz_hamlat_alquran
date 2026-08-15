<x-layouts.markaz-layout>
    <x-slot name="title">تفاصيل جلسة المجموعة</x-slot>

    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-blue-500 px-6 py-5">
                <h3 class="text-white font-bold flex items-center gap-2"><i class="fas fa-users"></i>تفاصيل الجلسة</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">الخطة الأسبوعية</span>
                        <span class="font-semibold text-gray-800">{{ $session->weeklyPlan->week_start ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">الحلقة</span>
                        <span class="font-semibold text-gray-800">{{ $session->weeklyPlan->circle->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">الوقت</span>
                        <span class="font-semibold text-gray-800 flex items-center gap-2"><i class="fas fa-clock text-blue-500"></i>{{ $session->start_time }} - {{ $session->end_time }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">المكان</span>
                        <span class="font-semibold text-gray-800">{{ $session->location ?? 'غير محدد' }}</span>
                    </div>
                    @if($session->topic)
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">الموضوع</span>
                        <span class="font-semibold text-gray-800">{{ $session->topic }}</span>
                    </div>
                    @endif
                    @if($session->description)
                    <div class="py-3">
                        <span class="text-gray-500 text-sm block mb-2">الوصف</span>
                        <p class="text-gray-700 text-sm bg-gray-50 p-3 rounded-lg">{{ $session->description }}</p>
                    </div>
                    @endif
                </div>
                <div class="mt-6 space-y-2">
                    @can('update', $session)
                    <a href="{{ route('group-session-plans.edit', $session) }}" class="block w-full bg-amber-500 hover:bg-amber-600 text-white text-center py-2.5 rounded-lg text-sm font-medium transition-colors"><i class="fas fa-edit ml-2"></i>تعديل</a>
                    @endcan
                    <a href="{{ route('student-weekly-followups.show', $session->weekly_plan_id) }}" class="block w-full bg-[#0a5c36] hover:bg-[#0d7a48] text-white text-center py-2.5 rounded-lg text-sm font-medium transition-colors"><i class="fas fa-arrow-right ml-2"></i>العودة للمتابعة</a>
                </div>
            </div>
        </div>
    </div>
    </x-app-layout>