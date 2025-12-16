<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@goclear.com',
            'phone' => '+923001234567',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}