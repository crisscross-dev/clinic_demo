<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatientInfo;

class PatientInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     PatientInfo::factory()
    //         ->count(100)
    //         ->state(['status' => 'pending'])
    //         ->create();
    // }
    public function run(): void
    {
        // Create additional approved student patients
        PatientInfo::factory()
            ->count(100)
            ->state(['consent_access_requested' => '1'])
            ->create();
    }
}
// php artisan db:seed --class=PatientInfoSeeder