<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Traits\ResolvesUserScope;
use Illuminate\Support\Facades\DB;

class StudentPolicy
{
    use ResolvesUserScope;

    // ─── helper مشترك ────────────────────────────────────────────
    private function canAccessStudent(User $user, Student $student): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('guardian')) {
            return $student->guardian_id === $user->id;
        }

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        if ($user->hasRole('manager')) {
            return $student->center_id === $teacher->center_id;
        }

        $circleIds = $this->getAccessibleCircleIds($user);
        return $circleIds->contains($student->circle_id);
    }

    // ─────────────────────────────────────────────────────────────
    public function viewAny(User $user): bool
    {
        return $user->can('view students') || $user->can('view own children');
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
        if ($student->status === 'inactive') return false;

        return $user->can('collect subscription')
            && $this->canAccessStudent($user, $student);
    }
}
