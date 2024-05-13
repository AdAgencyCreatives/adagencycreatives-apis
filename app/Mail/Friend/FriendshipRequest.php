<?php

namespace App\Mail\Friend;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FriendshipRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->data['APP_NAME'] = env('APP_NAME');
        $this->data['FRONTEND_URL'] = env('FRONTEND_URL');
    }

    public function envelope()
    {
        return new Envelope(
            // subject: sprintf('%s wants to be friends on %s', $this->data['inviter'], $this->data['APP_NAME']),
            // subject: sprintf('%s wants to be friends on %s', "Someone", $this->data['APP_NAME']),
            subject: sprintf('Pending Friend Alert on %s', $this->data['APP_NAME']),
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.friendship.request',
        );
    }

    public function attachments()
    {
        return [];
    }
}