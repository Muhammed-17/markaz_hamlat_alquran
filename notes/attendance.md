@extends('layouts.app')

@section('content')
<div class="container">
    <h2>تسجيل الحضور والغياب</h2>

    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="date">اختر التاريخ:</label>
                <input type="date" name="date" id="date" class="form-control" 
                       value="{{ $date }}" onchange="this.form.submit()">
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>اسم الطالب</th>
                    <th>الحالة</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            @foreach(['حاضر', 'غائب', 'متأخر', 'أجازة'] as $status)
                                @php
                                    $isSelected = ($attendanceMap[$student->id] ?? 'حاضر') === $status;
                                @endphp
                                <button type="button"
                                        class="btn btn-sm {{ $isSelected ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        onclick="setAttendance({{ $student->id }}, '{{ $status }}')">
                                    {{ $status }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="statuses[{{ $student->id }}]" 
                               id="status_{{ $student->id }}" 
                               value="{{ $attendanceMap[$student->id] ?? 'حاضر' }}">
                    </td>
                    <td>
                        <input type="text" 
                               name="notes[{{ $student->id }}]" 
                               class="form-control form-control-sm"
                               placeholder="سبب الغياب..."
                               value="{{ $notesMap[$student->id] ?? '' }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-success">حفظ السجل</button>
    </form>
</div>

<script>
function setAttendance(studentId, status) {
    document.getElementById(`status_${studentId}`).value = status;
    // تحديث مظهر الزر (اختياري)
    document.querySelectorAll(`[onclick*="setAttendance(${studentId},"]`).forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-secondary');
    });
    event.target.classList.remove('btn-outline-secondary');
    event.target.classList.add('btn-primary');
}
</script>
@endsection