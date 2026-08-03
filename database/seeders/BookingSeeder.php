<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tenant = User::where('role', 'user')->first();
        $room = Room::where('status', 'available')->first();

        if (! $admin || ! $tenant || ! $room) {
            $this->command->warn('Skipping bookings seeding: missing admin, user, or room data.');
            return;
        }

        Booking::updateOrCreate(
            ['booking_code' => 'BKG-2026-0001'],
            [
                'user_id' => $tenant->id,
                'room_id' => $room->id,
                'check_in_date' => now()->toDateString(),
                'duration_months' => 3,
                'monthly_price' => $room->price,
                'total_price' => $room->price * 3,
                'status' => 'pending',
                'notes' => 'Pengajuan booking untuk demo aplikasi.',
                'approved_by' => null,
            ]
        );

        $approvedRoom = Room::where('status', 'occupied')->first();
        $approvedTenant = User::where('role', 'user')->skip(1)->first();

        if ($approvedRoom && $approvedTenant) {
            Booking::updateOrCreate(
                ['booking_code' => 'BKG-2026-0002'],
                [
                    'user_id' => $approvedTenant->id,
                    'room_id' => $approvedRoom->id,
                    'check_in_date' => now()->subMonth()->toDateString(),
                    'duration_months' => 6,
                    'monthly_price' => $approvedRoom->price,
                    'total_price' => $approvedRoom->price * 6,
                    'status' => 'approved',
                    'approved_by' => $admin->id,
                    'notes' => 'Booking sudah disetujui admin.',
                ]
            );
        }

        $this->command->info('✔ Sample bookings seeded.');
    }
}
