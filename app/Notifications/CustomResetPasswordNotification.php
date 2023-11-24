<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(sprintf('Your %s password reset request', env('APP_NAME')))
            ->view('emails.account.reset_password', [
                'url' => sprintf('%s/reset-password?token=%s&email=%s', env('FRONTEND_URL'), $this->token, $notifiable->email),
                'userName' => $notifiable->first_name ?? $notifiable->username,
            ]);
    }
}
