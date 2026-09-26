<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Normal User
        User::create([
            'first_name' => 'User',
            'last_name' => 'Test',
            'email' => 'user@user.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => Carbon::now(),
        ]);

        // Admin User
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => Carbon::now(),
        ]);
    }
}
