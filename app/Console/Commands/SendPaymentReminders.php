<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminder;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature   = 'payments:send-reminders';
    protected $description = 'Kirim email reminder kepada penyewa H-7 sebelum jatuh tempo tagihan';

    public function handle(): int
    {
        $targetDate = now()->addDays(7)->toDateString();

        $payments = Payment::query()
            ->where('status', 'pending')
            ->whereDate('due_date', $targetDate)
            ->whereNull('proof_image')   // hanya yang belum upload bukti
            ->with(['user', 'booking.room'])
            ->get();

        if ($payments->isEmpty()) {
            $this->info("Tidak ada tagihan jatuh tempo pada {$targetDate}.");
            return self::SUCCESS;
        }

        $sent   = 0;
        $failed = 0;

        foreach ($payments as $payment) {
            try {
                Mail::to($payment->user->email)->send(new PaymentReminder($payment));
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                $this->warn("Gagal kirim ke {$payment->user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Reminder terkirim: {$sent} | Gagal: {$failed} | Total: {$payments->count()}");

        return self::SUCCESS;
    }
}
