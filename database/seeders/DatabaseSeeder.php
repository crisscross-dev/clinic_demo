<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\PatientInfoSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(AdminSeeder::class);
        $this->call(\Database\Seeders\StudentAccountSeeder::class);
        $this->call(PatientInfoSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(\Database\Seeders\InventoryItemSeeder::class);
    }
}
// php artisan db:seed --class=PatientInfoSeeder