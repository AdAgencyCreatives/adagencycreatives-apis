<?php

namespace App\Mail\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobPostedApprovedAlertAllSubscribers extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $data, public $user)
    {

    }

    public function envelope()
    {
        return new Envelope(
            subject: sprintf('New Job Posted in "%s" category', $this->data['category']),
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.job.new-job-alert',
        );
    }

    public function attachments()
    {
        return [];
    }
}
