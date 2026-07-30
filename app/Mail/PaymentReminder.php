<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Kost] Pengingat Tagihan — Jatuh Tempo 7 Hari Lagi ({$this->payment->payment_code})"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-reminder');
    }
}
