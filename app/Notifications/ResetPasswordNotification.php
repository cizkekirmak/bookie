<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return [];
    }

    public function send($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $htmlContent = '
        <div style="background-color: #f4f7f4; padding: 30px; font-family: sans-serif;">
            <div style="max-width: 500px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; border: 1px solid #e0e0e0;">
                <h2 style="color: #2d5a27; margin-top: 0;">' . e(__('haiii !!')) . '</h2>
                <p style="color: #444; font-size: 15px; line-height: 1.5;">' . e(__('we heard that you want to reset your password :( dont worry i got u, here is your link')) . '</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $url . '" style="background-color: #2d5a27; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 15px;">' . e(__('reset password')) . '</a>
                </div>
                <p style="color: #666; font-size: 14px;">' . e(__('this link will die in 60 minutes, be quick !')) . '</p>
                <p style="color: #666; font-size: 14px;">' . e(__('if u didnt request this link, just ignore it :p')) . '</p>
                <p style="color: #2d5a27; font-size: 14px; margin-top: 25px; white-space: pre-line; font-weight: bold;">' . e(__('with love,\nBookie Team')) . '</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">
                <p style="color: #999; font-size: 12px; word-break: break-all;">
                    ' . e(__('If you\'re having trouble clicking the "reset password" button, copy and paste the URL below into your web browser:')) . '<br>
                    <a href="' . $url . '" style="color: #2d5a27;">' . $url . '</a>
                </p>
            </div>
        </div>';

        $response = Http::withHeaders([
            'api-key' => env('BREVO_KEY'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => env('MAIL_FROM_NAME', 'Bookie'),
                'email' => env('MAIL_FROM_ADDRESS', 'bookieapp.info@gmail.com'),
            ],
            'to' => [
                ['email' => $notifiable->getEmailForPasswordReset()]
            ],
            'subject' => __('Bookie - Reset Password Notification'),
            'htmlContent' => $htmlContent,
        ]);

        if (!$response->successful()) {
            Log::error('Brevo API Mail Error: ' . $response->body());
        }
    }
}