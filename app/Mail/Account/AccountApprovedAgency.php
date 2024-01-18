<?php

namespace App\Mail\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AccountApprovedAgency extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    public function __construct($user)
    {
        $this->data['user'] = $user;
        $this->data['APP_NAME'] = env('APP_NAME');
        $this->data['FRONTEND_URL'] = env('FRONTEND_URL');

    }

    public function envelope()
    {
        return new Envelope(
            subject: sprintf('Your %s account has been activated', $this->data['APP_NAME']),
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.account.approved-agency',
        );
    }

    public function attachments()
    {
        return [];
    }
}