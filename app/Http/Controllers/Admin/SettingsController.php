<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        $defaultOutput = htmlspecialchars(
'Checking 14 students for sequential absences...
  [DRY-RUN] Would notify محمد أحمد الصالح → محمد السيد الشعراوي (4 absence days)
  [DRY-RUN] Would notify عبدالرحمن علي يوسف → محمد السيد الشعراوي (4 absence days)
  [DRY-RUN] Would notify يوسف إبراهيم عبدالكريم → محمد السيد الشعراوي (4 absence days)
  [DRY-RUN] Would notify عمر خالد عفرات → محمد السيد الشعراوي (4 absence days)
  [DRY-RUN] Would notify عثمان عفان النور → محمد السيد الشعراوي (2 absence days)
  [DRY-RUN] Would notify علي عمر خالد → محمد السيد الشعراوي (3 absence days)
  [DRY-RUN] Would notify حسن محمد أحمد → محمد السيد الشعراوي (2 absence days)
  [DRY-RUN] Would notify أبوعبيدة أحمد السيد  → محمد السيد الشعراوي (3 absence days)
  [DRY-RUN] Would notify سيف عبدالله عمرو → محمد السيد الشعراوي (2 absence days)
  [DRY-RUN] Would notify عمرو السيد عبدالسميع → محمد السيد الشعراوي (2 absence days)
Done. Would send: 10, Skipped (already notified today): 0');

        return view('admin.settings.notifications', compact('settings', 'defaultOutput'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tracking_start' => 'required|in:14:00,15:00,16:00',
            'tracking_end'   => 'required|in:17:00,18:00,19:00',
            'notify_time'    => 'required|date_format:H:i',
        ]);

        Setting::setValue('tracking_start', $validated['tracking_start']);
        Setting::setValue('tracking_end', $validated['tracking_end']);
        Setting::setValue('notify_time', $validated['notify_time']);

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function dryRun()
    {
        Artisan::call('app:notify-sequential-absences', [
            '--dry-run' => true,
            '--force'   => true,
        ]);

        $output = Artisan::output();

        return redirect()->route('admin.settings.index')
            ->with('dry_run_output', nl2br(e($output)));
    }

    public function forceSend()
    {
        Artisan::call('app:notify-sequential-absences', [
            '--force' => true,
        ]);

        $output = Artisan::output();

        return redirect()->route('admin.settings.index')
            ->with('success', nl2br(e($output)));
    }

    public function dryRunJson(): JsonResponse
    {
        Artisan::call('app:notify-sequential-absences', [
            '--dry-run' => true,
            '--force'   => true,
        ]);

        return response()->json([
            'output' => Artisan::output(),
        ]);
    }

    public function forceSendJson(): JsonResponse
    {
        Artisan::call('app:notify-sequential-absences', [
            '--force' => true,
        ]);

        $output = Artisan::output();

        return response()->json([
            'output'   => $output,
            'sent'     => (int) preg_match('/Sent: (\d+)/', $output, $m) ? $m[1] : 0,
            'skipped'  => (int) preg_match('/Skipped.*?: (\d+)/', $output, $m) ? $m[1] : 0,
        ]);
    }
}
