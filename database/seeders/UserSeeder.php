<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Petugas 1',
                'email' => 'petugas1@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Petugas 2',
                'email' => 'petugas2@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Petugas 3',
                'email' => 'petugas3@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
