<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentPolicy
{
    // ─── helper مشترك ────────────────────────────────────────────
    private function canAccessStudent(User $user, Student $student): bool
    {
        // admin → بلا قيود
        if ($user->hasRole('admin')) return true;

        // guardian → أولاده فقط
        if ($user->hasRole('guardian')) {
            return $student->guardian_id === $user->id;
        }

        $teacher = DB::table('teachers')
            ->where('user_id', $user->id)
            ->first();

        if (!$teacher) return false;

        // manager → طلاب فرعه فقط
        if ($user->hasRole('manager')) {
            return $student->center_id === $teacher->center_id;
        }

        // supervisor → طلاب حلقاته التي يشرف عليها
        if ($user->hasRole('supervisor')) {
            $circleIds = DB::table('circles')
                ->where('supervisor_id', $teacher->id)
                ->where('center_id', $teacher->center_id)
                ->pluck('id');

            return $circleIds->contains($student->circle_id);
        }

        // teacher → طلاب حلقاته في فرعه
        if ($user->hasRole('teacher')) {
            $circleIds = DB::table('circle_teacher')
                ->where('teacher_id', $teacher->id)
                ->pluck('circle_id');

            $circleIds = DB::table('circles')
                ->whereIn('id', $circleIds)
                ->where('center_id', $teacher->center_id)
                ->pluck('id');

            return $circleIds->contains($student->circle_id);
        }

        return false;
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