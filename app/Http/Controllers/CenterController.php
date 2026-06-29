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
        // ✅ تنظيف الاسم: إزالة "فرع" و"الفرع" من أي مكان في النص + المسافات الزائدة
        $cleanName = $request->input('name', '');

        // حذف "الفرع" أولاً (الأطول) ثم "فرع" لتجنب بقايا الـ "ال"
        $cleanName = preg_replace('/\bالفرع\b/u', ' ', $cleanName);
        $cleanName = preg_replace('/\bفرع\b/u', ' ', $cleanName);

        // إزالة المسافات الزائدة (متعددة، بداية، نهاية)
        $cleanName = preg_replace('/\s+/u', ' ', $cleanName);
        $cleanName = trim($cleanName);

        // ✅ استبدال القيمة في الطلب للتحقق منها
        $request->merge(['name' => $cleanName]);

        $validated = $request->validate([
            'id'   => 'nullable|exists:centers,id',
            'name' => 'required|string|max:255|unique:centers,name,' . $request->id,
        ], [
            'name.required' => 'حقل الاسم مطلوب.',
            'name.unique'   => 'هذا الفرع مسجل بالفعل.',
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
