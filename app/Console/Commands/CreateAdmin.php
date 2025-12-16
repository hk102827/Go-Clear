<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email} {password}';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::create([
            'name' => 'Admin',
            'email' => $email,
            'phone' => '+1234567890',
            'password' => Hash::make($password),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->info('Admin user created successfully!');
        $this->info('Email: ' . $email);
    }
}