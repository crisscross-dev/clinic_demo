<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentAccount;
use Carbon\Carbon;

class DeleteUnapprovedStudents extends Command
{
    protected $signature = 'students:cleanup-unapproved';
    protected $description = 'Delete student accounts whose patient_info is unapproved or missing, and last login is over 30 days ago.';

    public function handle()
    {
        $cutoff = Carbon::now()->subDays(30);

        // Find students with either:
        // 1. No patient info (never approved)
        // 2. Or patient info still pending/rejected
        // AND last login 30+ days ago OR never logged in
        $unapprovedStudents = StudentAccount::where(function ($query) {
            $query->whereDoesntHave('patientInfo')
                ->orWhereHas('patientInfo', function ($pi) {
                    $pi->whereIn('status', ['pending', 'rejected']);
                });
        })
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_login_at')
                    ->orWhere('last_login_at', '<=', $cutoff);
            })
            ->get();

        if ($unapprovedStudents->isEmpty()) {
            $this->info('✅ No unapproved or inactive student accounts found for deletion.');
            return;
        }

        $count = $unapprovedStudents->count();

        foreach ($unapprovedStudents as $student) {
            $student->delete();
        }

        $this->info("🧹 Deleted {$count} unapproved student accounts inactive for over 30 days.");
    }
}


// php artisan students:cleanup-unapproved