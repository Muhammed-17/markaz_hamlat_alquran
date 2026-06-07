<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $circle_level
 * @property string $education_level
 * @property string|null $school_grade
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereCircleLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereEducationLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereSchoolGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
class SubscriptionPrice extends Model
{
    use HasRoles;
    protected $fillable = ['circle_level', 'education_level', 'school_grade', 'amount'];

    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Models\Scopes\CenterScope());
    }

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
