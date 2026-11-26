<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Demo accounts para sa presentation.
     *   Admin   : admin@attendance.test    Password: admin123
     *   Teacher : teacher@attendance.test  Password: teacher2026
     * Ilisan ang password kon gamiton sa tuod nga klase.
     */
    public function run()
    {
        $this->account('admin@attendance.test', 'Admin', 'admin123', true);
        $this->account('teacher@attendance.test', 'Teacher Ana', 'teacher2026', false);
    }

    private function account(string $email, string $name, string $password, bool $admin): void
    {
        $u = User::firstOrNew(['email' => $email]);
        $u->name = $name;
        $u->password = Hash::make($password);
        $u->is_admin = $admin;      // not mass-assignable, set directly
        $u->is_active = true;
        $u->save();
    }
}
