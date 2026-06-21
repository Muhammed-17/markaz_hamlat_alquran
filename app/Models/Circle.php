<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Scopes\CenterScope;
use App\Models\Center;


/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $level
 * @property int $max_students
 * @property string|null $notes
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $assistantTeacher
 * @property-read int|null $assistant_teacher_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $mainTeacher
 * @property-read int|null $main_teacher_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \App\Models\Teacher|null $supervisor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $teachers
 * @property-read int|null $teachers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereMaxStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
class Circle extends Model
{
    use HasRoles;
    // app/Models/Circle.php — أضف center_id للـ fillable وأضف booted
    protected $fillable = [
        'name',
        'type',
        'level',
        'max_students',
        'notes',
        'is_active',
        'center_id', // ← أضف
    ];

    // protected static function booted(): void
    // {
    //     static::addGlobalScope(new CenterScope());

    //     static::creating(function ($model) {
    //         if (!$model->center_id && auth()->check()) {
    //             $model->center_id = auth()->user()->center_id;
    //         }
    //     });
    // }
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class)->withPivot('role')->withTimestamps();
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function supervisors()
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'supervisor')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function mainTeacher()
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'main')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assistantTeacher()
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'assistant')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function getLevelArabicAttribute()
    {
        return match ($this->level) {
            'build' => 'بناء',
            'mastery' => 'إتقان',
            'creativity' => 'إبداع',
            default => $this->level,
        };
    }

    public function getTypeArabicAttribute()
    {
        return match ($this->type) {
            'group' => 'جماعية',
            'individual' => 'فردية',
            default => $this->type,
        };
    }
}
