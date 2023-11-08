<?php

namespace App\Mail\Application;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewApplication extends Mailable
{
    use Queueable, SerializesModels;

   public $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->data['APP_NAME'] = env('APP_NAME');
    }


    public function envelope()
    {
        return new Envelope(
            subject: sprintf('You have a new applicant for your %s role! %s,', $this->data['job_title'], $this->data['applicant']->first_name),
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
            view: 'emails.application.new_to_the_agency',
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