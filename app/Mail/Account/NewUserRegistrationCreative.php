<?php

namespace App\Mail\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserRegistrationCreative extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $user = $data['user'];

        $this->data['user'] = $user;
        $this->data['profile_url'] = url("/users/{$user->id}/details");
        $this->data['link'] = $data['url'];
        $this->data['APP_NAME'] = env('APP_NAME');
        $this->data['APP_URL'] = env('APP_URL');
        $this->data['APPROVE_URL'] = route('user.activate', ['uuid' => $user->uuid]);
        $this->data['DENY_URL'] = route('user.deactivate', ['uuid' => $user->uuid]);
    }

    public function envelope()
    {
        return new Envelope(
            subject: sprintf('%s registration request', config('app.name'))
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.account.new_user_registration_creative',
        );
    }

    public function attachments()
    {
        return [];
    }
}