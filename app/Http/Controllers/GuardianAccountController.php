<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use App\Traits\HasCommonFilters;

class GuardianAccountController extends Controller
{
    use HasCommonFilters;
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        // $this->authorize('manage guardians');

        $query = User::role('guardian')
            ->withCount('students')
            ->with('students:id,guardian_id,center_id');

        $this->applySearch($query, $request, ['name', 'email', 'mobile']);
        $this->applyStatus($query, $request);
        $this->applyCenter($query, $request, relation: 'students', param: 'center_id');
        $this->applySort($query, $request, allowed: ['name', 'email', 'mobile', 'status', 'created_at'], default: 'name');

        $guardians = $query->paginate(20)->withQueryString();

        $centers = \App\Models\Center::orderBy('name')->get(['id', 'name']);

        return view('guardian.index_accounts', compact('guardians', 'centers'));
    }
    // ─────────────────────────────────────────
    public function show(User $guardian)
    {
        // $this->authorize('manage guardians');
        abort_unless($guardian->hasRole('guardian'), 404);

        $guardian->load([
            'students:id,name,status,student_code,guardian_id',
            'center:id,name',
        ]);

        return view('guardian.show', compact('guardian'));
    }

    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', [User::class, 'guardian']);
        $centers = \App\Models\Center::orderBy('name')->get(['id', 'name']);
        return view('guardian.form', [
            'guardian' => new User(),
            'centers'  => $centers,
        ]);
    }

    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        // $this->authorize('manage guardians');

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile'    => ['nullable', 'string', 'max:20', 'unique:users,mobile'],
            'center_id' => ['nullable', 'exists:centers,id'],
            'password'  => ['required', 'confirmed', Password::min(8)],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status']   = 'active';
        // ⚠️ بدون هذا، guardian.dashboard (محمية بـ 'verified' middleware)
        // ستكون مغلقة تماماً أمام ولي الأمر فور إنشاء حسابه.
        $data['email_verified_at'] = now();

        $guardian = User::create($data);
        $guardian->assignRole('guardian');

        return redirect()->route('guardians.index')
            ->with('success', 'تم إنشاء حساب ولي الأمر بنجاح ✓');
    }


    // ─────────────────────────────────────────
    public function edit(User $guardian)
    {
        // $this->authorize('manage guardians');
        abort_unless($guardian->hasRole('guardian'), 404);

        $guardian->load('students:id,name,status,student_code,guardian_id');
        $centers = \App\Models\Center::orderBy('name')->get(['id', 'name']);

        return view('guardian.form', compact('guardian', 'centers'));
    }

    // ─────────────────────────────────────────
    public function update(Request $request, User $guardian)
    {
        // $this->authorize('manage guardians');
        abort_unless($guardian->hasRole('guardian'), 404);

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email,' . $guardian->id],
            'mobile'    => ['nullable', 'string', 'max:20', 'unique:users,mobile,' . $guardian->id],
            'center_id' => ['nullable', 'exists:centers,id'],
            'password'  => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $guardian->update($data);

        return redirect()->route('guardians.index')
            ->with('success', 'تم تحديث بيانات ولي الأمر بنجاح ✓');
    }

    // ─────────────────────────────────────────
    public function toggleStatus(User $guardian)
    {
        // $this->authorize('update', [$guardian, 'guardian']);
        abort_unless($guardian->hasRole('guardian'), 404);

        $guardian->update([
            'status' => $guardian->status === 'active' ? 'inactive' : 'active',
        ]);

        $msg = $guardian->status === 'active' ? 'تم تفعيل الحساب ✓' : 'تم تعطيل الحساب ✓';

        return redirect()->back()->with('success', $msg);
    }

    // ─────────────────────────────────────────
    public function destroy(User $guardian)
    {
        // $this->authorize('delete', [$guardian, 'guardian']);
        abort_unless($guardian->hasRole('guardian'), 404);

        if ($guardian->students()->exists()) {
            return redirect()->back()->with(
                'error',
                'لا يمكن حذف ولي الأمر لوجود طلاب مرتبطين به — قم بإلغاء الربط أولاً'
            );
        }

        $guardian->delete();

        return redirect()->route('guardians.index')->with('success', 'تم حذف الحساب بنجاح');
    }
}
