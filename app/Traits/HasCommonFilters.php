<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasCommonFilters
{
    /**
     * تطبيق فلتر البحث النصي على عدة أعمدة.
     *
     * @param Builder $query
     * @param Request $request
     * @param array $fields  أعمدة الجدول المراد البحث فيها، مثل ['name', 'email', 'mobile']
     * @param string $param  اسم query string parameter (افتراضي: q)
     */
    public function applySearch(Builder $query, Request $request, array $fields, string $param = 'q'): Builder
    {
        if ($request->filled($param)) {
            $term = $request->input($param);

            $query->where(function (Builder $q) use ($fields, $term) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', "%{$term}%");
                }
            });
        }

        return $query;
    }

    /**
     * تطبيق فلتر الحالة (status) مباشرة على الجدول الحالي.
     */
    public function applyStatus(Builder $query, Request $request, string $column = 'status', string $param = 'status'): Builder
    {
        if ($request->filled($param)) {
            $query->where($column, $request->input($param));
        }

        return $query;
    }

    /**
     * تطبيق فلتر الفرع (center) سواء كان عمود مباشر أو عبر علاقة.
     *
     * @param string|null $relation  اسم العلاقة لو الفلتر عبر whereHas (مثل 'students')
     * @param string $column  اسم عمود الفرع في الجدول المستهدف (center_id)
     */
    public function applyCenter(
        Builder $query,
        Request $request,
        ?string $relation = null,
        string $column = 'center_id',
        string $param = 'center'
    ): Builder {
        if ($request->filled($param)) {
            $centerId = $request->input($param);

            if ($relation) {
                $query->whereHas($relation, function (Builder $q) use ($column, $centerId) {
                    $q->where($column, $centerId);
                });
            } else {
                $query->where($column, $centerId);
            }
        }

        return $query;
    }

    /**
     * تطبيق الترتيب (sort) بأمان مع قائمة أعمدة مسموحة فقط لتجنب SQL Injection.
     *
     * @param array $allowed  الأعمدة المسموح الترتيب بها
     * @param string $default  العمود الافتراضي
     */
    public function applySort(
        Builder $query,
        Request $request,
        array $allowed,
        string $default = 'created_at'
    ): Builder {
        $sortField = $request->input('sort', $default);
        $sortDir = $request->input('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        if (!in_array($sortField, $allowed, true)) {
            $sortField = $default;
        }

        return $query->orderBy($sortField, $sortDir);
    }
}
