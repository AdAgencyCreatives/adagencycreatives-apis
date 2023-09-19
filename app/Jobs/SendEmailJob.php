<?php

namespace App\Jobs;

use App\Mail\AccountApproved;
use App\Mail\NewUserRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    protected $emailType;

    public function __construct($data, $emailType)
    {
        $this->data = $data;
        $this->emailType = $emailType;
    }

    public function handle()
    {

        switch ($this->emailType) {
            case 'new_user_registration':
                Mail::to($this->data['receiver'])->send(new NewUserRegistration($this->data['data']));
                break;
            case 'account_approved':
                Mail::to($this->data['receiver'])->send(new AccountApproved($this->data['data']));
                break;

            default:
                // Handle unknown email types or fallback logic
                break;
        }
    }
}
