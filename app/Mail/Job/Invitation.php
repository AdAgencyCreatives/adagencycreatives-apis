<?php

namespace App\Mail\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Invitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $data)
    {

    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Job Invitation',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.job.invitation',
        );
    }

    public function attachments()
    {
        return [];
    }
}
