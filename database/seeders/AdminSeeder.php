<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@econfirm.test');
        $password = env('ADMIN_PASSWORD', 'change-me-now');
        $name = env('ADMIN_NAME', 'Admin');

        if (Admin::query()->where('email', $email)->exists()) {
            return;
        }

        Admin::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }
}
