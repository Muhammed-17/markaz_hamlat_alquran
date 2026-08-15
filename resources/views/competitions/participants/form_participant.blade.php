@php
$isEdit = isset($participant) && $participant !== null;
$initialParticipantType = $isEdit ? $participant->participant_type : 'student';
$initialCircleId = $isEdit ? ($participant->circle_id ?? '') : '';
$initialStudentId = $isEdit ? $participant->student_id : null;
$initialExternalId = $isEdit ? $participant->external_participant_id : null;
$initialExternalText = $isEdit && $participant->isExternal()
    ? trim(($participant->externalParticipant->name ?? '') .
        ($participant->externalParticipant->national_id ? ' - ' . $participant->externalParticipant->national_id : ''))
    : '';
$initialFee = $isEdit ? $participant->registration_fee : 0;
$initialFileStatus = $isEdit ? (string) $participant->file_status : (string) \App\Models\CompetitionParticipant::FILE_NOT_REQUIRED;
$initialSupervisorId = $isEdit ? $participant->supervisor_id : null;
$initialCenterId = $isEdit ? $participant->center_id : ($competition->center_id ?? null);
$initialLevelId = $isEdit ? $participant->competition_level_id : null;
$initialTafsirFileId = $isEdit ? $participant->tafsir_file_id : null;

$levelOptions = $levels->map(fn($competitionLevel) => [
'value' => $competitionLevel->id,
'label' => $competitionLevel->level->name ?? '-',
])->values()->toArray();

$centerOptions = $centers->map(fn($center) => [
'value' => $center->id,
'label' => $center->name,
])->values()->toArray();

$circleOptions = collect($circles ?? [])->map(fn($circle) => [
'value' => $circle->id,
'label' => $circle->name,
])->values()->toArray();

$fileStatusOptions = [
['value' => \App\Models\CompetitionParticipant::FILE_NOT_REQUIRED, 'label' => 'غير مطلوب'],
['value' => \App\Models\CompetitionParticipant::FILE_NOT_RECEIVED, 'label' => 'لم يتم الاستلام'],
['value' => \App\Models\CompetitionParticipant::FILE_RECEIVED, 'label' => 'تم الاستلام'],
];

$tafsirFileOptions = $tafsirFiles->map(fn($tafsirFile) => [
'value' => $tafsirFile->id,
'label' => $tafsirFile->name,
])->values()->toArray();

$supervisorOptions = $supervisors->map(fn($supervisor) => [
'value' => $supervisor->id,
'label' => $supervisor->name,
])->values()->toArray();
@endphp

