<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'name' => 'Sudip',
                'mobile' => '01712960833',
                'email' => 'palash.sudip@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'account_type' => 'Admin',
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'name' => 'User',
                'mobile' => '01712000001',
                'email' => 'user@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'account_type' => 'Admin',
                'status' => 'Active',
                'created_at' => now(),
            ]
        ]);
    }
}
