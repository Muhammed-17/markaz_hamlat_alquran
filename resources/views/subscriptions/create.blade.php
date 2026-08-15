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
                get isSelectedStudentExempted() {
                    const student = this.students.find(s => s.id == this.selectedStudent);
                    if (!student || !this.selectedMonth) return false;
                    return student.subscriptions && student.subscriptions.some(sub =>
                        sub.month && sub.month.startsWith(this.selectedMonth) && sub.status === 'معفي'
                    );
                },

                isStudentExemptedInSelectedMonth(studentId) {
                    const student = this.students.find(s => s.id == studentId);
                    if (!student || !this.selectedMonth) return false;
                    return student.subscriptions && student.subscriptions.some(sub =>
                        sub.month && sub.month.startsWith(this.selectedMonth) && sub.status === 'معفي'
                    );
                },

                get filteredStudents() {
                    if (!this.selectedCircle) return [];
                    let filtered = this.students.filter(s => s.circle_id == this.selectedCircle);
                    if (!this.selectedMonth) return filtered;
                    return filtered.filter(s => {
                        if (s.id == this.selectedStudent) return true;
                        // ✅ لا يُستبعَد الطالب إلا لو عنده اشتراك "مدفوع" فعليًا لنفس الشهر.
                        // الطالب المعفي (status = 'معفي') يبقى ظاهرًا للسماح بتسجيل دفعة استثنائية له.
                        const hasPaidSub = s.subscriptions && s.subscriptions.some(sub =>
                            sub.month && sub.month.startsWith(this.selectedMonth) && sub.status === 'مدفوع'
                        );
                        return !hasPaidSub;
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

                pushStudentOptions() {
                    const options = this.filteredStudents.map(s => ({
                        value: s.id,
                        label: this.isStudentExemptedInSelectedMonth(s.id)
                            ? s.name + ' (معفي هذا الشهر ⚠️)'
                            : s.name
                    }));
                    window.dispatchEvent(new CustomEvent('update-options', {
                        detail: { name: 'student_id', options }
                    }));
                },

                init() {
                    window.addEventListener('searchable-change', (e) => {
                        const {
                            name,
                            value
                        } = e.detail;

                        if (name === 'circle_id') {
                            this.selectedCircle = value;
                            this.selectedStudent = '';

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

    @include('subscriptions.form', ['isLockedByConfirmedRound' => false])

</x-layouts.markaz-layout>