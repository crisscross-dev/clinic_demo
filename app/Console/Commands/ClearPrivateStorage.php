<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File; // Import File facade

class ClearPrivateStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-private-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all private storage folders like patient uploads and signatures';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // List of private folders you want to delete
        $folders = [
            storage_path('app/private/patient_uploads'),
            storage_path('app/private/signatures'),
        ];

        foreach ($folders as $folder) {
            if (File::exists($folder)) {
                File::deleteDirectory($folder);
                $this->info("Deleted: {$folder}");
            } else {
                $this->warn("Not found: {$folder}");
            }
        }

        $this->info('All selected private storage folders cleared.');
    }
}
