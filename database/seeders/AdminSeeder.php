<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────
        // Admin Default
        // ─────────────────────────────────────────────────────────

        User::firstOrCreate(
            ['email' => 'admin@kost.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('admin123'),
                'phone'    => '081234567890',
                'role'     => 'admin',
            ]
        );

        $this->command->info('✔ Admin seeded.');

        // ─────────────────────────────────────────────────────────
        // Data Sample Kamar
        // ─────────────────────────────────────────────────────────

        $rooms = [

            [
                'name'        => 'Kamar 101',
                'type'        => 'Standard',
                'price'       => 800000,
                'floor'       => 1,
                'size_sqm'    => 12,
                'status'      => 'available',
                'description' => 'Kamar Standard yang nyaman dan bersih.',
                'facilities'  => [
                    'WiFi',
                    'Kipas Angin',
                    'Lemari',
                ],
            ],

            [
                'name'        => 'Kamar 102',
                'type'        => 'Standard',
                'price'       => 800000,
                'floor'       => 1,
                'size_sqm'    => 12,
                'status'      => 'available',
                'description' => 'Kamar Standard yang nyaman dan bersih.',
                'facilities'  => [
                    'WiFi',
                    'Kipas Angin',
                    'Lemari',
                ],
            ],

            [
                'name'        => 'Kamar 103',
                'type'        => 'Standard',
                'price'       => 800000,
                'floor'       => 1,
                'size_sqm'    => 12,
                'status'      => 'available',
                'description' => 'Kamar Standard yang nyaman dan bersih.',
                'facilities'  => [
                    'WiFi',
                    'Kipas Angin',
                    'Lemari',
                ],
            ],

            [
                'name'        => 'Kamar 201',
                'type'        => 'Deluxe',
                'price'       => 1200000,
                'floor'       => 2,
                'size_sqm'    => 16,
                'status'      => 'available',
                'description' => 'Kamar Deluxe yang nyaman dan bersih.',
                'facilities'  => [
                    'WiFi',
                    'AC',
                    'Lemari',
                    'Kamar Mandi Dalam',
                ],
            ],

            [
                'name'        => 'Kamar 202',
                'type'        => 'Deluxe',
                'price'       => 1200000,
                'floor'       => 2,
                'size_sqm'    => 16,
                'status'      => 'available',
                'description' => 'Kamar Deluxe yang nyaman dan bersih.',
                'facilities'  => [
                    'WiFi',
                    'AC',
                    'Lemari',
                    'Kamar Mandi Dalam',
                ],
            ],

            [
                'name'        => 'Kamar 301',
                'type'        => 'VIP',
                'price'       => 1800000,
                'floor'       => 3,
                'size_sqm'    => 20,
                'status'      => 'available',
                'description' => 'Kamar VIP yang nyaman dan bersih.',
                'facilities'  => [
                    'WiFi',
                    'AC',
                    'Lemari',
                    'Kamar Mandi Dalam',
                    'TV',
                    'Meja Kerja',
                ],
            ],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(
                [
                    'name' => $room['name'],
                ],
                $room
            );
        }

        $this->command->info('✔ Sample rooms seeded.');
    }
}