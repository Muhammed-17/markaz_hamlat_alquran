<!-- resources/views/students/create.blade.php -->

<form method="POST" action="{{ route('students.store') }}">
    @csrf

    <!-- بيانات الطالب -->
    <input name="student[name]" placeholder="اسم الطالب" required>
    <select name="student[education_level]" required>
        <option value="primary">ابتدائي</option>
        <option value="preparatory">إعدادي</option>
        <option value="secondary">ثانوي</option>
    </select>

    <!-- اختيار ولي أمر -->
    <label>هل ولي الأمر مسجّل مسبقًا؟</label>
    <select name="guardian_id">
        <option value="">-- اختر ولي أمر --</option>
        @foreach($existingGuardians as $guardian)
            <option value="{{ $guardian->id }}">{{ $guardian->name }} ({{ $guardian->email }})</option>
        @endforeach
        <option value="new">+ إضافة ولي أمر جديد</option>
    </select>

    <!-- حقول ولي الأمر الجديد (تظهر عند اختيار "جديد") -->
    <div id="new-guardian-fields" style="display:none;">
        <input name="guardian[name]" placeholder="اسم ولي الأمر">
        <input name="guardian[email]" type="email" placeholder="بريد ولي الأمر">
    </div>

    <button type="submit">حفظ الطالب</button>
</form>

<script>
    document.querySelector('select[name="guardian_id"]').addEventListener('change', function() {
        const newFields = document.getElementById('new-guardian-fields');
        newFields.style.display = this.value === 'new' ? 'block' : 'none';
    });
</script>