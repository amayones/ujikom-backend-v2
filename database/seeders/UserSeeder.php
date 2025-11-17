<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Customer Test',
                'email' => 'customer@test.com',
                'password' => Hash::make('password'),
                'role' => 'customer'
            ],
            [
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ],
            [
                'name' => 'Owner Test',
                'email' => 'owner@test.com',
                'password' => Hash::make('password'),
                'role' => 'owner'
            ],
            [
                'name' => 'Cashier Test',
                'email' => 'cashier@test.com',
                'password' => Hash::make('password'),
                'role' => 'cashier'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}