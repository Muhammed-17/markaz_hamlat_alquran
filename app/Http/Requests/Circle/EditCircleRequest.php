<?php

namespace App\Http\Requests\Circle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\ResolvesUserScope;
use App\Models\Circle;

class EditCircleRequest extends FormRequest
{
    use ResolvesUserScope;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $circle = Circle::find($this->route('circle'));
        if (!$circle) return false;
        return $this->user()->can('edit circles') && $this->user()->can('update', $circle);
    }

    public function rules(): array
    {
        $circleId = $this->route('circle');
        $circle = Circle::find($circleId);
        $centerId = $circle?->center_id ?? $this->center_id;

        $accessibleCenterIds = $this->getAccessibleCenters($this->user())->pluck('id');

        return [
            'name' => ['required', 'string', 'max:255', 'unique:circles,name,' . $circleId],
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
                $this->validateSameCenter($centerId, 'المعلم الرئيسي'),
            ],

            'assistant_teacher_id' => [
                'nullable',
                'exists:teachers,id',
                $this->validateSameCenter($centerId, 'المعلم المساعد'),
            ],

            'supervisor_ids' => 'required|array|min:1',
            'supervisor_ids.*' => [
                'exists:teachers,id',
                $this->validateSameCenter($centerId, 'المشرف'),
            ],
        ];
    }

    private function validateSameCenter(int $centerId, string $roleName)
    {
        return function ($attribute, $value, $fail) use ($centerId, $roleName) {
            if (!$value) return;
            $teacher = \App\Models\Teacher::find($value);
            if ($teacher && $teacher->center_id != $centerId) {
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
        $name = trim($this->name);
        $this->merge([
            'name' => str_starts_with($name, 'حلقة') ? $name : 'حلقة ' . $name,
        ]);
    }
}
