<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Notifications\SequentialAbsenceNotification;
use Illuminate\Console\Command;

class NotifySequentialAbsences extends Command
{
    protected $signature = 'notify:sequential-absences
                            {--force : Force send even if already notified today}
                            {--dry-run : Simulate without sending}
                            {--days=30 : Number of days to look back}
                            {--min-absences=2 : Minimum absence days to trigger}';

    protected $description = 'Send notifications to guardians for students with sequential absences';

    public function handle(): int
    {
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');
        $days = (int) $this->option('days');
        $minAbsences = (int) $this->option('min-absences');
        $sent = 0;
        $skipped = 0;
        $noGuardian = 0;

        $students = Student::with([
            'attendances' => fn($q) => $q->orderBy('date', 'desc')->take($days),
            'guardian',
            'circle'
        ])
            ->where('status', '!=', 'متوقف')
            ->whereHas('guardian')
            ->get();

        $this->info("Checking {$students->count()} students for sequential absences (last {$days} days, min {$minAbsences} absences)...");
        $this->newLine();

        $bar = $this->output->createProgressBar($students->count());
        $bar->start();

        foreach ($students as $student) {
            $bar->advance();

            if (!$this->hasSequentialAbsencePattern($student)) {
                continue;
            }

            $absenceDays = $student->attendances->where('status', 'absent')->count();

            if ($absenceDays < $minAbsences) {
                continue;
            }

            $guardian = $student->guardian;

            if (!$guardian) {
                $noGuardian++;
                continue;
            }

            if (!$force && $this->alreadyNotifiedToday($guardian, $student)) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->newLine();
                $this->line("  [DRY-RUN] Would notify: {$student->name} → {$guardian->name} ({$absenceDays} absence days)");
                $sent++;
                continue;
            }

            try {
                $guardian->notify(new SequentialAbsenceNotification($student, $absenceDays));
                $sent++;
                $this->newLine();
                $this->info("  ✓ Sent: {$student->name} → {$guardian->name}");
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  ✗ Failed: {$student->name} - {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine(2);

        $label = $dryRun ? 'Would send' : 'Sent';
        $this->info("📊 Summary:");
        $this->line("   {$label}: {$sent}");
        $this->line("   Skipped (already notified today): {$skipped}");
        $this->line("   No guardian: {$noGuardian}");

        return 0;
    }

    private function hasSequentialAbsencePattern(Student $student): bool
    {
        $records = $student->attendances->sortBy('date')->values();
        $statuses = $records->pluck('status')->toArray();
        $count = count($statuses);

        if ($count < 2) return false;

        // Two consecutive absences
        for ($i = 0; $i < $count - 1; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 1] === 'absent') {
                return true;
            }
        }

        // Absent with one day gap
        for ($i = 0; $i < $count - 2; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 2] === 'absent') {
                return true;
            }
        }

        // Three absences in any 5-day window
        for ($i = 0; $i <= $count - 3; $i++) {
            $window = array_slice($statuses, $i, 5);
            $absences = collect($window)->filter(fn($s) => $s === 'absent')->count();
            if ($absences >= 3) return true;
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
