<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            [
                'email' => 'admin@kost.com',
            ],
            [
                'name'       => 'Administrator',
                'password'   => Hash::make('admin123'),
                'phone'      => '081234567890',
                'role'       => 'admin',
                'occupation' => 'Administrator',
                'gender'     => 'L',
                'religion'   => 'Islam',
            ]
        );

        $this->command->info('✔ Admin seeded.');
    }
}