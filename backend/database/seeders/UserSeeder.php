<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'username' => 'Admin ActiveCourt',
            'email' => 'admin@activecourt.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now()->subMonth(),
        ]);

        // Users
        $users = [
            ['username' => 'Raka Pratama', 'email' => 'raka@example.com'],
            ['username' => 'Nadia Putri', 'email' => 'nadia@example.com'],
            ['username' => 'Dimas Arya', 'email' => 'dimas@example.com'],
        ];

        foreach ($users as $index => $user) {
            User::create([
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => now()->subDays(18 - ($index * 3)),
            ]);
        }
    }
}