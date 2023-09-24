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
        //
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'New Job Posted: Review and Approval Needed',
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
