# Database Schema Summary

## `users`

- `id`
- `name`
- `email`
- `mobile`
- `email_verified_at`
- `password`
- `status`
- `remember_token`
- `created_at`
- `updated_at`

## `circles`

- `id`
- `name`
- `type`
- `level`
- `max_students`
- `supervisor_id`
- `notes`
- `is_active`
- `created_at`
- `updated_at`

## `teachers`

- `id`
- `name`
- `user_id`
- `created_at`
- `updated_at`

## `circle_teacher`

- `circle_id`
- `teacher_id`
- `role`
- `created_at`
- `updated_at`

## `students`

- `suspended_at`
- `circle_id` 
- `current_surah`
- `enrollment_date`
- `created_at`
- `updated_at`

## `attendances`

- `id`
- `student_id`
- `date`
- `status`
- `notes`
- `created_at`
- `updated_at`

## `subscriptions`

- `id`
- `student_id`
- `circle_id`
- `collected_by`
- `amount`
- `month`
- `status`
- `payment_method`
- `paid_at`
- `notes`
- `created_at`
- `updated_at`

## `subscription_prices`

- `id`
- `circle_level`
- `education_level`
- `amount`
- `created_at`
- `updated_at`
