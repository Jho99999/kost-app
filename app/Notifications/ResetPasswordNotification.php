<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /** Token untuk reset password */
    public function __construct(public readonly string $token) {}

    /** Kirim via email */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /** Bangun email */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', $this->token) . '?email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Reset Password — ' . config('app.name'))
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
            ->action('Reset Password', $url)
            ->line('Tautan ini akan kedaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.')
            ->salutation('Salam, ' . config('app.name'));
    }
}
