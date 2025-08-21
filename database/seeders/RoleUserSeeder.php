<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@tracktern.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        // Create a student user
        User::create([
            'name' => 'Student User',
            'email' => 'student@tracktern.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STUDENT,
            'email_verified_at' => now(),
        ]);

        // Create additional test users
        User::create([
            'name' => 'John Coordinator',
            'email' => 'coordinator@tracktern.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Student',
            'email' => 'jane.student@tracktern.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STUDENT,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Mark Student',
            'email' => 'mark.student@tracktern.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STUDENT,
            'email_verified_at' => now(),
        ]);
    }
}
