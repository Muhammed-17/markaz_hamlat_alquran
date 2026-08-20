<?php

namespace App\Http\Requests\Circle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\UserAccessService;
use App\Models\Circle;

class StoreCircleRequest extends FormRequest
{


    public function authorize(): bool
    {
        return $this->user()->can('create circles');
    }

    public function rules(): array
    {
        $user   = $this->user();
        $access = app(UserAccessService::class);
        $accessibleCenterIds = $access->accessibleCenters($user)->pluck('id');

        // ✅ FIX: للمشرف — أضف مراكز الحلقات التي يشرف عليها (عبر الفرع، بعد إن circles.center_id اتشال)
        if ($user->hasRole('supervisor') && !$user->hasRole(['manager', 'admin', 'general_manager'])) {
            $teacher = $access->teacher($user);
            $supervisorCenterIds = \App\Models\Circle::whereHas('supervisors', fn($q) => $q->where('teachers.id', $teacher?->id))
                ->with('branch')
                ->get()
                ->pluck('branch.center_id')
                ->filter()
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
            ],

            'assistant_teacher_id' => [
                'nullable',
                'exists:teachers,id',
            ],

            'supervisor_ids' => 'required|array|min:1',
            'supervisor_ids.*' => [
                'exists:teachers,id',
            ],
        ];
    }

    //         'teacher_id' => [
    //             'required',
    //             'exists:teachers,id',
    //             $this->validateSameCenter('المعلم الرئيسي'),
    //         ],

    //         'assistant_teacher_id' => [
    //             'nullable',
    //             'exists:teachers,id',
    //             $this->validateSameCenter('المعلم المساعد'),
    //         ],

    //         'supervisor_ids' => 'required|array|min:1',
    //         'supervisor_ids.*' => [
    //             'exists:teachers,id',
    //             $this->validateSameCenter('المشرف'),
    //         ],
    //     ];
    // }

    // private function validateSameCenter(string $roleName)
    // {
    //     return function ($attribute, $value, $fail) use ($roleName) {
    //         if (!$value) return;
    //         $teacher = \App\Models\Teacher::find($value);
    //         if ($teacher && $teacher->center_id != $this->center_id) {
    //             $fail("{$roleName} يجب أن يكون في نفس الفرع.");
    //         }
    //     };
    // }

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
        $user    = $this->user();
        $access  = app(UserAccessService::class);
        $teacher = $access->teacher($user);

        if (!$this->input('center_id')) {
            $centerId = $teacher?->center_id;

            if (!$centerId && $user->hasRole('supervisor')) {
                $firstCircle = Circle::whereHas('supervisors', fn($q) => $q->where('teachers.id', $teacher?->id))
                    ->first();
                $centerId = $firstCircle?->center_id; // عبر accessor في Circle model — آمن (property access مش query)
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
