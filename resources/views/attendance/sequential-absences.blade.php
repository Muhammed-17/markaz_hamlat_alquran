<x-layouts.markaz-layout>
    <script>
        window.__students = @json($students);
        window.__csrf = '{{ csrf_token() }}';
        window.__notifyUrl = '{{ route('attendance.sequential-absences.notify', '__ID__') }}';
        window.__toggleUrl = '{{ route('attendance.sequential-absences.toggle-contact', '__ID__') }}';
    </script>
    <div class="max-w-6xl mx-auto"
        x-data="{
            sortField: 'absence_days',
            sortAsc: false,
            students: window.__students,
            csrfToken: window.__csrf,
            notifyUrl: window.__notifyUrl,
            toggleUrl: window.__toggleUrl,
            sendingIds: [],
            togglingIds: [],
            toastMessage: '',
            toastType: 'success',
            toastShow: false,
            toastTimeout: null,
            showMessageModal: false,
            selectedStudentId: null,
            selectedStudentName: '',
            customMessage: '',
            sortBy(field) {
                if (this.sortField === field) {
                    this.sortAsc = !this.sortAsc;
                } else {
                    this.sortField = field;
                    this.sortAsc = field === 'name';
                }
                this.sortStudents();
            },
            sortStudents() {
                this.students = [...this.students].sort((a, b) => {
                    if (this.sortField === 'name') {
                        return this.sortAsc
                            ? a.name.localeCompare(b.name)
                            : b.name.localeCompare(a.name);
                    }
                    if (this.sortField === 'is_guardian_contacted') {
                        return this.sortAsc
                            ? (a.is_guardian_contacted === b.is_guardian_contacted ? 0 : a.is_guardian_contacted ? 1 : -1)
                            : (a.is_guardian_contacted === b.is_guardian_contacted ? 0 : b.is_guardian_contacted ? 1 : -1);
                    }
                    return this.sortAsc
                         ? a.absence_days - b.absence_days
                         : b.absence_days - a.absence_days;
                 });
             },
             showToast(message, type) {
                 if (this.toastTimeout) clearTimeout(this.toastTimeout);
                 this.toastMessage = message;
                 this.toastType = type;
                 this.toastShow = true;
                 this.toastTimeout = setTimeout(() => { this.toastShow = false; }, 4000);
             },
             hideToast() {
                 this.toastShow = false;
                 if (this.toastTimeout) clearTimeout(this.toastTimeout);
             },
             openNotifyModal(studentId) {
                 const student = this.students.find(s => s.id === studentId);
                 if (!student) return;
                 this.selectedStudentId = studentId;
                 this.selectedStudentName = student.name;
                 this.customMessage = 'تم رصد غياب متتالٍ لابنكم ' + student.name + ' لمدة ' + student.absence_days + ' أيام. يرجى التواصل مع المشرف.';
                 this.showMessageModal = true;
             },
             closeNotifyModal() {
                 this.showMessageModal = false;
                 this.selectedStudentId = null;
                 this.selectedStudentName = '';
                 this.customMessage = '';
             },
             async confirmNotify() {
                 const studentId = this.selectedStudentId;
                 if (!studentId || this.sendingIds.includes(studentId)) return;
                 this.sendingIds.push(studentId);
                 this.closeNotifyModal();
                 try {
                      const res = await fetch(this.notifyUrl.replace('__ID__', studentId), {
                         method: 'POST',
                         headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                         body: JSON.stringify({ message: this.customMessage }),
                     });
                     const data = await res.json();
                     if (res.ok) {
                         this.showToast(data.message, 'success');
                     } else {
                         this.showToast(data.message, 'error');
                     }
                 } catch {
                     this.showToast('حدث خطأ أثناء إرسال التنبيه.', 'error');
                 } finally {
                     this.sendingIds = this.sendingIds.filter(id => id !== studentId);
                 }
             },
             async toggleContact(studentId) {
                 if (this.togglingIds.includes(studentId)) return;
                 this.togglingIds.push(studentId);
                 try {
                      const res = await fetch(this.toggleUrl.replace('__ID__', studentId), {
                         method: 'POST',
                         headers: { 'X-CSRF-TOKEN': this.csrfToken, 'X-HTTP-Method-Override': 'PATCH', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                     });
                     const data = await res.json();
                     if (res.ok) {
                         this.showToast(data.message, 'success');
                     } else {
                         this.showToast(data.message, 'error');
                     }
                     const student = this.students.find(s => s.id === studentId);
                     if (student && data.is_guardian_contacted !== undefined) {
                         student.is_guardian_contacted = data.is_guardian_contacted;
                     }
                 } catch {
                     this.showToast('حدث خطأ أثناء تحديث حالة التواصل.', 'error');
                 } finally {
                     this.togglingIds = this.togglingIds.filter(id => id !== studentId);
                 }
             }
         }"
        x-init="sortStudents()">

        <!-- Toast Notification -->
        <div x-show="toastShow"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @click="hideToast()"
            class="mb-4 px-4 py-3 rounded-xl shadow-lg border cursor-pointer flex items-center gap-2 text-sm font-medium"
            :class="toastType === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-800 border-red-200'">
            <template x-if="toastType === 'success'">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </template>
            <template x-if="toastType === 'error'">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </template>
            <span x-text="toastMessage"></span>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[#0a5c36]">الطلاب الأكثر غياباً</h1>
                <p class="text-gray-500 text-sm">الطلاب الذين تم رصد غيابهم بشكل متتالٍ (غياب يومين متتاليين أو أكثر)</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="text-sm text-blue-600 hover:text-blue-800 transition">&larr; العودة إلى لوحة التحكم</a>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <span class="block text-2xl font-bold text-gray-800">{{ $students->count() }}</span>
                <span class="text-sm text-gray-500">طالب متطابق مع نمط الغياب المتتالي</span>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <span class="block text-2xl font-bold text-red-600">{{ $students->sum('absence_days') }}</span>
                <span class="text-sm text-gray-500">إجمالي أيام الغياب</span>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <span class="block text-2xl font-bold text-emerald-600">{{ $students->avg('absence_days') ? number_format($students->avg('absence_days'), 1) : 0 }}</span>
                <span class="text-sm text-gray-500">متوسط أيام الغياب لكل طالب</span>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-right px-4 py-3 font-bold text-gray-600">#</th>
                            <th @click="sortBy('name')"
                                class="text-right px-4 py-3 font-bold text-gray-600 cursor-pointer select-none hover:text-gray-800">
                                اسم الطالب
                                <span x-show="sortField === 'name'" x-text="sortAsc ? '↑' : '↓'" class="mr-1"></span>
                            </th>
                            <th class="text-right px-4 py-3 font-bold text-gray-600">الحلقة</th>
                            <th class="text-right px-4 py-3 font-bold text-gray-600">المشرف</th>
                            <th @click="sortBy('absence_days')"
                                class="text-center px-4 py-3 font-bold text-gray-600 cursor-pointer select-none hover:text-gray-800">
                                أيام الغياب
                                <span x-show="sortField === 'absence_days'" x-text="sortAsc ? '↑' : '↓'" class="mr-1"></span>
                            </th>
                            <th class="text-center px-4 py-3 font-bold text-gray-600">إرسال تنبيه</th>
                            <th @click="sortBy('is_guardian_contacted')"
                                class="text-center px-4 py-3 font-bold text-gray-600 cursor-pointer select-none hover:text-gray-800">
                                حالة التواصل
                                <span x-show="sortField === 'is_guardian_contacted'" x-text="sortAsc ? '↑' : '↓'" class="mr-1"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(student, index) in students" :key="student.id">
                            <tr :class="[
                                index % 2 === 0 ? 'bg-white' : 'bg-gray-50/30',
                                student.is_guardian_contacted ? 'opacity-60' : 'hover:bg-red-50/50 transition'
                            ]">
                                <td class="px-4 py-3 text-gray-500" x-text="index + 1"></td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-800" x-text="student.name"></span>
                                </td>
                                <td class="px-4 py-3 text-gray-600" x-text="student.circle?.name || '—'"></td>
                                <td class="px-4 py-3 text-gray-600" x-text="student.circle?.supervisor?.name || student.circle?.main_teacher?.name || '—'"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full font-bold" x-text="student.absence_days"></span>
                                </td>
                                <!-- <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-xs px-3 py-1 rounded-full border border-red-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                        نمط متتال
                                    </span>
                                </td> -->
                                <td class="px-4 py-3 text-center">
                                    <button @click="openNotifyModal(student.id)"
                                        :disabled="sendingIds.includes(student.id)"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
                                        :class="sendingIds.includes(student.id) ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-[#0a5c36] hover:bg-[#084b2c] text-white'">
                                        <span x-show="!sendingIds.includes(student.id)">إرسال</span>
                                        <span x-show="sendingIds.includes(student.id)" class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                            </svg>
                                            جاري
                                        </span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="toggleContact(student.id)"
                                        :disabled="togglingIds.includes(student.id)"
                                        class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors duration-200 focus:outline-none"
                                        :class="student.is_guardian_contacted ? 'bg-emerald-500' : 'bg-gray-200'">
                                        <span class="inline-block w-4 h-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                                            :class="student.is_guardian_contacted ? 'translate-x-6' : 'translate-x-1'"></span>
                                    </button>
                                    <span class="block text-xs mt-1"
                                        :class="student.is_guardian_contacted ? 'text-emerald-600' : 'text-gray-400'"
                                        x-text="student.is_guardian_contacted ? 'تم التواصل' : 'لم يتم'"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="students.length === 0">
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="text-gray-400 mb-2">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500">لا يوجد طلاب متطابقين مع نمط الغياب المتتالي حالياً.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer info -->
        @if($students->isNotEmpty())
        <p class="text-xs text-gray-400 mt-4 text-center">
            * يتم احتساب الغياب المتتالي بناءً على آخر ٣٠ سجل حضور لكل طالب.
            النمط المتتالي يشمل: غياب يومين متتاليين، أو غياب ← حضور ← غياب.
        </p>
        @endif
        <!-- Message Modal -->
        <div x-show="showMessageModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            @click.self="closeNotifyModal()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">إرسال تنبيه</h3>
                    <button @click="closeNotifyModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-500 mb-4">
                    كتابة رسالة تنبيه لولي أمر الطالب: <span class="font-medium text-gray-700" x-text="selectedStudentName"></span>
                </p>
                <textarea x-model="customMessage"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 resize-none focus:outline-none focus:ring-2 focus:ring-[#0a5c36]/20 focus:border-[#0a5c36] transition"
                    rows="4"
                    placeholder="اكتب رسالة التنبيه..."></textarea>
                <div class="flex justify-end gap-3 mt-4">
                    <button @click="closeNotifyModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                        إلغاء
                    </button>
                    <button @click="confirmNotify()"
                        :disabled="sendingIds.includes(selectedStudentId)"
                        class="px-5 py-2 text-sm font-bold text-white bg-[#0a5c36] hover:bg-[#084b2c] rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
                        <span x-show="!sendingIds.includes(selectedStudentId)">إرسال التنبيه</span>
                        <span x-show="sendingIds.includes(selectedStudentId)" class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            جاري
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.markaz-layout>