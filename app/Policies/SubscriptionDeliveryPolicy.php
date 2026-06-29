<?php

namespace App\Policies;

use App\Models\SubscriptionDelivery;
use App\Models\User;

class SubscriptionDeliveryPolicy
{
    /**
     * عرض قائمة التسليمات
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['teacher', 'supervisor', 'admin', 'general_manager']);
    }

    /**
     * عرض تسليم محدد
     */
    public function view(User $user, SubscriptionDelivery $delivery): bool
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        if ($user->id === $delivery->teacher_id) {
            return true;
        }

        if ($user->id === $delivery->supervisor_id) {
            return true;
        }

        return false;
    }

    /**
     * إنشاء تسليم جديد
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['teacher', 'admin', 'general_manager']);
    }

    /**
     * تعديل تسليم
     */
    public function update(User $user, SubscriptionDelivery $delivery): bool
    {
        // الأدمن والمدير العام يستطيعان التعديل دائماً
        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        // المعلم يستطيع التعديل فقط قبل الاعتماد
        if ($user->id === $delivery->teacher_id && !$delivery->confirmed_by_admin) {
            return true;
        }

        return false;
    }

    /**
     * حذف تسليم
     */
    public function delete(User $user, SubscriptionDelivery $delivery): bool
    {
        // فقط الأدمن يستطيع الحذف
        if (!$user->hasRole(['admin', 'general_manager'])) {
            return false;
        }

        // لا يمكن حذف تسليم مُعتمد
        if ($delivery->confirmed_by_admin) {
            return false;
        }

        return true;
    }

    /**
     * مراجعة المدير (تأكيد + تعديل المبلغ المحصل)
     */
    public function adminConfirm(User $user, SubscriptionDelivery $delivery): bool
    {
        return $user->hasRole(['admin', 'general_manager', 'supervisor', 'manager']);
    }

    /**
     * ❌ محذوفة: confirm (استبدلت بـ adminConfirm)
     */
    // public function confirm(User $user, SubscriptionDelivery $delivery): bool
    // {
    //     return $user->hasRole(['supervisor', 'admin', 'general_manager']);
    // }
}
