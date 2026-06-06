<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property \Illuminate\Support\Carbon $date
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Center extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $level
 * @property int $max_students
 * @property string|null $notes
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $supervisor_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $assistantTeacher
 * @property-read int|null $assistant_teacher_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $mainTeacher
 * @property-read int|null $main_teacher_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \App\Models\Teacher|null $supervisor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $teachers
 * @property-read int|null $teachers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereMaxStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	class Circle extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 * @mixin \Eloquent
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string $gender
 * @property string|null $second_phone
 * @property string|null $address
 * @property int|null $guardian_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $suspended_at
 * @property int|null $circle_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $student_code
 * @property string|null $education_type
 * @property string|null $educational_stage
 * @property string|null $school_grade
 * @property string|null $previous_school
 * @property string|null $center_entry_level
 * @property \Illuminate\Support\Carbon|null $join_date
 * @property string|null $whatsapp_number
 * @property string|null $health_status
 * @property string|null $notes
 * @property int|null $supervisor_id
 * @property string|null $applicant
 * @property string|null $applicant_other
 * @property int|null $center_id
 * @property string|null $whatsapp_owner
 * @property string|null $whatsapp_owner_other
 * @property string|null $additional_contact_owner
 * @property string|null $additional_contact_owner_other
 * @property string|null $learning_difficulties
 * @property string|null $personal_traits
 * @property array<array-key, mixed>|null $hobbies
 * @property string|null $reading
 * @property string|null $exit_details
 * @property string|null $student_exit_status
 * @property string|null $decision
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \App\Models\Center|null $center
 * @property-read Circle|null $circle
 * @property-read StudentConstructionDetail|null $constructionDetail
 * @property-read int $overdue_months_count
 * @property-read float $suspended_past_debt
 * @property-read User|null $guardian
 * @property-read StudentIbdaDetail|null $ibdaDetail
 * @property-read StudentItqanDetail|null $itqanDetail
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAdditionalContactOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAdditionalContactOwnerOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereApplicant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereApplicantOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCenterEntryLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCircleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereEducationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereEducationalStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereExitDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereHealthStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereHobbies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereJoinDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereLearningDifficulties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student wherePersonalTraits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student wherePreviousSchool($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereReading($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSchoolGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSecondPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereStudentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereStudentExitStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereWhatsappNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereWhatsappOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereWhatsappOwnerOther($value)
 * @mixin \Eloquent
 */
	class Student extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property string|null $current_surah
 * @property string|null $study_system
 * @property string|null $group_name
 * @property string|null $new_memorization_plan
 * @property string|null $placement_evaluation
 * @property string|null $old_memorization_plan
 * @property string|null $old_memorization_plan_other
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereCurrentSurah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereNewMemorizationPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereOldMemorizationPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereOldMemorizationPlanOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail wherePlacementEvaluation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereStudySystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class StudentConstructionDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property string|null $previous_licenses_and_chains
 * @property string|null $desired_narration_and_path
 * @property string|null $preferred_time
 * @property string|null $supervisor_name
 * @property string|null $ibda_details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereDesiredNarrationAndPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereIbdaDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail wherePreferredTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail wherePreviousLicensesAndChains($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereSupervisorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class StudentIbdaDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property string|null $previous_memorization_side
 * @property int|null $previous_khatamat_count
 * @property string|null $current_review_amount
 * @property string|null $self_evaluation
 * @property string|null $tajweed_matn
 * @property string|null $tajweed_matn_other
 * @property string|null $memorized_texts
 * @property string|null $desired_path
 * @property string|null $preferred_time
 * @property string|null $teacher_name
 * @property string|null $itqan_details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereCurrentReviewAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereDesiredPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereItqanDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereMemorizedTexts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail wherePreferredTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail wherePreviousKhatamatCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail wherePreviousMemorizationSide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereSelfEvaluation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereTajweedMatn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereTajweedMatnOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereTeacherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class StudentItqanDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property int $circle_id
 * @property int|null $collected_by
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $month
 * @property string $status
 * @property string|null $payment_method
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Circle $circle
 * @property-read \App\Models\User|null $collectedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCircleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCollectedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $circle_level
 * @property string $education_level
 * @property string|null $school_grade
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereCircleLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereEducationLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereSchoolGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPrice withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	class SubscriptionPrice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Circle> $circles
 * @property-read int|null $circles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	class Teacher extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $mobile
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $collectedSubscriptions
 * @property-read int|null $collected_subscriptions_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
 * @property-read int|null $students_count
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

