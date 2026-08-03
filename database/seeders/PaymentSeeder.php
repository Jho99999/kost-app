<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $pendingBooking = Booking::where('status', 'pending')->first();
        $approvedBooking = Booking::where('status', 'approved')->first();
        $bankMethod = PaymentMethod::where('type', 'bank')->first();

        if ($pendingBooking) {
            Payment::updateOrCreate(
                ['payment_code' => 'PAY-2026-0001'],
                [
                    'booking_id' => $pendingBooking->id,
                    'user_id' => $pendingBooking->user_id,
                    'amount' => $pendingBooking->total_price,
                    'due_date' => now()->addDay()->toDateString(),
                    'payment_date' => null,
                    'month_period' => now()->translatedFormat('F Y'),
                    'status' => 'pending',
                    'payment_method' => 'transfer',
                    'payment_method_id' => $bankMethod?->id,
                    'month_number' => now()->month,
                    'paid_at' => null,
                    'proof_image' => null,
                    'verified_by' => null,
                    'notes' => 'Tagihan awal untuk booking yang sedang menunggu persetujuan.',
                    'verified_at' => null,
                ]
            );
        }

        if ($approvedBooking) {
            Payment::updateOrCreate(
                ['payment_code' => 'PAY-2026-0002'],
                [
                    'booking_id' => $approvedBooking->id,
                    'user_id' => $approvedBooking->user_id,
                    'amount' => $approvedBooking->total_price,
                    'due_date' => now()->subDay()->toDateString(),
                    'payment_date' => now()->toDateString(),
                    'month_period' => now()->translatedFormat('F Y'),
                    'status' => 'paid',
                    'payment_method' => 'transfer',
                    'payment_method_id' => $bankMethod?->id,
                    'month_number' => now()->month,
                    'paid_at' => now()->toDateString(),
                    'proof_image' => null,
                    'verified_by' => $admin?->id,
                    'notes' => 'Pembayaran sudah diterima dan diverifikasi.',
                    'verified_at' => now(),
                ]
            );
        }

        $this->command->info('✔ Sample payments seeded.');
    }
}
