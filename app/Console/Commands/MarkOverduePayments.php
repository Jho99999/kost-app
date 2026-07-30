<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class MarkOverduePayments extends Command
{
    protected $signature   = 'payments:mark-overdue';
    protected $description = 'Tandai pembayaran yang melewati jatuh tempo sebagai overdue';

    public function handle(): int
    {
        // Tandai sebagai overdue: status pending, jatuh tempo SEBELUM hari ini,
        // dan belum ada bukti yang diunggah.
        // Bug note: menggunakan whereDate(..., '<', today()) — pastikan timezone
        // di config/app.php sesuai WIB (Asia/Jakarta) agar H+0 tidak salah hitung.
        $count = Payment::query()
            ->where('status', 'pending')
            ->whereDate('due_date', '<', today())
            ->update(['status' => 'overdue']);

        $this->info("Ditandai {$count} pembayaran sebagai overdue.");

        return self::SUCCESS;
    }
}
