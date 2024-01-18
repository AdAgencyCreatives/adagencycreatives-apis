<?php

namespace App\Mail\CustomPkg;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HireAnAdvisorJobCompleted extends Mailable
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
            subject: sprintf("%s HIRE AN ADVISOR JOB COMPLETED", $this->data['agency_name'],)
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.custom-pkg.hire-an-advisor-job-completed',
        );
    }

    public function attachments()
    {
        return [];
    }
}