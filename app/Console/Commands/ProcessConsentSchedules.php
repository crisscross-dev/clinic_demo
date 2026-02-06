<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConsentSchedule;
use App\Models\PatientInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessConsentSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consent:process-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process consent form schedules - unlock/lock forms based on schedule';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ensure we're using the application timezone
        $now = Carbon::now(config('app.timezone'));
        $this->info("Processing consent schedules at {$now->format('Y-m-d H:i:s')} (" . config('app.timezone') . ")");

        // Log to Laravel log file for debugging
        Log::info("Processing consent schedules at {$now->format('Y-m-d H:i:s')} (" . config('app.timezone') . ")");

        // Get all active schedules
        $schedules = ConsentSchedule::where('is_active', true)->get();

        if ($schedules->isEmpty()) {
            $this->info('No active schedules found.');
            Log::info('No active schedules found.');
            return 0;
        }

        $this->info("Found {$schedules->count()} active schedule(s)");
        Log::info("Found {$schedules->count()} active schedule(s)");

        $unlocked = 0;
        $locked = 0;

        foreach ($schedules as $schedule) {
            // Parse times in the application timezone
            $startTime = Carbon::parse($schedule->start_time, config('app.timezone'));
            $endTime = Carbon::parse($schedule->end_time, config('app.timezone'));

            $this->info("Schedule ID {$schedule->id}: Start={$startTime->format('Y-m-d H:i:s')}, End={$endTime->format('Y-m-d H:i:s')}, Dept={$schedule->department}");
            Log::info("Schedule ID {$schedule->id}: Start={$startTime->format('Y-m-d H:i:s')}, End={$endTime->format('Y-m-d H:i:s')}, Dept={$schedule->department}");

            // Check if we're currently within the schedule window
            if ($now->between($startTime, $endTime)) {
                // Unlock forms for this department
                $query = PatientInfo::query();
                if ($schedule->department) {
                    $query->where('department', $schedule->department);
                }
                // Debug: log patient IDs to be updated
                $ids = $query->pluck('id')->toArray();
                Log::info('Unlocking consent_form for patient IDs:', $ids);
                $count = count($ids);
                $updated = 0;
                if ($count > 0) {
                    $updated = PatientInfo::whereIn('id', $ids)->update(['consent_form' => 0]); // 0 = unlocked
                }
                $unlocked += $updated;
                $deptText = $schedule->department ?? 'All departments';
                $this->info("✓ Unlocked {$updated} forms for {$deptText}");
                Log::info("Unlocked {$updated} forms for {$deptText}");
            }
            // Check if schedule has ended
            elseif ($now->greaterThan($endTime)) {
                // Lock forms for this department
                $query = PatientInfo::query();
                if ($schedule->department) {
                    $query->where('department', $schedule->department);
                }
                // Debug: log patient IDs to be updated
                $ids = $query->pluck('id')->toArray();
                Log::info('Locking consent_form for patient IDs:', $ids);
                $count = count($ids);
                $updated = 0;
                if ($count > 0) {
                    $updated = PatientInfo::whereIn('id', $ids)->update(['consent_form' => 1]); // 1 = locked
                }
                $locked += $updated;
                // Deactivate this schedule since it's completed
                $schedule->is_active = false;
                $schedule->save();
                $deptText = $schedule->department ?? 'All departments';
                $this->info("✓ Locked {$updated} forms for {$deptText} (schedule ended)");
                Log::info("Locked {$updated} forms for {$deptText} (schedule ended)");
            }
            // Schedule hasn't started yet
            else {
                $deptText = $schedule->department ?? 'All departments';
                $this->info("⏳ Waiting for schedule to start for {$deptText} at {$startTime->format('Y-m-d H:i')}");
                Log::info("Waiting for schedule to start for {$deptText} at {$startTime->format('Y-m-d H:i')}");
            }
        }

        $this->info("\nSummary: Unlocked {$unlocked} | Locked {$locked}");
        Log::info("Summary: Unlocked {$unlocked} | Locked {$locked}");
        return 0;
    }
}
