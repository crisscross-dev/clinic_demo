<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentAccount;

class StudentAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 50 random student accounts by default — adjust as needed.
        StudentAccount::factory()
        ->count(100)
        ->state(['status' => 'inactive'])
        ->create();
    }
}

//  php artisan db:seed --class=StudentAccountSeeder