<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    public function index()
    {
        $centers = Center::all();
        return view('centers.index', compact('centers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:centers,id',
            'name' => 'required|string|max:255|unique:centers,name,' . $request->id,
        ], [
            'name.required' => 'حقل الاسم مطلوب.',
            'name.unique' => 'هذا الفرع مسجل بالفعل.',
        ]);

        if (!empty($validated['id'])) {
            $center = Center::findOrFail($validated['id']);
            $center->update(['name' => $validated['name']]);
            $msg = 'تم تحديث الفرع بنجاح';
        } else {
            Center::create(['name' => $validated['name']]);
            $msg = 'تم إضافة الفرع بنجاح';
        }

        return redirect()->route('centers.index')->with('success', $msg);
    }

    public function destroy(Center $center)
    {
        // ✅ استخدم center_id بدل center
        $studentsCount = \App\Models\Student::where('center_id', $center->id)->count();

        if ($studentsCount > 0) {
            return redirect()->route('centers.index')
                ->with('error', 'لا يمكن حذف هذا الفرع لأنه مرتبط بـ ' . $studentsCount . ' طالب.');
        }

        $center->delete();
        return redirect()->route('centers.index')->with('success', 'تم حذف الفرع بنجاح');
    }
}
