<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * @property int $id
 * @property int $student_id
 * @property int $circle_id
 * @property \Illuminate\Support\Carbon $from_date
 * @property \Illuminate\Support\Carbon|null $to_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Student $student
 * @property-read Circle $circle
 */
class CircleAssignmentHistory extends Model
{
    protected $table = 'circle_assignment_history';

    protected $fillable = [
        'student_id',
        'circle_id',
        'from_date',
        'to_date',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date',
            'to_date'   => 'date',
        ];
    }

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }

    // ==========================================
    // Scopes مساعدة
    // ==========================================

    /**
     * السجلات النشطة حاليًا (لم تُغلق بعد)
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->whereNull('to_date');
    }

    /**
     * السجلات التي كانت سارية في تاريخ معيّن
     * (يشمل من بدأ قبل أو في ذلك التاريخ ولم ينتهِ بعده، أو انتهى بعد ذلك التاريخ)
     */
    public function scopeActiveAt(Builder $query, $date): Builder
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $query->where('from_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('to_date')
                    ->orWhere('to_date', '>=', $date);
            });
    }

    /**
     * فلترة بحلقة أو مجموعة حلقات
     */
    public function scopeInCircles(Builder $query, $circleIds): Builder
    {
        return $query->whereIn('circle_id', collect($circleIds)->flatten()->values()->all());
    }

    // ==========================================
    // دوال مساعدة ستاتيكية
    // ==========================================

    /**
     * إنهاء السجل الحالي للطالب (إن وجد) عند تاريخ معيّن
     */
    public static function closeCurrentFor(int $studentId, $toDate = null): void
    {
        static::where('student_id', $studentId)
            ->whereNull('to_date')
            ->update(['to_date' => $toDate ? Carbon::parse($toDate) : now()]);
    }

    /**
     * فتح سجل جديد لانتساب الطالب لحلقة، مع إغلاق أي سجل سابق مفتوح تلقائيًا
     */
    public static function openNewFor(int $studentId, int $circleId, $fromDate = null): self
    {
        static::closeCurrentFor($studentId, $fromDate);

        return static::create([
            'student_id' => $studentId,
            'circle_id'  => $circleId,
            'from_date'  => $fromDate ? Carbon::parse($fromDate) : now(),
            'to_date'    => null,
        ]);
    }

    /**
     * إرجاع معرّفات الطلاب الذين كانوا منتسبين لإحدى الحلقات المحدّدة
     * في تاريخ معيّن (مفيد لحساب "الطلاب النشطين" تاريخيًا بدقة)
     */
    public static function studentIdsInCirclesAt($circleIds, $date): \Illuminate\Support\Collection
    {
        return static::query()
            ->inCircles($circleIds)
            ->activeAt($date)
            ->pluck('student_id')
            ->unique()
            ->values();
    }

    public static function studentIdsInCirclesAtMultipleDates($circleIds, $dates): array
    {
        $dates = collect($dates)->map(fn($d) => Carbon::parse($d));

        if ($dates->isEmpty()) {
            return [];
        }

        $minDate = $dates->min()->format('Y-m-d');
        $maxDate = $dates->max()->format('Y-m-d');

        // ✅ استعلام واحد يجلب كل السجلات التي قد تتقاطع مع أي من التواريخ المطلوبة
        $records = static::query()
            ->inCircles($circleIds)
            ->where('from_date', '<=', $maxDate)
            ->where(function ($q) use ($minDate) {
                $q->whereNull('to_date')
                    ->orWhere('to_date', '>=', $minDate);
            })
            ->get(['student_id', 'from_date', 'to_date']);

        $result = [];
        foreach ($dates as $date) {
            $dateStr = $date->format('Y-m-d');
            $monthLabel = $date->format('Y-m');

            $result[$monthLabel] = $records
                ->filter(function ($r) use ($dateStr) {
                    return $r->from_date->format('Y-m-d') <= $dateStr
                        && (is_null($r->to_date) || $r->to_date->format('Y-m-d') >= $dateStr);
                })
                ->pluck('student_id')
                ->unique()
                ->values()
                ->all();
        }

        return $result;
    }
}
