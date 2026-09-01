<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        return ['mail'];
    }

    public function toMail($notifiable)
{
    $url = url(route("password.reset", [
        "token" => $this->token,
        "email" => $notifiable->getEmailForPasswordReset(),
    ], false));

    return (new MailMessage)
        ->subject(__("Bookie - Reset Password Notification"))
        ->greeting(__("haiii !!"))
        ->line(__('we heard that you want to reset your password :( dont worry i got u, here is your link'))
        ->action(__('reset password'), $url)
        ->line(__("this link will die in 60 minutes, be quick !"))
        ->line(__("if u didnt request this link, just ignore it :p"))
        ->salutation(__("with love,\nBookie Team"));
}
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
