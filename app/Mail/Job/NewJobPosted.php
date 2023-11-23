<?php

namespace App\Mail\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewJobPosted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $data)
    {
        $this->data = $data;
        $this->data['APP_NAME'] = env('APP_NAME');
    }

    public function envelope()
    {
        return new Envelope(
            subject: sprintf("%s posted a new job on %s", $this->data['agency'], $this->data['APP_NAME'] )
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.job.new-job-posted-admin-alert',
        );
    }

    public function attachments()
    {
        return [];
    }
}