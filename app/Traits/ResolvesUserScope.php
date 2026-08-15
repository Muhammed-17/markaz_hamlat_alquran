<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Teacher;
use App\Services\UserAccessService;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * ✅ تعديل جذري: تم حذف كل منطق الصلاحيات المكرر من هنا بالكامل ونقله إلى
 * App\Services\UserAccessService (نفس المصدر المستخدم الآن في CenterScope).
 * هذا الـ Trait أصبح مجرد "واجهة تفويض" (delegation layer) رفيعة تحافظ
 * على نفس أسماء وتوقيعات (signatures) الميثودز القديمة بالحرف الواحد،
 * حتى لا ينكسر أي كود يستخدمها حاليًا في الـ Controllers / Policies.
 */
trait ResolvesUserScope
{
    private function access(): UserAccessService
    {
        return app(UserAccessService::class);
    }

    protected function getTeacherRecord(User $user): ?Teacher
    {
        return $this->access()->teacher($user);
    }

    protected function getAccessibleCircleIds(User $user): Collection
    {
        return $this->getAccessibleCirclesQuery($user)->pluck('id');
    }

    protected function getAccessibleCircles(User $user): Collection
    {
        return $this->getAccessibleCirclesQuery($user)->get();
    }

    protected function getAccessibleCirclesQuery(User $user): Builder
    {
        return $this->access()->accessibleCircles($user);
    }

    protected function getAccessibleCenters(User $user): Collection
    {
        // ✅ ملحوظة: accessibleCenters() في السيرفس بترجع Builder،
        // فبنعمل ->get() هنا عشان نطابق نوع الإرجاع القديم بالحرف الواحد.
        return $this->access()->accessibleCenters($user)->get();
    }

    protected function getAccessibleTeachers(User $user, ?Teacher $teacher): Collection
    {
        return $this->getAccessibleTeachersQuery($user, $teacher)->get();
    }

    protected function getAccessibleTeachersQuery(User $user, ?Teacher $teacher): Builder
    {
        // ✅ ملحوظة مهمة: التوقيع القديم بياخد $teacher جاهز من الكولر
        // (مش بيجيبه بنفسه)، عكس UserAccessService::accessibleTeachers()
        // اللي بتجيب الـ teacher داخليًا عبر $this->teacher($user).
        // بما إن الاتنين بيرجعوا نفس الـ Teacher record لنفس الـ user
        // (نفس مصدر البيانات وبنفس الكاش)، التفويض هنا آمن ومطابق تمامًا
        // للسلوك القديم من ناحية النتيجة، مع تبسيط المنطق الداخلي.
        return $this->access()->accessibleTeachers($user);
    }

    protected function applyTeacherCenterScope(Builder $query, Teacher $record): void
    {
        // Refactored: تفويض كامل بدل تكرار المنطق — المصدر الوحيد الآن UserAccessService
        $this->access()->applyTeacherCenterFilter($query, $record);
    }

    protected function getAccessibleSupervisors(User $user, ?Teacher $teacher): Collection
    {
        // ✅ دالة مش موجودة في UserAccessService — فضلت هنا زي ما هي،
        // مبنية فوق getAccessibleTeachersQuery المفوَّضة أعلاه.
        return $this->getAccessibleTeachersQuery($user, $teacher)
            ->whereHas('user.roles', fn($r) => $r->whereIn('name', ['supervisor', 'manager', 'general_manager']))
            ->get();
    }

    protected function applyCircleFilter(Builder $query, User $user, array|Collection $circleIds): void
    {
        // ✅ منطق عام بسيط مش مرتبط بـ UserAccessService — اتسيب زي ما هو.
        if ($user->hasRole(['admin', 'general_manager'])) {
            return;
        }

        $query->whereIn('circle_id', $circleIds);
    }

    public static function clearScopeCache(): void
    {
        app(UserAccessService::class)->clearCache();
    }
}
