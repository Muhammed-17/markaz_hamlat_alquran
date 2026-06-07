<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property int $student_id
 * @property \Illuminate\Support\Carbon $date
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
class Attendance extends Model
{
    use HasRoles;
    protected $fillable = ['student_id', 'date', 'status', 'notes', 'user_id'];

    protected $casts = [
        'date' => 'date',
    ];
    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Models\Scopes\CenterScope());
    }

    // علاقة: الحضور ← طالبه
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // علاقة: الحضور ← المستخدم الذي سجله
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
