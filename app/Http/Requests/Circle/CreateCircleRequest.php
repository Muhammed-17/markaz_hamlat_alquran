<?php

namespace App\Http\Requests\Circle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\ResolvesUserScope;

class CreateCircleRequest extends FormRequest
{
    use ResolvesUserScope;

    public function authorize(): bool
    {
        return $this->user()->can('create circles');
    }

    public function rules(): array
    {
        $user = $this->user();
        $accessibleCenterIds = $this->getAccessibleCenters($user)->pluck('id');

        // ✅ FIX: للمشرف — أضف center_id من الحلقات التي يشرف عليها
        if ($user->hasRole('supervisor') && !$user->hasRole(['manager', 'admin', 'general_manager'])) {
            $teacher = $this->getTeacherRecord($user);
            $supervisorCenterIds = \App\Models\Circle::whereHas('supervisors', fn($q) => $q->where('teachers.id', $teacher?->id))
                ->pluck('center_id')
                ->unique()
                ->values();
            $accessibleCenterIds = $accessibleCenterIds->merge($supervisorCenterIds);
        }

        return [
            'name' => 'required|string|max:255|unique:circles,name',
            'type' => 'required|string',
            'level' => 'required|string',
            
            'center_id' => [
                'required',
                'exists:centers,id',
                Rule::in($accessibleCenterIds),
            ],
            
            'teacher_id' => [
                'required',
                'exists:teachers,id',
                $this->validateSameCenter('المعلم الرئيسي'),
            ],
            
            'assistant_teacher_id' => [
                'nullable',
                'exists:teachers,id',
                $this->validateSameCenter('المعلم المساعد'),
            ],
            
            'supervisor_ids' => 'required|array|min:1',
            'supervisor_ids.*' => [
                'exists:teachers,id',
                $this->validateSameCenter('المشرف'),
            ],
        ];
    }

    private function validateSameCenter(string $roleName)
    {
        return function ($attribute, $value, $fail) use ($roleName) {
            if (!$value) return;
            $teacher = \App\Models\Teacher::find($value);
            if ($teacher && $teacher->center_id != $this->center_id) {
                $fail("{$roleName} يجب أن يكون في نفس الفرع.");
            }
        };
    }

    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب.',
            'name.string' => 'حقل الاسم يجب أن يكون نصًا.',
            'name.max' => 'حقل الاسم لا يجب أن يتجاوز 255 حرفًا.',
            'name.unique' => 'اسم الحلقة مستخدم بالفعل، يرجى اختيار اسم آخر.',
            'type.required' => 'حقل النوع مطلوب.',
            'type.string' => 'حقل النوع يجب أن يكون نصًا.',
            'level.required' => 'حقل المستوى مطلوب.',
            'level.string' => 'حقل المستوى يجب أن يكون نصًا.',
            'center_id.required' => 'حقل الفرع مطلوب.',
            'center_id.exists' => 'الفرع المحدد غير موجود.',
            'center_id.in' => 'الفرع المحدد غير متاح لك.',
            'teacher_id.required' => 'حقل المعلم الرئيسي مطلوب.',
            'teacher_id.exists' => 'المعلم الرئيسي المحدد غير موجود.',
            'assistant_teacher_id.exists' => 'المعلم المساعد المحدد غير موجود.',
            'supervisor_ids.required' => 'يجب اختيار مشرف واحد على الأقل.',
            'supervisor_ids.array' => 'حقل المشرفين يجب أن يكون قائمة.',
            'supervisor_ids.min' => 'يجب اختيار مشرف واحد على الأقل.',
            'supervisor_ids.*.exists' => 'أحد المشرفين المحددين غير موجود.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();
        $teacher = $this->getTeacherRecord($user);

        // ✅ FIX: إذا لم يكن center_id مرسلاً أو فارغاً، استخدم فرع المستخدم
        $centerId = $this->input('center_id');
        
        if (!$centerId) {
            $centerId = $teacher?->center_id;
            
            // للمشرف بدون center_id — من أول حلقة يشرف عليها
            if (!$centerId && $user->hasRole('supervisor')) {
                $firstCircle = \App\Models\Circle::whereHas('supervisors', fn($q) => $q->where('teachers.id', $teacher?->id))
                    ->first();
                $centerId = $firstCircle?->center_id;
            }
            
            if ($centerId) {
                $this->merge(['center_id' => $centerId]);
            }
        }

        // تنسيق الاسم
        if ($this->has('name')) {
            $name = trim($this->name);
            $this->merge([
                'name' => str_starts_with($name, 'حلقة') ? $name : 'حلقة ' . $name,
            ]);
        }
    }
}