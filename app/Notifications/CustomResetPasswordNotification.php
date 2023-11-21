<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject(sprintf('Your %s password reset request', config('app.name')))
            ->view('emails.account.reset_password', [
                'url' => sprintf('%s?token=%s&email=%s', config('app.frontend_reset_password_url'), $this->token, $notifiable->email),
                'userName' => $notifiable->first_name ?? $notifiable->username,
            ]);
    }
}
