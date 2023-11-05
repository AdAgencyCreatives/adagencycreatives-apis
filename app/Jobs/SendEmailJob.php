<?php

namespace App\Jobs;

use App\Mail\Account\AccountApproved;
use App\Mail\Account\AccountDenied;
use App\Mail\Account\NewUserRegistrationAgency;
use App\Mail\Account\NewUserRegistrationCreative;
use App\Mail\Application\ApplicationSubmitted;
use App\Mail\Friend\FriendshipRequest;
use App\Mail\Friend\FriendshipRequestAccepted;
use App\Mail\Group\Invitation;
use App\Mail\Job\CustomJobRequestRejected;
use App\Mail\Job\Invitation as JobInvitation;
use App\Mail\Job\JobPostedApprovedAlertAllSubscribers;
use App\Mail\Job\NewJobPosted;
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
            /**
             * Account
             */
            case 'new_user_registration_creative_role':
                Mail::to($this->data['receiver'])->send(new NewUserRegistrationCreative($this->data['data']));
                break;
            case 'new_user_registration_agency_role':
                Mail::to($this->data['receiver'])->send(new NewUserRegistrationAgency($this->data['data']));
                break;
            case 'account_approved':
                Mail::to($this->data['receiver'])->send(new AccountApproved($this->data['data']));
                break;
            case 'account_denied':
                Mail::to($this->data['receiver'])->send(new AccountDenied($this->data['data']));
                break;

                /**
                 * Group
                 */
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

                /**
                 * Job
                 */
            case 'new_job_added_admin':
                Mail::to($this->data['receiver'])->send(new NewJobPosted($this->data['data']));
                break;
            case 'job_invitation':
                Mail::to($this->data['receiver'])->send(new JobInvitation($this->data['data']));
                break;
            case 'custom_job_request_rejected':
                Mail::to($this->data['receiver'])->send(new CustomJobRequestRejected($this->data['data']));
                break;


                /**
                 * Application
                */
            case 'application_submitted':
                Mail::to($this->data['receiver'])->send(new ApplicationSubmitted($this->data['data']));
                break;


                /**
                 * Friend
                 */
            case 'friendship_request_sent':
                Mail::to($this->data['receiver'])->send(new FriendshipRequest($this->data['data']));
                break;
            case 'friendship_request_accepted':
                Mail::to($this->data['receiver'])->send(new FriendshipRequestAccepted($this->data['data']));
                break;


            default:
                // Handle unknown email types or fallback logic
                break;
        }
    }
}
