<?php

namespace App\Mail\JobPostExpiring;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobPostExpiringAgency extends Mailable
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
            subject: sprintf('Your %s listing on %s expires soon.', $this->data['job_title'], $this->data['APP_NAME']),
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.job.job_post_expiring_soon_agency',
        );
    }

    public function attachments()
    {
        return [];
    }
}