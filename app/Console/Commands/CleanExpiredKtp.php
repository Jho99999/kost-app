<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanExpiredKtp extends Command
{
    protected $signature   = 'ktp:clean-expired';
    protected $description = 'Hapus foto KTP pengguna yang masa sewanya sudah habis';

    public function handle(): int
    {
        // Cari booking yang sudah lewat check_out (check_in + duration)
        // dan masih berstatus 'approved' atau 'active'
        $expiredBookings = Booking::query()
            ->whereIn('status', ['approved', 'active'])
            ->where(DB::raw('DATE_ADD(check_in_date, INTERVAL duration_months MONTH)'), '<', now()->toDateString())
            ->with('user')
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('Tidak ada booking kedaluwarsa yang perlu dibersihkan.');
            return self::SUCCESS;
        }

        $ktpDeleted = 0;
        $bookingsCompleted = 0;

        foreach ($expiredBookings as $booking) {
            // 1. Update status booking jadi 'completed'
            $booking->update(['status' => 'completed']);
            $bookingsCompleted++;

            // 2. Hapus KTP user dari storage
            $user = $booking->user;
            if ($user && $user->ktp_image) {
                Storage::disk('public')->delete($user->ktp_image);
                $user->update([
                    'ktp_image'       => null,
                    'ktp_uploaded_at' => null,
                ]);
                $ktpDeleted++;
                $this->line("  ✓ KTP {$user->name} ({$user->email}) dihapus.");
            }
        }

        $this->info("Selesai! {$bookingsCompleted} booking ditandai selesai, {$ktpDeleted} KTP dihapus.");

        return self::SUCCESS;
    }
}
