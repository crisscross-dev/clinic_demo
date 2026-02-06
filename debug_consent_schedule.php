<?php

/**
 * Debug script for testing consent schedule system
 * Run this via SSH on Hostinger: php debug_consent_schedule.php
 */

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ConsentSchedule;
use App\Models\PatientInfo;
use Carbon\Carbon;

echo "========================================\n";
echo "CONSENT SCHEDULE DEBUG SCRIPT\n";
echo "========================================\n";
echo "Current Server Time: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";

// 1. Check active schedules
echo "--- ACTIVE SCHEDULES ---\n";
$schedules = ConsentSchedule::where('is_active', true)->get();

if ($schedules->isEmpty()) {
    echo "❌ No active schedules found!\n\n";
} else {
    echo "✓ Found {$schedules->count()} active schedule(s):\n";
    foreach ($schedules as $schedule) {
        echo "\n  Schedule ID: {$schedule->id}\n";
        echo "  Department: " . ($schedule->department ?? 'All') . "\n";
        echo "  Start: {$schedule->start_time->format('Y-m-d H:i:s')}\n";
        echo "  End: {$schedule->end_time->format('Y-m-d H:i:s')}\n";
        echo "  Status: " . ($schedule->is_active ? 'Active' : 'Inactive') . "\n";

        $now = Carbon::now();
        if ($now->between($schedule->start_time, $schedule->end_time)) {
            echo "  → SHOULD BE UNLOCKING NOW ✓\n";
        } elseif ($now->greaterThan($schedule->end_time)) {
            echo "  → SHOULD BE LOCKED (ended) ✓\n";
        } else {
            echo "  → WAITING (not started yet)\n";
        }
    }
    echo "\n";
}

// 2. Check patient info consent_form status
echo "--- PATIENT CONSENT STATUS ---\n";
$totalPatients = PatientInfo::count();
$unlockedCount = PatientInfo::where('consent_form', 0)->count();
$lockedCount = PatientInfo::where('consent_form', 1)->count();

echo "Total Patients: {$totalPatients}\n";
echo "Unlocked Forms (consent_form = 0): {$unlockedCount}\n";
echo "Locked Forms (consent_form = 1): {$lockedCount}\n\n";

// 3. Check by department
echo "--- CONSENT STATUS BY DEPARTMENT ---\n";
$departments = PatientInfo::select('department')
    ->distinct()
    ->whereNotNull('department')
    ->pluck('department');

foreach ($departments as $dept) {
    $total = PatientInfo::where('department', $dept)->count();
    $unlocked = PatientInfo::where('department', $dept)->where('consent_form', 0)->count();
    $locked = PatientInfo::where('department', $dept)->where('consent_form', 1)->count();

    echo "{$dept}: {$unlocked} unlocked / {$locked} locked (Total: {$total})\n";
}
echo "\n";

// 4. Check Laravel scheduler status
echo "--- SCHEDULER STATUS ---\n";
echo "Run this command to test scheduler:\n";
echo "  php artisan consent:process-schedules\n\n";

// 5. Check cron job
echo "--- CRON JOB VERIFICATION ---\n";
echo "To verify cron job is running, check:\n";
echo "1. SSH: crontab -l\n";
echo "2. Laravel logs: tail -f storage/logs/laravel.log\n";
echo "3. Cron log (if enabled): cat ~/cron.log\n\n";

// 6. Test the command manually
echo "--- RUNNING CONSENT COMMAND NOW ---\n";
echo "Executing: php artisan consent:process-schedules\n\n";

use Illuminate\Support\Facades\Artisan;

Artisan::call('consent:process-schedules');
echo Artisan::output();

echo "\n========================================\n";
echo "DEBUG COMPLETE\n";
echo "========================================\n";
