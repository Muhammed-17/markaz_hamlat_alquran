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
            'name'          => 'required|string|max:255|unique:roles,name',
            'display_name'  => 'nullable|string|max:255', // ✅
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'         => $validated['name'],
            'display_name' => $validated['display_name'] ?? null, // ✅
            'guard_name'   => 'web',
        ]);

        if (!empty($validated['permissions'])) {
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

        // 🛡️ حماية صلاحية admin
        if ($role->name === 'admin') {
            $adminPermissionId = Permission::where('name', 'إدارة الصلاحيات')
                ->orWhere('display_name', 'إدارة الصلاحيات')
                ->value('id');

            if ($adminPermissionId && !in_array($adminPermissionId, $permissionIds)) {
                $permissionIds[] = $adminPermissionId;
            }
        }

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $userIds = \App\Models\User::role($role->name)->pluck('id');
        DB::table('sessions')->whereIn('user_id', $userIds)->delete();

        return redirect()->to(url('admin/roles'))
            ->with('success', 'تم تحديث الصلاحيات بنجاح ✓');
    }

    // ─────────────────────────────────────────────
    // حذف دور ✅ جديد
    // ─────────────────────────────────────────────
    public function destroyRole(Role $role)
    {
        // 🛡️ منع حذف دور admin
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن حذف دور المدير');
        }

        // 🛡️ منع حذف الأدوار الأساسية
        $protectedRoles = ['admin', 'supervisor', 'teacher', 'guardian'];
        if (in_array($role->name, $protectedRoles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن حذف الأدوار الأساسية للنظام');
        }

        // إزالة الصلاحيات أولاً
        $role->syncPermissions([]);

        // حذف الدور
        $role->delete();

        $this->clearPermissionCache();

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح ✓');
    }

    // ─────────────────────────────────────────────
    // مسح الكاش
    // ─────────────────────────────────────────────
    private function clearPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $cacheKey = config('permission.cache.key', 'spatie.permission.cache');
        Cache::forget($cacheKey);
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    }
}