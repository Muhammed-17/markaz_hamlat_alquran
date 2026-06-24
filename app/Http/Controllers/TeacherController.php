<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\CreateTeacherRequest;
use App\Http\Requests\Teacher\EditTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use App\Traits\ResolvesUserScope;
use App\Traits\HasAllowedRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use App\Models\Center;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    use ResolvesUserScope;
    use HasAllowedRoles;

    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Teacher::class);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        // ربط جدول المعلمين بجدول المستخدمين لتمكين الترتيب عبر حقول الـ User
        $query = Teacher::query()
            ->join('users', 'teachers.user_id', '=', 'users.id')
            ->select('teachers.*') // نختار حقول المعلم فقط لتجنب تداخل الـ IDs
            ->with(['user.roles', 'center']);

        // 1. فلترة المعلمين النشطين للمتصفح العادي
        if (!$user->hasRole(['admin', 'general_manager'])) {
            $query->where('users.status', 'active');
        }

        // 2. صلاحيات رؤية الفروع والفلترة
        if (!$user->can('view all teachers')) {
            if ($teacher) {
                $query->where(
                    fn($q) =>
                    $q->where('teachers.center_id', $teacher->center_id)
                        ->orWhereHas('circles', fn($cq) =>
                        $cq->where('circles.center_id', $teacher->center_id))
                );
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // 3. بحث بالاسم
        if ($request->filled('search')) {
            $query->where('teachers.name', 'like', '%' . $request->search . '%');
        }

        // 4. فلتر الدور (Roles)
        if ($request->filled('role') && $user->hasRole(['admin', 'general_manager', 'manager'])) {
            $query->whereHas('user.roles', fn($q) => $q->where('name', $request->role));
        }

        // هنا يتم استقبال قيم الترتيب من الـ Request
        $sortBy    = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc') === 'desc' ? 'desc' : 'asc';

        // ==========================================
        // جملة الـ switch المحدثة والمحمية بالكامل:
        // ==========================================
        switch ($sortBy) {
            case 'status':
                // الترتيب حسب الحالة (active / inactive)
                $query->orderBy('users.status', $sortOrder);
                break;

            case 'online':
                // الترتيب حسب الاتصال (الأحدث ظهوراً أولاً) مع دفع الـ NULL للأسفل
                $query->orderByRaw('users.last_seen_at IS NULL, users.last_seen_at ' . $sortOrder);
                break;

            case 'role':
                // الترتيب حسب اسم الدور العربي (display_name) أو الإنجليزي (name)
                // تم استخدام التجميع الفرعي (Subquery) لمنع تكرار صفوف المعلمين نهائياً دون كسر الـ Strict Mode
                $query->leftJoin('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', '=', \App\Models\User::class);
                })
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->orderBy('roles.display_name', $sortOrder); // رتبنا بالـ display_name العربي المضاف حديثاً
                break;

            case 'center':
                // الترتيب حسب اسم الفرع (Center Name)
                $query->leftJoin('centers', 'teachers.center_id', '=', 'centers.id')
                    ->orderBy('centers.name', $sortOrder);
                break;

            default:
                // الترتيب الافتراضي (حسب معرف المعلم الصريح)
                $query->orderBy('teachers.id', $sortOrder);
                break;
        }
        // ==========================================

        // جلب البيانات النهائية وإرسالها للـ View
        $teachers = $query->get();
        $centers  = $this->getAccessibleCenters($user);
        $roles    = Role::orderBy('name')->get();

        return view('teachers.index', compact('teachers', 'centers', 'roles'));
    }

    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Teacher::class);

        $user    = Auth::user();
        $centers = $this->getAccessibleCenters($user);


        $roles = $this->getAllowedRolesForCreate($user);


        return view('teachers.create', compact('centers', 'roles'));
    }


    // ─────────────────────────────────────────
    public function store(CreateTeacherRequest $request)
    {
        $this->authorize('create', Teacher::class);
        $this->authorize('create', User::class);

        DB::transaction(function () use ($request) {
            $isAdministrative = $request->input('is_administrative', 0);

            // إضافة الحقول الأمنية لضمان عدم حجب الحساب فور إنشائه
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
                'center_id'         => $request->center_id,
                'is_administrative' => $isAdministrative,
                'status'            => 'active',          // تفعيل الحساب تلقائياً
                'email_verified_at' => now(),             // تخطي خطوة تأكيد الإيميل برمجياً
            ]);

            $user->syncRoles($request->roles ?? []);

            Teacher::create([
                'user_id'           => $user->id,
                'name'              => $request->name,
                'center_id'         => $request->center_id,
                'is_administrative' => $isAdministrative,
            ]);
        });

        return redirect()->route('teachers.index')->with('success', 'تم إضافة المستخدم بنجاح وتفعيله فوراً');
    }

    // ─────────────────────────────────────────
    public function show(string $id)
    {
        // ✅ فك الحجب عن الموديل الأساسي والعلاقات معاً لضمان جلب المعلم الخارجي بنجاح
        $teacher = Teacher::withoutGlobalScope(\App\Models\Scopes\CenterScope::class)
            ->with([
                'user.roles',
                'center',
                'circles' => function ($query) {
                    // فك الحجب عن الحلقات أيضاً إذا كانت تخضع لنفس الـ Scope
                    $query->withoutGlobalScope(\App\Models\Scopes\CenterScope::class);
                }
            ])->findOrFail($id);

        // 🛡️ هنا يتم الفحص الأمني الذكي (الـ Policy ستسمح له إذا كان بينهما حلقات مشتركة)
        $this->authorize('view', $teacher);

        return view('teachers.show', compact('teacher'));
    }
    // ─────────────────────────────────────────
    public function edit(Teacher $teacher)
    {
        $this->authorize('update', $teacher);

        $user    = Auth::user();
        $centers = $this->getAccessibleCenters($user);

        $teacher->load('user.roles');

        // ✅ تمرير الأدوار المسموحة فقط
        $roles = $this->getAllowedRolesForEdit($user, $teacher);

        $currentRoles = $teacher->user->roles->pluck('name')->toArray();

        return view('teachers.edit', compact('teacher', 'centers', 'roles', 'currentRoles'));
    }

    // ─────────────────────────────────────────
    public function update(EditTeacherRequest $request, Teacher $teacher)
    {
        $this->authorize('update', $teacher);

        // منع تعديل الحساب المعطل إلا لمن لديه صلاحية خاصة
        if ($teacher->user->status === 'inactive' && !$request->user()->can('activate inactive accounts')) {
            return back()->with('error', 'لا يمكن تعديل حساب معطل. يرجى تفعيله أولاً.');
        }

        DB::transaction(function () use ($request, $teacher) {
            $isAdministrative = $request->boolean('is_administrative', false);

            // 1. تحديث بيانات المعلم
            $teacher->update([
                'name'              => $request->name,
                'center_id'         => $request->center_id,
                'is_administrative' => $isAdministrative,
            ]);

            // 2. تجهيز بيانات المستخدم المرتبط
            $data = [
                'name'              => $request->name,
                'email'             => $request->email,
                'center_id'         => $request->center_id,
                'is_administrative' => $isAdministrative,
            ];

            // ✅ ضع الكود هنا - بعد تجهيز $data وقبل تحديث المستخدم
            if ($request->filled('password')) {
                // التحقق من كلمة المرور الحالية فقط إذا لم يكن admin أو general_manager
                if (!Auth::user()->hasRole(['admin', 'general_manager', 'manager'])) {
                    if ($request->user()->id === $teacher->user_id) {
                        if (!Hash::check($request->current_password, $teacher->user->password)) {
                            throw ValidationException::withMessages([
                                'current_password' => 'كلمة المرور الحالية غير صحيحة.'
                            ]);
                        }
                    }
                }
                $data['password'] = Hash::make($request->password);
            }

            $teacher->user->update($data);
            $teacher->user->syncRoles($request->roles ?? []);
        });

        return redirect()->route('teachers.index')->with('success', 'تم تحديث البيانات بنجاح');
    }

    // ─────────────────────────────────────────
    public function destroy(Teacher $teacher)
    {
        $this->authorize('delete', $teacher);

        // التحقق من عدم وجود حلقات مرتبطة
        if ($teacher->circles()->exists()) {
            return back()->with('error', 'لا يمكن حذف معلم مرتبط بحلقات.');
        }

        // منع حذف نفسه
        if (Auth::id() === $teacher->user_id) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص.');
        }

        // منع حذف admin أو general_manager
        if ($teacher->user->hasRole(['admin', 'general_manager'])) {
            return back()->with('error', 'لا يمكن حذف حساب إداري رئيسي.');
        }

        DB::transaction(function () use ($teacher) {
            $teacherId = $teacher->id;
            $userId = $teacher->user_id;

            $teacher->user->delete();
            $teacher->delete();

            // Audit Log
            Log::channel('audit')->info('teacher_deleted', [
                'actor_id' => Auth::id(),
                'teacher_id' => $teacherId,
                'user_id' => $userId,
            ]);
        });

        return redirect()->route('teachers.index')->with('success', 'تم الحذف بنجاح');
    }

    // ─────────────────────────────────────────
    public function toggle(Teacher $teacher)
    {
        $this->authorize('toggle', $teacher);

        // منع تعطيل نفسه
        if (Auth::id() === $teacher->user_id) {
            return back()->with('error', 'لا يمكنك تعطيل حسابك الخاص.');
        }

        // منع تعطيل admin أو general_manager
        if ($teacher->user->hasRole(['admin', 'general_manager'])) {
            return back()->with('error', 'لا يمكن تعطيل حساب إداري رئيسي.');
        }

        $newStatus = $teacher->user->status === 'active' ? 'inactive' : 'active';

        $teacher->user->update([
            'status' => $newStatus,
        ]);

        // Audit Log
        Log::channel('audit')->info('teacher_toggled', [
            'actor_id' => Auth::id(),
            'teacher_id' => $teacher->id,
            'new_status' => $newStatus,
        ]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
