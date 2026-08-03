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
                'name' => 'Rina Sari',
                'email' => 'rina@kost.test',
                'phone' => '081234567811',
                'occupation' => 'Mahasiswa',
                'gender' => 'P',
                'religion' => 'Islam',
                'role' => 'user',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@kost.test',
                'phone' => '081234567812',
                'occupation' => 'Karyawan',
                'gender' => 'L',
                'religion' => 'Kristen',
                'role' => 'user',
            ],
            [
                'name' => 'Siti Nurfadilah',
                'email' => 'siti@kost.test',
                'phone' => '081234567813',
                'occupation' => 'Freelancer',
                'gender' => 'P',
                'religion' => 'Islam',
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    ...$user,
                    'password' => Hash::make('password123'),
                ]
            );
        }

        $this->command->info('✔ Sample users seeded.');
    }
}
