<x-layouts.markaz-layout>

    <script>
function formData() {
    return {
        selectedCircle: '{{ old('circle_id', request('circle_id')) }}',
        selectedStudent: '{{ old('student_id', request('student_id')) }}',
        selectedMonth: '{{ old('month', request('month') ?? date('Y-m')) }}',
        amount: {{ old('amount', 60) }},
        status: '{{ old('status', 'مدفوع') }}',
        isSubmitting: false,
        students: {{ Js::from($students) }},
        circles: {{ Js::from($circles) }},
        prices: {{ Js::from($prices) }},

        get filteredStudents() {
            if (!this.selectedCircle) return [];
            let filtered = this.students.filter(s => s.circle_id == this.selectedCircle);
            if (!this.selectedMonth) return filtered;
            return filtered.filter(s => {
                if (s.id == this.selectedStudent) return true;
                const hasSub = s.subscriptions && s.subscriptions.some(sub =>
                    sub.month && sub.month.startsWith(this.selectedMonth)
                );
                return !hasSub;
            });
        },

        updateDefaultAmount() {
            const student = this.students.find(s => s.id == this.selectedStudent);
            if (student) {
                const eduStage = student.educational_stage ?? '';
                const circleLevel = student.circle?.level ?? '';
                const priceRule = this.prices.find(p =>
                    p.education_stage == eduStage &&
                    p.circle_level == circleLevel
                );
                this.amount = priceRule ? priceRule.amount : 60;
            } else {
                this.amount = 60;
            }
            this.applyStatusRules();
        },

        // ✅ دي الدالة الجديدة اللي بتبعت options للـ student select
        pushStudentOptions() {
            const options = this.filteredStudents.map(s => ({
                value: s.id,
                label: s.name
            }));
            window.dispatchEvent(new CustomEvent('update-options', {
                detail: { name: 'student_id', options }
            }));
        },

init() {
    window.addEventListener('searchable-change', (e) => {
        const { name, value } = e.detail;

        if (name === 'circle_id') {
            this.selectedCircle = value;  // ✅ اتغير أولاً
            this.selectedStudent = '';

            // ✅ nextTick واحد بس بعد ما selectedCircle اتضبط
            this.$nextTick(() => {
                this.pushStudentOptions();
                this.updateDefaultAmount();
            });
        }

        if (name === 'student_id') {
            this.selectedStudent = value;
            this.updateDefaultAmount();
        }
    });

    if (this.selectedCircle) {
        this.$nextTick(() => {
            this.pushStudentOptions();
            if (this.selectedStudent) {
                this.updateDefaultAmount();
            }
        });
    }

    this.$watch('selectedMonth', () => {
        // ✅ متمسحش الطالب لو لسه موجود في الفلتر الجديد
        this.$nextTick(() => {
            this.pushStudentOptions();
        });
    });

    this.$watch('status', () => this.applyStatusRules());
    this.applyStatusRules();
},

        applyStatusRules() {
            if (this.status === 'معفي') {
                this.amount = 0;
            } else if (this.amount == 0) {
                this.updateDefaultAmount();
            }
        }
    }
}
    </script>

    @include('subscription.form')
    
</x-layouts.markaz-layout>