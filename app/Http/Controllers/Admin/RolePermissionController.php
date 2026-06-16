<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    private const PROTECTED_ROLES = ['admin', 'manager', 'supervisor', 'teacher', 'guardian'];

    // ─────────────────────────────────────────────
    public function index(): View
    {
        $roles       = Role::with('permissions')->get();
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()
            ->groupBy('group');

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    // ─────────────────────────────────────────────
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name',
            'display_name'  => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'         => $validated['name'],
            'display_name' => $validated['display_name'] ?? null,
            'guard_name'   => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions(
                Permission::whereIn('id', $validated['permissions'])->get()
            );
        }

        $this->clearPermissionCache();

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح ✓');
    }

    // ─────────────────────────────────────────────
    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $request->input('permissions', []);

        // 🛡️ حماية manage roles للـ admin دايماً
        if ($role->name === 'admin') {
            $manageRolesId = Permission::where('name', 'manage roles')->value('id');
            if ($manageRolesId && !in_array($manageRolesId, $permissionIds)) {
                $permissionIds[] = $manageRolesId;
            }
        }

        $role->syncPermissions(
            Permission::whereIn('id', $permissionIds)->get()
        );

        // logout المستخدمين عشان ياخدوا الصلاحيات الجديدة
        $userIds = User::role($role->name)->pluck('id');
        DB::table('sessions')->whereIn('user_id', $userIds)->delete();

        $this->clearPermissionCache();

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم تحديث الصلاحيات بنجاح ✓');
    }

    // ─────────────────────────────────────────────
    public function destroyRole(Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن حذف الأدوار الأساسية للنظام');
        }

        $role->syncPermissions([]);
        $role->delete();

        $this->clearPermissionCache();

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح ✓');
    }

    // ─────────────────────────────────────────────
    private function clearPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
