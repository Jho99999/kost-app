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
        /*
        |--------------------------------------------------------------------------
        | Admin Default
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Sample Rooms
        |--------------------------------------------------------------------------
        */

        $rooms = [

            [
                'name'             => 'Kamar 101',
                'room_number'      => '101',

                'type'             => 'Standard',

                'description'      => 'Kamar Standard yang nyaman dan bersih.',

                'price'            => 800000,
                'deposit'          => 800000,

                'status'           => 'available',

                'floor'            => 1,
                'capacity'         => 1,

                'length_m'         => 3,
                'width_m'          => 4,
                'size_sqm'         => 12,

                'bathroom_type'    => 'outside',
                'furnished'        => 'semi',
                'electricity_type' => 'included',
                'water_type'       => 'included',

                'facilities' => [
                    'WiFi',
                    'Kipas Angin',
                    'Lemari',
                ],

                'images' => [
                    'rooms/dummy-room.png',
                ],

                'cover_image' => 0,
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
                'images' => [],
                'cover_image' => 0,
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
                'images' => [],
                'cover_image' => 0,
            ],

            [
                'name'        => 'Kamar 201',
                'type'        => 'Deluxe',
                'price'       => 1200000,
                'floor'       => 2,
                'size_sqm'    => 16,
                'status'      => 'available',
                'description' => 'Kamar Deluxe dengan AC dan kamar mandi dalam.',
                'facilities'  => [
                    'WiFi',
                    'AC',
                    'Lemari',
                    'Kamar Mandi Dalam',
                ],
                'images' => [],
                'cover_image' => 0,
            ],

            [
                'name'        => 'Kamar 202',
                'type'        => 'Deluxe',
                'price'       => 1200000,
                'floor'       => 2,
                'size_sqm'    => 16,
                'status'      => 'available',
                'description' => 'Kamar Deluxe dengan AC dan kamar mandi dalam.',
                'facilities'  => [
                    'WiFi',
                    'AC',
                    'Lemari',
                    'Kamar Mandi Dalam',
                ],
                'images' => [],
                'cover_image' => 0,
            ],

            [
                'name'        => 'Kamar 301',
                'type'        => 'VIP',
                'price'       => 1800000,
                'floor'       => 3,
                'size_sqm'    => 20,
                'status'      => 'available',
                'description' => 'Kamar VIP lengkap dengan fasilitas premium.',
                'facilities'  => [
                    'WiFi',
                    'AC',
                    'TV',
                    'Lemari',
                    'Meja Kerja',
                    'Kamar Mandi Dalam',
                ],
                'images' => [],
                'cover_image' => 0,
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