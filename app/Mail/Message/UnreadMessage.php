<?php

namespace App\Mail\Message;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnreadMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->data['APP_NAME'] = env('APP_NAME');
        $this->data['APP_URL'] = env('APP_URL');
        $this->data['FRONTEND_URL'] = env('FRONTEND_URL') . "/job-messages";
    }

    public function envelope()
    {
        return new Envelope(
            subject: sprintf('You have a new message waiting for you on %s', $this->data['APP_NAME']),
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.message.unread',
        );
    }

    public function attachments()
    {
        return [];
    }
}
