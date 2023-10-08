<?php

namespace App\Jobs;

use App\Mail\AccountApproved;
use App\Mail\Group\Invitation;
use App\Mail\Job\CustomJobRequestRejected;
use App\Mail\Job\Invitation as JobInvitation;
use App\Mail\Job\JobPostedApprovedAlertAllSubscribers;
use App\Mail\Job\NewJobPosted;
use App\Mail\NewUserRegistration;
use App\Mail\Order\ConfirmationAdmin;
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
            case 'group_invitation':
                Mail::to($this->data['receiver'])->send(new Invitation($this->data['data']));
                break;
            case 'order_confirmation':
                Mail::to($this->data['receiver'])->send(new ConfirmationAdmin($this->data['data']));
                break;
            case 'job_approved_alert_all_subscribers':
                $email_data = $this->data['email_data'];
                $subscribers = $this->data['subscribers'];
                foreach ($subscribers as $subscriber) {
                    Mail::to($subscriber->user->email)->send(new JobPostedApprovedAlertAllSubscribers($email_data, $subscriber->user));
                }
                break;
            case 'new_job_added_admin':
                Mail::to($this->data['receiver'])->send(new NewJobPosted($this->data['data']));
                break;
            case 'job_invitation':
                Mail::to($this->data['receiver'])->send(new JobInvitation($this->data['data']));
                break;
            case 'custom_job_request_rejected':
                Mail::to($this->data['receiver'])->send(new CustomJobRequestRejected($this->data['data']));
                break;
            default:
                // Handle unknown email types or fallback logic
                break;
        }
    }
}
