<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMailService
{
    public function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $html
    ): void {

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'api-key' => env('BREVO_API_KEY'),
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [

            'sender' => [
                'name'  => config('mail.from.name'),
                'email' => config('mail.from.address'),
            ],

            'to' => [[
                'email' => $toEmail,
                'name'  => $toName,
            ]],

            'subject' => $subject,
            'htmlContent' => $html,
        ]);

        $response->throw();
    }
}