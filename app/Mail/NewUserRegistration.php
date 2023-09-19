<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserRegistration extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($user)
    {
        $this->data['user'] = $user;
        $this->data['profile_url'] = url("/users/{$user->id}/details");
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Important Registration Request: '.$this->data['user']->username,
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.account.new_user_registration',
        );
    }

    public function attachments()
    {
        return [];
    }
}
