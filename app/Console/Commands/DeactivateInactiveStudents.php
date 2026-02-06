<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentAccount;
use Carbon\Carbon;

class DeactivateInactiveStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:deactivate-inactive'; // 👈 match this to Kernel.php

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate student accounts that have been inactive for 2 years or more.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $twoYearsAgo = Carbon::now()->subYears(2);

        // Find students whose last_login_at is older than 2 years or NULL
        $students = StudentAccount::where(function ($query) use ($twoYearsAgo) {
            $query->where('last_login_at', '<', $twoYearsAgo)
                ->orWhereNull('last_login_at');
        })
            ->where('status', 'active')
            ->get();

        if ($students->isEmpty()) {
            $this->info('✅ No inactive students found.');
            return;
        }

        // Update all matching students to inactive
        $count = $students->count();
        StudentAccount::whereIn('id', $students->pluck('id'))
            ->update(['status' => 'inactive']);

        $this->info("🟠 Deactivated {$count} inactive student account(s).");
    }
}


// php artisan students:deactivate-inactive