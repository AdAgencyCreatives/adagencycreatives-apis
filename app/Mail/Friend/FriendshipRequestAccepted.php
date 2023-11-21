<?php

namespace App\Mail\Friend;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FriendshipRequestAccepted extends Mailable
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
            subject: sprintf('You and %s are now connected on %s', $this->data['member'], $this->data['APP_NAME']),
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.friendship.request_accepted',
        );
    }

    public function attachments()
    {
        return [];
    }
}