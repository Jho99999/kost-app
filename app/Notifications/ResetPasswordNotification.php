<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(
        public readonly string $token
    ) {}

    public function via(object $notifiable): array
    {
        return [];
    }
}