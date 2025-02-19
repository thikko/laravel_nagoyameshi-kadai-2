<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'taro.tanaka@example.com'],
            [
                'name' => '田中 太郎',
                'kana' => 'タナカ タロウ',
                'password' => Hash::make('password123'),
                'postal_code' => '1234567',
                'address' => '1234567 長野県飯田市松尾',
                'phone_number' => '090-1234-1234',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
    }
}
