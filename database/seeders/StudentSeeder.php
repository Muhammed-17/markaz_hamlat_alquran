<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Center;
use App\Models\Circle;
use App\Models\Student;
use App\Models\StudentConstructionDetail;
use App\Models\StudentItqanDetail;
use App\Models\Surah;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * عدد الطلاب المطلوب توليدهم.
     * غيّر الرقم أو مرّره وقت التشغيل: php artisan db:seed --class=StudentSeeder
     */
    protected int $count = 30;

    public function run(): void
    {
        // ─────────────────────────────────────────
        // 1) تجهيز البيانات المرجعية (centers / circles / supervisors / guardians)
        // لو الجداول دي فاضية، ننشئ عدد بسيط منها عشان الـ Seeder يشتغل من غير أخطاء FK.
        // ─────────────────────────────────────────

        // ✅ الهيكل التنظيمي الحقيقي: مركزان، كل واحد بفرعين
        $centersData = [
            'الرئيسي' => ['الصديق', 'الفاروق'],
            'العواسجة'  => ['ذي النورين', 'على بن أبي طالب'],
        ];

        $branches = collect();
        foreach ($centersData as $centerName => $branchNames) {
            $center = Center::firstOrCreate(['name' => $centerName]);
            foreach ($branchNames as $branchName) {
                $branches->push(
                    Branch::firstOrCreate(
                        ['name' => $branchName, 'center_id' => $center->id]
                    )
                );
            }
        }

        $centers = Center::whereIn('name', array_keys($centersData))->get();

        $circles = Circle::query()->inRandomOrder()->limit(20)->get();

        // ✅ التأكد من وجود حلقتي "أبو بكر الصديق" (فردي) و"عمر بن الخطاب" (جماعي)
        // بيتسكنوا في فرع الصديق تحديدًا.
        $siddiqBranch = $branches->firstWhere('name', 'الصديق') ?? $branches->first();

        $namedCircles = collect([
            ['name' => 'حلقة أبو بكر الصديق', 'type' => 'individual'],
            ['name' => 'حلقة عمر بن الخطاب', 'type' => 'group'],
        ])->map(function ($data) use ($siddiqBranch) {
            return Circle::firstOrCreate(
                ['name' => $data['name']],
                [
                    'type'      => $data['type'],
                    'level'     => 'build',
                    'branch_id' => $siddiqBranch->id,
                ]
            );
        });

        $circles = $circles->concat($namedCircles)->unique('id')->values();
        // لو مفيش حلقات تانية غير دول، الـ Seeder برضه يشتغل عادي.

        // مشرفون = مستخدمين بدور supervisor (أو أي دور إداري متاح)
        $supervisors = User::role(['supervisor', 'manager', 'admin', 'general_manager'])
            ->inRandomOrder()
            ->limit(10)
            ->get();

        // أولياء أمور = مستخدمين بدور guardian
        $guardians = User::role('guardian')->inRandomOrder()->limit($this->count)->get();

        $surahs = Surah::query()->inRandomOrder()->limit(30)->get();

        // ─────────────────────────────────────────
        // 2) بيانات ثابتة للاختيار العشوائي منها (متوافقة مع خيارات الفورم)
        // ─────────────────────────────────────────

        $genders           = ['ذكر', 'أنثى'];
        $applicants         = ['الأم', 'الأب', 'الطالب نفسه'];
        $educationalStages  = ['تمهيدي', 'حضانة', 'ابتدائي', 'اعدادي', 'ثانوي', 'جامعي', 'خريج'];
        $educationTypes     = ['غير محدد', 'أزهري', 'عام (تربية وتعليم)'];
        $schoolGrades       = ['لا يوجد', 'الأول', 'الثاني', 'الثالث', 'الرابع', 'الخامس', 'السادس', 'دراسات عليا'];
        $readingLevels      = ['مبتدئ', 'مقبول', 'متمكن', 'متقن'];
        $centerEntryLevels  = ['construction', 'mastery', 'creativity'];
        $studySystems       = ['group', 'individual'];
        $healthStatuses     = ['طبيعية (الحمد الله)', 'ربو خفيف', 'حساسية موسمية'];
        $learningDiffs      = ['لا يوجد (الحمد الله)', 'صعوبة في التركيز', 'بطء تعلم بسيط'];
        $personalTraits     = ['هادئ', 'نشيط', 'خجول', 'اجتماعي', 'طموح'];
        $hobbiesPool        = ['كرة القدم', 'الكاراتيه', 'الرسم', 'البرمجة والألعاب الإلكترونية', 'الأشغال اليدوية', 'القراءة والإطلاع'];
        $exitStatuses       = ['بمفرده', 'مع ولي الأمر أو أحد الأقارب'];
        $statuses           = ['active', 'suspended', 'graduated']; // ⚠️ عدّل القيم دي لو enum عندك مختلف
        $newMemPlans        = ['5 سطور يومياً', 'وجه يومياً', 'ربع حزب يومياً'];
        $revisionPlans      = ['وجه يومياً', 'ربع حزب يومياً', 'نصف حزب يومياً'];
        $oldMemPlans        = ['حزب أسبوعياً', 'حزبين أسبوعياً', 'جزء أسبوعياً'];

        // ✅ أسماء عربية للطلاب (بدل fake()->name() الإنجليزي)
        $maleFirstNames = ['محمد', 'أحمد', 'عبدالله', 'يوسف', 'عمر', 'خالد', 'إبراهيم', 'حسن', 'مصطفى', 'زياد', 'كريم', 'عبدالرحمن', 'يحيى', 'طه', 'حمزة'];
        $femaleFirstNames = ['فاطمة', 'مريم', 'عائشة', 'نور', 'سارة', 'ملك', 'جنى', 'هنا', 'رقية', 'زينب', 'ياسمين', 'آية', 'روان', 'لجين', 'شهد'];
        $lastNamePool = ['السيد', 'إبراهيم', 'محمود', 'عبدالعزيز', 'الشريف', 'حسانين', 'عبدالغني', 'الجندي', 'صبري', 'رمضان', 'العدوي', 'قطب', 'الديب', 'حجازي', 'عزب'];

        $schoolNames = ['مدرسة النور الابتدائية', 'مدرسة الأمل الإعدادية', 'مدرسة الفاروق الثانوية', 'مدرسة المستقبل الخاصة', 'مدرسة الرسالة الأزهرية', 'مدرسة الشروق الابتدائية'];

        $arabicName = function (string $gender) use ($maleFirstNames, $femaleFirstNames, $lastNamePool) {
            $first = $gender === 'ذكر'
                ? fake()->randomElement($maleFirstNames)
                : fake()->randomElement($femaleFirstNames);

            // اسمين أب/جد بعد الاسم الأول لتقريب الصيغة الرباعية
            $middle1 = fake()->randomElement($maleFirstNames);
            $middle2 = fake()->randomElement($lastNamePool);

            return "{$first} {$middle1} {$middle2}";
        };

        for ($i = 0; $i < $this->count; $i++) {
            $center = $centers->random();
            $circle = $circles->isNotEmpty() ? $circles->random() : null;
            $supervisor = $supervisors->isNotEmpty() ? $supervisors->random() : null;

            // ولي أمر: يستخدم موجود لو فيه، أو يترك فارغ (نص الطلاب بدون ولي أمر كحالة واقعية)
            $guardian = $guardians->isNotEmpty() && $i % 4 !== 0
                ? $guardians->random()
                : null;

            $dob = fake()->dateTimeBetween('-16 years', '-4 years');
            $entryLevel = fake()->randomElement($centerEntryLevels);
            $studySystem = fake()->randomElement($studySystems);
            $gender = fake()->randomElement($genders);

            $student = Student::create([
                'name'                      => $arabicName($gender),
                'student_code'              => fake()->boolean(60) ? fake()->numerify(str_repeat('#', 14)) : null,
                'date_of_birth'             => $dob,
                'gender'                    => $gender,
                'address'                   => fake()->address(),
                'guardian_id'               => $guardian?->id,
                'status'                    => fake()->randomElement($statuses),
                'suspended_at'              => null,
                'circle_id'                 => $circle?->id,
                'applicant'                 => fake()->randomElement($applicants),
                'educational_stage'         => fake()->randomElement($educationalStages),
                'education_type'            => fake()->randomElement($educationTypes),
                'school_grade'              => fake()->randomElement($schoolGrades),
                'previous_school'           => fake()->randomElement($schoolNames),
                'center_entry_level'        => $entryLevel,
                'join_date'                 => fake()->dateTimeBetween('-3 years', 'now'),
                'whatsapp_number'           => '01' . fake()->numerify('#########'),
                'health_status'             => fake()->randomElement($healthStatuses),
                'notes'                     => fake()->boolean(30) ? fake()->sentence() : null,
                'supervisor_id'             => $supervisor?->id,
                'center_id'                 => $center->id,
                'whatsapp_owner'            => fake()->randomElement($applicants),
                'additional_contact_owner'  => fake()->boolean(50) ? fake()->randomElement($applicants) : null,
                'second_phone'              => fake()->boolean(50) ? '01' . fake()->numerify('#########') : null,
                'learning_difficulties'     => fake()->randomElement($learningDiffs),
                'personal_traits'           => fake()->randomElement($personalTraits),
                'hobbies'                   => json_encode(fake()->randomElements($hobbiesPool, rand(1, 3))),
                'reading'                   => fake()->randomElement($readingLevels),
                'student_exit_status'       => fake()->randomElement($exitStatuses),
                'decision'                  => null,
                'subscription_fees'         => fake()->randomElement([0, 50, 100, 150]),
                'received_tools'            => fake()->boolean(40),
            ]);

            // ✅ تفاصيل مستوى البناء (لو الطالب دخل بمستوى البناء)
            if ($entryLevel === 'construction') {
                StudentConstructionDetail::create([
                    'student_id'             => $student->id,
                    'circle_id'              => $circle?->id,
                    'study_system'           => $studySystem,
                    'current_surah_id'       => $studySystem === 'individual' && $surahs->isNotEmpty()
                        ? $surahs->random()->id
                        : null,
                    'new_memorization_plan'  => $studySystem === 'individual' ? fake()->randomElement($newMemPlans) : null,
                    'revision_plan'          => $studySystem === 'individual' ? fake()->randomElement($revisionPlans) : null,
                    'old_memorization_plan'  => $studySystem === 'individual' ? fake()->randomElement($oldMemPlans) : null,
                    'placement_evaluation'   => fake()->boolean(50) ? fake()->sentence() : null,
                ]);
            }

            // ✅ تفاصيل مستوى الإتقان (لو الطالب دخل بمستوى الإتقان)
            if ($entryLevel === 'mastery') {
                StudentItqanDetail::create([
                    'student_id'                    => $student->id,
                    'previous_memorization_side'    => fake()->randomElement(['نصف القرآن', 'القرآن كاملاً', 'عشرة أجزاء']),
                    'previous_khatamat_count'        => fake()->numberBetween(0, 5),
                    'current_review_amount'         => fake()->randomElement(['حزب يومياً', 'جزء يومياً']),
                    'self_evaluation'               => fake()->sentence(),
                    'tajweed_matn'                   => fake()->randomElement(['الجزرية', 'تحفة الأطفال', null]),
                    'desired_path'                   => fake()->randomElement(['التثبيت والمراجعة', 'الإجازة']),
                    'preferred_time'                 => fake()->randomElement(['عصراً', 'مساءً', 'بعد الفجر']),
                    'teacher_name'                    => $arabicName('ذكر'),
                    'itqan_details'                  => fake()->boolean(50) ? fake()->sentence() : null,
                ]);
            }
        }

        $this->command?->info("✅ تم إنشاء {$this->count} طالب تجريبي بنجاح.");
    }
}