<?php

namespace App\Mail\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProfileCompletionCreative extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;

        $this->data['APP_NAME'] = env('APP_NAME');
        $this->data['APP_URL'] = env('APP_URL');

        $this->data['FRONTEND_URL'] = env('FRONTEND_URL');
    }

    public function envelope()
    {
        return new Envelope(
            subject: sprintf('New message from %s', config('app.name'))
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.account.profile_completion_creative',
        );
    }

    public function attachments()
    {
        return [];
    }
}
