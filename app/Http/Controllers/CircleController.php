<?php

namespace App\Http\Controllers;


use App\Http\Requests\Circle\CreateCircleRequest;
use App\Http\Requests\Circle\EditCircleRequest;
use App\Models\Circle;
use App\Models\Teacher;
use GuzzleHttp\Promise\Create;

class CircleController extends Controller
{
    /**
    !* Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Circle::with(['mainTeacher', 'assistantTeacher', 'supervisor']);

        if ($user->hasRole('supervisor')) {
            $query->where('supervisor_id', $user->teacher->id);
        } elseif ($user->hasRole('teacher')) {
            $query->whereHas('teachers', function ($q) use ($user) {
                $q->where('teachers.id', $user->teacher->id);
            });
        }

        $circles = $query->get();
        return view('circles.index', compact('circles'));
    }

    /**
    !* Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = Teacher::with('user.roles')
            ->orderBy('name')
            ->get();

        $supervisors = $teachers->filter(function ($teacher) {
            return $teacher->user->hasRole('supervisor');
        });

        return view('circles.create', compact('teachers', 'supervisors'));
    }

    /**
    !* Store a newly created resource in storage.
     */
    public function store(CreateCircleRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = true;

        $circle = Circle::create([
            'name' => $request->name,
            'type' => $request->type,
            'level' => $request->level,
            'supervisor_id' => $request->supervisor_id ?? null,
        ]);

        // ربط جميع المعلمين
        $circle->teachers()->attach($request->teacher_id, [
            'role' => 'main'
        ]);

        $circle->teachers()->attach($request->assistant_teacher_id, [
            'role' => 'assistant'
        ]);


        return redirect()->route('circles.index');
    }

    /**
    !* Display the specified resource.
     */
    public function show(string $id)
    {
        $circle = Circle::findOrFail($id);
        return view('circles.show', compact('circle'));
    }

    /**
    !* Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $circle = Circle::with(['mainTeacher', 'assistantTeacher', 'supervisor'])->findOrFail($id);

        $teachers = Teacher::with('user')->orderBy('name')->get();

        // المشرفين فقط حسب الدور
        $supervisors = $teachers->filter(function ($teacher) {
            return $teacher->user->hasRole('supervisor');
        });

        return view('circles.edit', compact('circle', 'teachers', 'supervisors'));
    }

    /**
    !* Update the specified resource in storage.
     */
    public function update(EditCircleRequest $request, string $id)
    {
        $circle = Circle::findOrFail($id);

        $validated = $request->validated();
        $validated['is_active'] = true;
        // $validated['is_active'] = $request->has('is_active');

        // تحديث بيانات الحلقة الأساسية
        $circle->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'level' => $validated['level'],
            'max_students' => $validated['max_students'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['is_active'],
            'supervisor_id' => $validated['supervisor_id'] ?? null,
        ]);
        // تجهيز بيانات المعلمين مع الدور
        $teachers = [];

        if ($request->teacher_id) {
            $teachers[$request->teacher_id] = ['role' => 'main'];
        }

        if ($request->assistant_teacher_id) {
            $teachers[$request->assistant_teacher_id] = ['role' => 'assistant'];
        }

        // مزامنة جدول pivot مع الأدوار
        $circle->teachers()->sync($teachers);

        return redirect()
            ->route('circles.index')
            ->with('success', 'تم تحديث الحلقة بنجاح');
    }


    /**
    !* Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $circle = Circle::findOrFail($id);

        // يفصل المعلمين قبل الحذف
        $circle->teachers()->detach();

        $circle->delete();

        return redirect()
            ->route('circles.index')
            ->with('success', 'تم حذف الحلقة بنجاح');
    }
}
