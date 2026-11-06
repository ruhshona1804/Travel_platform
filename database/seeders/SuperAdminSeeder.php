<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/** Birinchi Super Admin .env dagi ADMIN_EMAIL / ADMIN_PASSWORD orqali yaratiladi (kodga yozilmaydi). */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('travel.admin.email');
        $password = config('travel.admin.password');
        if (! $email || ! $password) {
            $this->command?->warn("ADMIN_EMAIL va ADMIN_PASSWORD .env faylida ko'rsatilmagan — Super Admin yaratilmadi.");
            return;
        }
        if (strlen($password) < 10) {
            $this->command?->error("ADMIN_PASSWORD kamida 10 belgidan iborat bo'lishi kerak.");
            return;
        }
        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill([
            'name' => config('travel.admin.name'),
            'password' => $password,
            'role' => 'super_admin',
            'email_verified_at' => now(),
            'is_suspended' => false,
        ])->save();
        $this->command?->info("Super Admin tayyor: $email");
    }
}
