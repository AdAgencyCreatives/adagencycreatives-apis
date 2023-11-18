<?php

namespace App\Mail\Application;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Interested extends Mailable
{
    use Queueable, SerializesModels;

   public $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->data['APP_NAME'] = env('APP_NAME');
        $this->data['APP_URL'] = env('FRONTEND_URL');
    }


    public function envelope()
    {
        return new Envelope(
            subject: sprintf('%s application status update %s,', $this->data['APP_NAME'], $this->data['applicant']),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.application.interested',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}