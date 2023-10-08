<?php

namespace App\Mail\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CustomJobRequestRejected extends Mailable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    public function __construct($user)
    {
        $this->data['user'] = $user;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Custom Package Request Rejected',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.job.custom-package-request-rejected',
        );
    }

    public function attachments()
    {
        return [];
    }
}
