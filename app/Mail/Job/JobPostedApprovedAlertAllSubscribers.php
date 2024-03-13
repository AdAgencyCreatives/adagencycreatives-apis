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
        $this->data = $data;
        $this->data['APP_NAME'] = env('APP_NAME');
        $this->data['APP_URL'] = env('FRONTEND_URL');

        $this->user = $user;
    }

    public function envelope()
    {
        return new Envelope(
            // subject: sprintf('New Job Posted in "%s" category', $this->data['category'], $this->data['APP_NAME']),
            subject: sprintf('New Job Opportunity'),
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
