<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Circle> $circles
 * @property-read int|null $circles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
class Teacher extends Model
{
    use HasRoles;
    protected $fillable = ['user_id', 'name', 'center_id'];

    protected static function booted(): void
{
    static::addGlobalScope(new \App\Models\Scopes\CenterScope());
}
    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    // علاقة: المعلم ← مستخدمه
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة: المعلم ←→ حلقاته (Many-to-Many)
    public function circles()
    {
        return $this->belongsToMany(Circle::class, 'circle_teacher');
    }
}
