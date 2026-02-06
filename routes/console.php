<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule consent form processing every minute
Schedule::command('consent:process-schedules')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
