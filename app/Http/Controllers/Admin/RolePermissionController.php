<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        //     $roles = Role::with('permissions')->get();

        // dd($roles->find(4)->permissions->pluck('id', 'name'));
        // ✅ fresh() لضمان جلب أحدث بيانات بدون كاش علاقات Eloquent
        $roles       = Role::with('permissions')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    // ─────────────────────────────────────────────
    // إنشاء دور جديد
    // ─────────────────────────────────────────────
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255|unique:roles,name',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            // ✅ جلب الـ Permission objects بالـ IDs ثم syncPermissions
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        $this->clearPermissionCache();

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح ✓');
    }

    // ─────────────────────────────────────────────
    // تحديث صلاحيات دور موجود
    // ─────────────────────────────────────────────
    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $request->input('permissions', []);

        // 🛡️ خط الدفاع الأقوى ضد الـ Inspect والتلاعب الخارجي
        if ($role->name === 'admin') {
            // ابحث عن المعرف (ID) الخاص بصلاحية إدارة الصلاحيات
            $adminPermissionId = \Spatie\Permission\Models\Permission::where('name', 'إدارة الصلاحيات')
                ->orWhere('display_name', 'إدارة الصلاحيات')
                ->value('id');

            // إذا تم العثور على الصلاحية ولم تكن موجودة في المصفوفة القادمة من المستخدم، أضفها إجبارياً
            if ($adminPermissionId && !in_array($adminPermissionId, $permissionIds)) {
                $permissionIds[] = $adminPermissionId;
            }
        }

        // جلب الصلاحيات بناءً على المصفوفة المؤمنة والمحمية
        $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->get();

        // 1. حفظ الصلاحيات
        $role->syncPermissions($permissions);

        // 2. مسح كاش Spatie
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 3. ✅ مسح session كل المستخدمين الذين لهم هذا الـ role
        $userIds = \App\Models\User::role($role->name)->pluck('id');
        \Illuminate\Support\Facades\DB::table('sessions')
            ->whereIn('user_id', $userIds)
            ->delete();

        return redirect()->to(url('admin/roles'))
            ->with('success', 'تم تحديث الصلاحيات بنجاح ✓');
    }
    // ─────────────────────────────────────────────
    // مسح الكاش بشكل شامل
    // ─────────────────────────────────────────────
    private function clearPermissionCache(): void
    {
        // مسح كاش Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // مسح كاش Laravel العام (يشمل Redis/Memcached/File)
        // يستهدف فقط مفتاح Spatie لتجنب مسح كاش التطبيق كله
        $cacheKey = config('permission.cache.key', 'spatie.permission.cache');
        Cache::forget($cacheKey);

        // إعادة تهيئة الـ PermissionRegistrar بالكامل
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    }
}
