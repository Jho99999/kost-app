<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Kost] Pemesanan Tidak Disetujui — {$this->booking->booking_code}"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.booking-rejected');
    }
}
