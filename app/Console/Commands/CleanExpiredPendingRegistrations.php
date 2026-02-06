<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PendingRegistration;
use Carbon\Carbon;

class CleanExpiredPendingRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pending:clean-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all expired pending registrations (token_expires_at in the past).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $expired = PendingRegistration::whereNotNull('token_expires_at')
            ->where('token_expires_at', '<', $now)
            ->get();

        if ($expired->isEmpty()) {
            $this->info('✅ No expired pending registrations found.');
            return;
        }

        $count = $expired->count();
        PendingRegistration::whereIn('id', $expired->pluck('id'))->delete();
        $this->info("🟠 Deleted {$count} expired pending registration(s).");
    }
}

// php artisan pending:clean-expired
