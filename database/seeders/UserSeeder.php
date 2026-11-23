<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Demo account para sa presentation.
     * Email: admin@attendance.test  Password: admin123
     * Ilisan ang password kon gamiton sa tuod nga klase.
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@attendance.test'],
            ['name' => 'Admin', 'password' => Hash::make('admin123')]
        );
    }
}
