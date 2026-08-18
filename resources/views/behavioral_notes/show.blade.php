<x-layouts.markaz-layout>
    <x-slot name="title">تفاصيل الملاحظة السلوكية</x-slot>

    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @php
            $statusLabels = [
            'pending' => ['label' => 'قيد الانتظار', 'bg' => 'bg-amber-500'],
            'resolved' => ['label' => 'تم الحل', 'bg' => 'bg-green-500'],
            'escalated' => ['label' => 'تم التصعيد', 'bg' => 'bg-red-500'],
            'under_review' => ['label' => 'قيد المراجعة', 'bg' => 'bg-sky-500'],
            ];
            $statusInfo = $statusLabels[$behavioralNote->current_status] ?? ['label' => $behavioralNote->current_status, 'bg' => 'bg-gray-500'];
            @endphp
            <div class="{{ $statusInfo['bg'] }} px-6 py-5">
                <h3 class="text-white font-bold flex items-center gap-2">
                    <i class="fas fa-clipboard-check"></i>ملاحظة سلوكية -
                    <span class="bg-white/20 text-white px-3 py-1 rounded-full text-xs backdrop-blur">{{ $statusInfo['label'] }}</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">الطالب</span>
                        <span class="font-semibold text-gray-800">{{ $behavioralNote->student->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">الحلقة</span>
                        <span class="font-semibold text-gray-800">{{ $behavioralNote->circle->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">المعلم</span>
                        <span class="font-semibold text-gray-800">{{ $behavioralNote->teacher->user->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">تاريخ الحادثة</span>
                        <span class="font-semibold text-gray-800">{{ $behavioralNote->incident_at?->format('Y-m-d H:i') ?? '-' }}</span>
                    </div>
                    <div class="py-3">
                        <span class="text-gray-500 text-sm block mb-2">وصف السلوك</span>
                        <p class="text-gray-700 text-sm bg-gray-50 p-3 rounded-lg whitespace-pre-line">{{ $behavioralNote->behavior }}</p>
                    </div>
                    @if($behavioralNote->action_taken)
                    <div class="py-3">
                        <span class="text-gray-500 text-sm block mb-2">الإجراء المتخذ</span>
                        <p class="text-gray-700 text-sm bg-gray-50 p-3 rounded-lg whitespace-pre-line">{{ $behavioralNote->action_taken }}</p>
                    </div>
                    @endif
                    @if($behavioralNote->supervisor)
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">المشرف</span>
                        <span class="font-semibold text-gray-800">{{ $behavioralNote->supervisor->user->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">تاريخ الإجراء</span>
                        <span class="font-semibold text-gray-800">{{ $behavioralNote->action_at?->format('Y-m-d H:i') ?? '-' }}</span>
                    </div>
                    @endif
                    @if($behavioralNote->follow_up_at)
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">تاريخ المتابعة القادمة</span>
                        <span class="font-semibold text-gray-800">{{ $behavioralNote->follow_up_at->format('Y-m-d') }}</span>
                    </div>
                    @endif
                </div>
                <div class="mt-6 space-y-2">
                    @can('edit behavioral notes')
                    <a href="{{ route('behavioral-notes.edit', $behavioralNote) }}" class="block w-full bg-amber-500 hover:bg-amber-600 text-white text-center py-2.5 rounded-lg text-sm font-medium transition-colors"><i class="fas fa-edit ml-2"></i>تعديل</a>
                    @endcan
                    @can('approve behavioral notes')
                    <a href="{{ route('behavioral-notes.edit-action', $behavioralNote) }}" class="block w-full bg-sky-500 hover:bg-sky-600 text-white text-center py-2.5 rounded-lg text-sm font-medium transition-colors"><i class="fas fa-clipboard-check ml-2"></i>تسجيل الإجراء</a>
                    @endcan
                    <a href="{{ route('behavioral-notes.index') }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-2.5 rounded-lg text-sm font-medium transition-colors"><i class="fas fa-arrow-right ml-2"></i>رجوع</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.markaz-layout>