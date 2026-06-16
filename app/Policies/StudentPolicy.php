<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Traits\ResolvesUserScope;

class StudentPolicy
{
    use ResolvesUserScope;

    // ─── helper مشترك ────────────────────────────────────────────
    private function canAccessStudent(User $user, Student $student): bool
    {
        // ✅ أعلى مستوى أولاً
        if ($user->hasRole('admin')) return true;

        // ✅ general_manager يرى كل الطلاب في كل الفروع
        if ($user->hasRole('general_manager')) return true;

        // ✅ guardian يرى أبناءه فقط
        if ($user->hasRole('guardian')) {
            return $student->guardian_id === $user->id;
        }

        // باقي الأدوار تحتاج teacher record
        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        // ✅ manager — نطاق الفرع
        if ($user->hasRole('manager')) {
            if (is_null($teacher->center_id) || is_null($student->center_id)) {
                return false;
            }
            return $student->center_id === $teacher->center_id;
        }

        // ✅ supervisor — نطاق حلقاته
        if ($user->hasRole('supervisor')) {
            if (is_null($student->circle_id)) return false;
            return $this->getAccessibleCircleIds($user)
                ->contains($student->circle_id);
        }

        // ✅ teacher — نطاق حلقاته فقط
        if ($user->hasRole('teacher')) {
            if (is_null($student->circle_id)) return false;
            return $this->getAccessibleCircleIds($user)
                ->contains($student->circle_id);
        }

        // ✅ أي دور غير معروف — رفض
        return false;
    }

    // ─────────────────────────────────────────────────────────────
    public function viewAny(User $user): bool
    {
        return $user->can('view students')
            || $user->can('view own children');
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole('guardian')) {
            return $user->can('view own children')
                && $student->guardian_id === $user->id;
        }

        return $user->can('view students')
            && $this->canAccessStudent($user, $student);
    }

    public function create(User $user): bool
    {
        return $user->can('create students');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->can('edit students')
            && $this->canAccessStudent($user, $student);
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->can('delete students')
            && $this->canAccessStudent($user, $student);
    }

    public function manageStatus(User $user, Student $student): bool
    {
        return $user->can('manage student status')
            && $this->canAccessStudent($user, $student);
    }

    public function assignCircle(User $user, Student $student): bool
    {
        return $user->can('assign student to circle')
            && $this->canAccessStudent($user, $student);
    }

    public function recordPayment(User $user, Student $student): bool
    {
        // ✅ فحص الحالة أولاً قبل أي query
        if ($student->status === 'متوقف') return false;

        // ✅ تحقق من decision — وحّد القيمة مع الـ DB
        // لو DB يخزن عربي
        if ($student->decision !== 'مقبول') return false;

        return $user->can('create subscriptions')
            && $this->canAccessStudent($user, $student);
    }
}
