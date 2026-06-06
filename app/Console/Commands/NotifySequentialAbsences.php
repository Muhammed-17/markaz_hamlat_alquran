<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Notifications\SequentialAbsenceNotification;
use Illuminate\Console\Command;

class NotifySequentialAbsences extends Command
{
    protected $signature = 'app:notify-sequential-absences
        {--force : Send even if already notified today}
        {--dry-run : Preview students that would be notified without actually sending}';

    protected $description = 'Detect sequential absence patterns and notify guardians';

    public function handle(): int
    {
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');
        $sent = 0;
        $skipped = 0;

        $students = Student::with(['attendances' => function ($q) {
            $q->orderBy('date', 'desc')->take(30);
        }, 'guardian', 'circle'])
            ->where('status', '!=', 'inactive')
            ->whereHas('guardian')
            ->get();

        $this->info('Checking ' . $students->count() . ' students for sequential absences...');

        foreach ($students as $student) {
            if (!$this->hasSequentialAbsencePattern($student)) {
                continue;
            }

            $absenceDays = $student->attendances->where('status', 'absent')->count();
            $guardian = $student->guardian;

            if (!$guardian) {
                $this->warn("Skipping student {$student->name}: No linked guardian found in users table.");
                continue;
            }

            if (!$force && $this->alreadyNotifiedToday($guardian, $student)) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY-RUN] Would notify {$student->name} → {$guardian->name} ({$absenceDays} absence days)");
                $sent++;
                continue;
            }

            $guardian->notify(new SequentialAbsenceNotification($student, $absenceDays));
            $sent++;

            $this->line("  Sent notification for: {$student->name} → {$guardian->name}");
        }

        $label = $dryRun ? 'Would send' : 'Sent';
        $this->info("Done. {$label}: {$sent}, Skipped (already notified today): {$skipped}");

        return 0;
    }

    private function hasSequentialAbsencePattern(Student $student): bool
    {
        $records = $student->attendances->sortBy('date')->values();
        $statuses = $records->pluck('status')->toArray();

        // Condition 1: two or more consecutive absences
        for ($i = 0; $i < count($statuses) - 1; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 1] === 'absent') {
                return true;
            }
        }

        // Condition 2: absent → (not absent) → absent
        for ($i = 0; $i < count($statuses) - 2; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 2] === 'absent') {
                return true;
            }
        }

        return false;
    }

    private function alreadyNotifiedToday($guardian, Student $student): bool
    {
        $search = '"student_id":' . $student->id;

        return $guardian->notifications()
            ->where('type', SequentialAbsenceNotification::class)
            ->whereDate('created_at', today())
            ->where('data', 'like', '%' . $search . '%')
            ->exists();
    }
}
