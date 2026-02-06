<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'prefix'    => 'Dr.',
            'username'  => 'Demo@gmail.com',
            'password'  => hash::make('Password@123'), // 🔐 always hash
            'lastname'  => 'Argawanon',
            'firstname' => 'Criss',
            'middlename' => 'Cross',
            'role'      => 'admin',
            'email'     => 'Demo@gmail.com',
        ]);

        Admin::create([
            'prefix'    => 'Dr.',
            'username'  => 'cris@gmail.com',
            'password'  => hash::make('crisscross@123'), // 🔐 always hash
            'lastname'  => 'Argawanon',
            'firstname' => 'Cris',
            'middlename' => 'Catarata',
            'role'      => 'admin',
            'email'     => 'cris@gmail.com',
        ]);
    }
}

// php artisan db:seed --class=AdminSeeder

// criscatarata@gmail.com
// Password@123