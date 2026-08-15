<?php

use App\Jobs\CalculateUnpaidMonths;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Bus;

Schedule::command('notify:sequential-absences')->dailyAt('17:30')->withoutOverlapping();

Schedule::command('notify:unpaid-subscriptions')->dailyAt('09:00');

Schedule::call(function () {
    Bus::dispatchSync(new CalculateUnpaidMonths());
})
    ->name('calculate-unpaid-months')
    ->dailyAt('02:00')
    ->withoutOverlapping();
