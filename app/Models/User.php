<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Spatie\Permission\Traits\HasRoles;
    use Illuminate\Database\Eloquent\Casts\Attribute;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    /**
     * @property int $id
     * @property string $name
     * @property string $email
     * @property string|null $mobile
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property string $password
     * @property string|null $remember_token
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string $status
     * @property int|null $center_id
     * @property \Illuminate\Support\Carbon|null $last_login_at
     * @property \Illuminate\Support\Carbon|null $last_seen_at
     * @property-read bool $is_online
     * @property-read bool $is_administrative
     */
    class User extends Authenticatable
    {
        /** @use HasFactory<\Database\Factories\UserFactory> */
        use HasFactory, Notifiable, HasRoles;

        /**
         * @var list<string>
         */
        protected $fillable = [
            'name',
            'email',
            'mobile',
            'password',
            'status',
            'center_id',
        ];

        /**
         * @var list<string>
         */
        protected $hidden = [
            'password',
            'remember_token',
        ];

        /**
         * @return array<string, string>
         */
        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'last_login_at' => 'datetime',
                'last_seen_at' => 'datetime',
                'center_id' => 'integer',
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | العلاقات (Relations) - مع إضافة الـ Return Types لتوثيق أفضل للـ IDE
    |--------------------------------------------------------------------------
    |*/

        public function students(): HasMany
        {
            return $this->hasMany(Student::class, 'guardian_id');
        }

        public function teacher(): HasOne
        {
            return $this->hasOne(Teacher::class);
        }

        public function center(): BelongsTo
        {
            return $this->belongsTo(Center::class, 'center_id');
        }

        public function collectedSubscriptions(): HasMany
        {
            return $this->hasMany(Subscription::class, 'collected_by');
        }

        public function attendances(): HasMany
        {
            return $this->hasMany(Attendance::class, 'user_id');
        }

    /*
    |--------------------------------------------------------------------------
    | الصفات المشتقة الحديثة (Modern Accessors)
    |--------------------------------------------------------------------------
    |*/

        /**
         * التحقق مما إذا كان المستخدم متصلاً الآن (خلال آخر 5 دقائق)
         */
        protected function isOnline(): Attribute
        {
            return Attribute::make(
                get: fn() => $this->last_seen_at ? $this->last_seen_at->gt(now()->subMinutes(5)) : false,
            );
        }

        /**
         * جلب الصفة الإدارية للمعلم مباشرة من خلال كائن الـ User
         * تنبيه: تأكد من عمل eager load للعلاقة عبر User::with('teacher') عند جلب مجموعات كبيرة.
         */
        protected function isAdministrative(): Attribute
        {
            return Attribute::make(
                get: fn() => $this->teacher?->is_administrative ?? false,
            );
        }
    }
