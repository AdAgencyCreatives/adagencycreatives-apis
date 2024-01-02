<?php

namespace App\Mail\JobPostExpiring;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobPostExpiringAdmin extends Mailable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
            subject: sprintf('The %s opening at %s expires tomorrow. %s', $this->data['job_title'],$this->data['agency_name'], $this->data['APP_NAME']),
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.job.job_post_expiring_soon_admin',
        );
    }

    public function attachments()
    {
        return [];
    }
}