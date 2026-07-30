<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentVerified extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Kost] Pembayaran Dikonfirmasi — Bulan ke-{$this->payment->month_number} ({$this->payment->payment_code})"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-verified');
    }
}
