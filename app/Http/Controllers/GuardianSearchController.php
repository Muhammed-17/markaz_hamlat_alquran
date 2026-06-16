<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GuardianSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ================================================================
    // البحث عن ولي أمر موجود
    // ================================================================
    public function search(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $user   = auth()->user();
        $fields = $this->getAllowedFields($user);

        $guardians = User::role('guardian')
            ->where(function ($query) use ($q, $user) {
                // ✅ البحث بالـ ID لو المدخل رقم
                if (is_numeric($q)) {
                    $query->where('id', (int) $q)
                        ->orWhere('mobile', 'like', "%{$q}%");
                    return;
                }

                // ✅ البحث بالاسم والموبايل لكل الأدوار
                $query->where('name',   'like', "%{$q}%")
                    ->orWhere('mobile', 'like', "%{$q}%");

                // ✅ البحث بالإيميل فقط للأدوار المخولة
                if ($user->hasRole(['admin', 'general_manager', 'manager'])) {
                    $query->orWhere('email', 'like', "%{$q}%");
                }
            })
            ->select($fields)
            ->limit(10)
            ->get()
            ->map(fn($g) => $this->formatGuardian($g, $user));

        return response()->json($guardians);
    }

    // ================================================================
    // التحقق من وجود حساب بالإيميل أو الموبايل
    // ================================================================
    public function check(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $user   = auth()->user();
        $email  = trim($request->get('email', ''));
        $mobile = trim($request->get('mobile', ''));

        // ✅ supervisor لا يقدر يبحث بالإيميل
        if (!empty($email) && !$user->hasRole(['admin', 'general_manager', 'manager'])) {
            $email = '';
        }

        if (empty($email) && empty($mobile)) {
            return response()->json(['exists' => false]);
        }

        $query = User::role('guardian');

        if (!empty($email) && !empty($mobile)) {
            $query->where(
                fn($q) => $q
                    ->where('email', $email)
                    ->orWhere('mobile', $mobile)
            );
        } elseif (!empty($email)) {
            $query->where('email', $email);
        } else {
            $query->where('mobile', $mobile);
        }

        $guardian = $query->select($this->getAllowedFields($user))->first();

        if (!$guardian) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => true,
            ...$this->formatGuardian($guardian, $user),
        ]);
    }

    // ================================================================
    // Private Helpers
    // ================================================================

    /**
     * ✅ تحقق موحد من الصلاحية
     */
    private function authorizeAccess(): void
    {
        $user = auth()->user();

        $allowed =
            $user->can('create students') ||
            $user->can('edit students')   ||
            $user->hasRole(['admin', 'general_manager', 'manager', 'supervisor']);

        if (!$allowed) {
            abort(403, 'ليس لديك صلاحية البحث عن أولياء الأمور');
        }
    }

    /**
     * ✅ الحقول المسموح بعرضها حسب الدور
     */
    private function getAllowedFields(User $user): array
    {
        // الحقول الأساسية لكل الأدوار
        $fields = ['id', 'name', 'mobile', 'status'];

        // ✅ الإيميل فقط للأدوار الإدارية
        if ($user->hasRole(['admin', 'general_manager', 'manager'])) {
            $fields[] = 'email';
        }

        return $fields;
    }

    /**
     * ✅ تنسيق بيانات الـ guardian حسب الدور
     */
    private function formatGuardian(User $guardian, User $currentUser): array
    {
        $data = [
            'id'        => $guardian->id,
            'name'      => $guardian->name,
            'mobile'    => $guardian->mobile ?? '',
            'is_active' => $guardian->status === 'active',
            'status'    => $guardian->status,
        ];

        // ✅ الإيميل فقط للأدوار المخولة
        if ($currentUser->hasRole(['admin', 'general_manager', 'manager'])) {
            $data['email'] = $guardian->email ?? '';
        } else {
            $data['email'] = '';
        }

        return $data;
    }
}
