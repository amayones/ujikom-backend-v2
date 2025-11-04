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
                'role' => 'customer',
                'phone' => '081234567890'
            ],
            [
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567891'
            ],
            [
                'name' => 'Owner Test',
                'email' => 'owner@test.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081234567892'
            ],
            [
                'name' => 'Cashier Test',
                'email' => 'cashier@test.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'phone' => '081234567893'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}