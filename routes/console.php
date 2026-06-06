<?php

use App\Console\Commands\NotifySequentialAbsences;
use App\Models\Setting;
use Illuminate\Support\Facades\Schedule;

$notifyTime = Setting::getValue('notify_time', '17:30');

Schedule::command('app:notify-sequential-absences')
    ->dailyAt($notifyTime)
    ->withoutOverlapping();