<div class="max-w-3xl mx-auto space-y-6"
    x-data="{
        participantType: '{{ $initialParticipantType }}',
        circleId: '{{ $initialCircleId }}',
        registrationFee: {{ (int) $initialFee }},
        fileStatus: '{{ $initialFileStatus }}',
        externalSelectedId: {{ $initialExternalId ?? 'null' }},
        externalSelectedText: {{ Illuminate\Support\Js::from($initialExternalText) }},
        externalSearch: {{ Illuminate\Support\Js::from($initialExternalText) }},
        externalResults: [],
        externalOpen: false,
        externalLoading: false,
        studentInitialOptions: {{ Illuminate\Support\Js::from($studentOptions ?? []) }},
        async loadStudents(preserveSelection = false) {
            if (!this.circleId) {
                window.dispatchEvent(new CustomEvent('update-options', {
                    detail: { name: 'student_id', options: [] }
                }));
                return;
            }

            const url = `{{ route('competitions.participants.search-students') }}`;
            const params = new URLSearchParams({
                circle_id: this.circleId,
                competition_id: {{ $competition->id }},
                @if($isEdit)
                exclude_participant_id: {{ $participant->id }},
                @endif
            });

            const res = await fetch(`${url}?${params.toString()}`);
            const data = await res.json();

            window.dispatchEvent(new CustomEvent('update-options', {
                detail: { name: 'student_id', options: data }
            }));
        },
        resetCircle() {
            this.circleId = '';
            window.dispatchEvent(new CustomEvent('clear-selection', { detail: { name: 'circle_id' } }));
            window.dispatchEvent(new CustomEvent('update-options', {
                detail: { name: 'student_id', options: [] }
            }));
        },
        async doExternalSearch() {
            if (this.externalSearch.length < 2) { this.externalResults = []; return; }

            this.externalLoading = true;

            const url = `{{ route('competitions.participants.search-external') }}`;
            const params = new URLSearchParams({
                q: this.externalSearch,
                competition_id: {{ $competition->id }},
                @if($isEdit)
                exclude_participant_id: {{ $participant->id }},
                @endif
            });

            try {
                const res = await fetch(`${url}?${params.toString()}`);
                this.externalResults = await res.json();
                this.externalOpen = true;
            } finally {
                this.externalLoading = false;
            }
        },
        selectExternal(item) {
            this.externalSelectedId = item.id;
            this.externalSearch = item.national_id ? `${item.name} - ${item.national_id}` : item.name;
            this.externalOpen = false;
        },
        resetExternalSelection() {
            this.externalSelectedId = null;
            this.externalSelectedText = '';
            this.externalSearch = '';
            this.externalResults = [];
        },
        init() {
            // بحالة التعديل: لو المشارك طالب، حمّل قائمة طلاب الحلقة المحددة سلفاً
            // وحط الخيار المختار جوه x-searchable-select عن طريق نفس حدث update-options
            if (this.participantType === 'student' && this.circleId && this.studentInitialOptions.length) {
                window.dispatchEvent(new CustomEvent('update-options', {
                    detail: { name: 'student_id', options: this.studentInitialOptions }
                }));
            }

            // ✅ ربط تغييرات searchable-select بحقل circle_id بمتغير circleId المحلي
            window.addEventListener('searchable-change', (e) => {
                if (e.detail.name === 'circle_id') {
                    this.circleId = e.detail.value;
                    this.loadStudents();
                }
                if (e.detail.name === 'file_status') {
                    this.fileStatus = e.detail.value;
                }
            });
        }
    }">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">{{ $isEdit ? 'تعديل مشارك' : 'إضافة مشارك' }}</h1>
        <p class="text-gray-600">{{ $competition->name }}</p>
    </div>

    <form action="{{ $formAction }}" method="POST">
        @csrf
        @if ($formMethod === 'PUT')
        @method('PUT')
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="flex items-center gap-3 mb-2 border-b border-gray-50 pb-4">
                <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">بيانات المشارك</h2>
            </div>

            {{-- المستوى --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">المستوى</label>
                <x-searchable-select
                    name="competition_level_id"
                    :options="$levelOptions"
                    :default-value="$initialLevelId"
                    placeholder="اختر المستوى..."
                    search-placeholder="ابحث عن مستوى..." />
                @error('competition_level_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- المركز --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">المركز</label>
                <x-searchable-select
                    name="center_id"
                    :options="$centerOptions"
                    :default-value="$initialCenterId"
                    placeholder="اختر المركز..."
                    search-placeholder="ابحث عن مركز..." />
                @error('center_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">نوع المشارك</label>
                <div class="flex gap-3">
                    <label class="flex-1 flex items-center justify-center gap-2 rounded-2xl border px-4 py-3 cursor-pointer transition"
                        :class="participantType === 'student' ? 'bg-emerald-50 border-[#0a5c36] text-[#0a5c36]' : 'border-gray-200 text-gray-600'">
                        <input type="radio" name="participant_type" value="student" x-model="participantType" @change="resetCircle(); resetExternalSelection()" class="hidden">
                        طالب
                    </label>
                    <label class="flex-1 flex items-center justify-center gap-2 rounded-2xl border px-4 py-3 cursor-pointer transition"
                        :class="participantType === 'external' ? 'bg-emerald-50 border-[#0a5c36] text-[#0a5c36]' : 'border-gray-200 text-gray-600'">
                        <input type="radio" name="participant_type" value="external" x-model="participantType" @change="resetCircle(); resetExternalSelection()" class="hidden">
                        خارجي
                    </label>
                </div>
                @error('participant_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- حقل الحلقة: يظهر فقط مع نوع "طالب" --}}
            <div class="space-y-2" x-show="participantType === 'student'" x-cloak>
                <label class="block text-sm font-bold text-gray-700">الحلقة</label>
                <x-searchable-select
                    name="circle_id"
                    :options="$circleOptions"
                    :default-value="$initialCircleId"
                    placeholder="اختر الحلقة..."
                    search-placeholder="ابحث عن حلقة..." />
                @error('circle_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- اختيار الطالب: select قابل للبحث --}}
            <div class="space-y-2" x-show="participantType === 'student' && circleId" x-cloak>
                <label class="block text-sm font-bold text-gray-700">الطالب</label>
                <x-searchable-select
                    name="student_id"
                    :options="$studentOptions ?? []"
                    :default-value="$initialStudentId"
                    placeholder="اختر الطالب..."
                    search-placeholder="ابحث بالاسم..." />
                @error('student_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- بحث عن مشارك خارجي --}}
            <div class="relative space-y-2" x-show="participantType === 'external'" x-cloak>
                <label class="block text-sm font-bold text-gray-700">بحث عن مشارك خارجي</label>
                <div class="relative">
                    <svg x-show="!externalLoading" class="absolute top-1/2 -translate-y-1/2 left-4 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <svg x-show="externalLoading" style="display: none;" class="absolute top-1/2 -translate-y-1/2 left-4 w-5 h-5 text-[#0a5c36] animate-spin pointer-events-none" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <input type="text" x-model="externalSearch" @input.debounce.300ms="doExternalSearch()"
                        @focus="externalSearch = ''; externalSelectedId = null"
                        placeholder="ابحث بالاسم أو الرقم القومي..."
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
                </div>
                <input type="hidden" name="external_participant_id" :value="externalSelectedId">
                @error('external_participant_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <div x-show="externalOpen" x-transition style="display: none;"
                    class="absolute z-20 mt-1 w-full bg-white rounded-2xl shadow-lg border border-gray-100 max-h-60 overflow-y-auto">
                    <template x-for="item in externalResults" :key="item.id">
                        <button type="button" @click="selectExternal(item)"
                            class="w-full text-right px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center justify-between gap-2">
                            <span x-text="item.name"></span>
                            <span class="text-xs text-gray-400" x-text="item.national_id"></span>
                        </button>
                    </template>
                    <p x-show="externalResults.length === 0" class="px-4 py-2.5 text-sm text-gray-400">لا توجد نتائج</p>
                </div>
            </div>

            {{-- حالة الملف --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">حالة الملف</label>
                <x-searchable-select
                    name="file_status"
                    :options="$fileStatusOptions"
                    :default-value="$initialFileStatus"
                    placeholder="اختر حالة الملف..."
                    search-placeholder="ابحث..." />
                @error('file_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- ملف التفسير --}}
            <div class="space-y-2"
                x-show="String(fileStatus) !== '{{ \App\Models\CompetitionParticipant::FILE_NOT_REQUIRED }}'"
                x-cloak>
                <label class="block text-sm font-bold text-gray-700">ملف التفسير</label>
                <x-searchable-select
                    name="tafsir_file_id"
                    :options="$tafsirFileOptions"
                    :default-value="$initialTafsirFileId"
                    placeholder="اختر ملف التفسير..."
                    search-placeholder="ابحث عن ملف..." />
                @error('tafsir_file_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- الرسوم --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">الرسوم</label>
                <input type="number" name="registration_fee" min="0" step="1"
                    x-model="registrationFee"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
                @error('registration_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- المشرف المسؤول --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">المشرف المسؤول</label>
                <x-searchable-select
                    name="supervisor_id"
                    :options="$supervisorOptions"
                    :default-value="$initialSupervisorId"
                    placeholder="بدون تحديد"
                    search-placeholder="ابحث عن مشرف..." />
                @error('supervisor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-4 border-t pt-6 mt-6">
            <a href="{{ route('competitions.participants', $competition) }}"
                class="px-6 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition-all font-bold">
                إلغاء
            </a>
            <button type="submit"
                class="px-8 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d] text-white rounded-xl font-bold transition-all shadow-md">
                {{ $isEdit ? 'حفظ التعديلات' : 'حفظ المشارك' }}
            </button>
        </div>
    </form>
</div>