<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class CenterScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (!$user || $user->hasRole('admin')) return;

        $table = $model->getTable();

        // ─── manager ─────────────────────────────────────────────
        if ($user->hasRole('manager')) {
            $teacher = DB::table('teachers')
                ->where('user_id', $user->id)
                ->first();

            if (!$teacher) {
                $builder->whereRaw('1 = 0');
                return;
            }

            match (true) {
                in_array($table, ['circles', 'students', 'teachers', 'subscriptions'])
                    => $builder->where("{$table}.center_id", $teacher->center_id),

                $table === 'attendances'
                    => $builder->whereIn(
                        'student_id',
                        DB::table('students')
                            ->where('center_id', $teacher->center_id)
                            ->pluck('id')
                    ),

                default => null, // ✅ جداول تانية — مفيش قيد
            };

            return;
        }

        // ─── supervisor ───────────────────────────────────────────
        if ($user->hasRole('supervisor')) {
            $teacher = DB::table('teachers')
                ->where('user_id', $user->id)
                ->first();

            if (!$teacher) {
                $builder->whereRaw('1 = 0');
                return;
            }

            $circleIds = DB::table('circles')
                ->where('supervisor_id', $teacher->id)
                ->where('center_id', $teacher->center_id)
                ->pluck('id');

            if ($circleIds->isEmpty()) {
                $builder->whereRaw('1 = 0');
                return;
            }

            match ($table) {
                'circles'       => $builder->whereIn('id', $circleIds),
                'students'      => $builder->whereIn('circle_id', $circleIds),
                'subscriptions' => $builder->whereIn('circle_id', $circleIds),
                'attendances'   => $builder->whereIn(
                    'student_id',
                    DB::table('students')
                        ->whereIn('circle_id', $circleIds)
                        ->pluck('id')
                ),
                'teachers'      => $builder->where("{$table}.center_id", $teacher->center_id),
                default         => null, // ✅
            };

            return;
        }

        // ─── teacher ─────────────────────────────────────────────
        if ($user->hasRole('teacher')) {
            $teacher = DB::table('teachers')
                ->where('user_id', $user->id)
                ->first();

            if (!$teacher) {
                $builder->whereRaw('1 = 0');
                return;
            }

            $circleIds = DB::table('circle_teacher')
                ->where('teacher_id', $teacher->id)
                ->pluck('circle_id');

            $circleIds = DB::table('circles')
                ->whereIn('id', $circleIds)
                ->where('center_id', $teacher->center_id)
                ->pluck('id');

            if ($circleIds->isEmpty()) {
                $builder->whereRaw('1 = 0');
                return;
            }

            match ($table) {
                'circles'       => $builder->whereIn('id', $circleIds),
                'students'      => $builder->whereIn('circle_id', $circleIds),
                'subscriptions' => $builder->whereIn('circle_id', $circleIds),
                'attendances'   => $builder->whereIn(
                    'student_id',
                    DB::table('students')
                        ->whereIn('circle_id', $circleIds)
                        ->pluck('id')
                ),
                'teachers'      => $builder->where("{$table}.center_id", $teacher->center_id),
                default         => null, // ✅
            };
        }
    }
}