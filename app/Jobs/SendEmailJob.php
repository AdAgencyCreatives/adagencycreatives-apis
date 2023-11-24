<?php

namespace App\Jobs;

use App\Mail\Account\AccountApproved;
use App\Mail\Account\AccountDenied;
use App\Mail\Account\NewUserRegistrationAgency;
use App\Mail\Account\NewUserRegistrationCreative;
use App\Mail\Application\ApplicationSubmitted;
use App\Mail\Application\Interested;
use App\Mail\Application\NewApplication;
use App\Mail\Application\Removed;
use App\Mail\Friend\FriendshipRequest;
use App\Mail\Friend\FriendshipRequestAccepted;
use App\Mail\Group\Invitation;
use App\Mail\Job\CustomJobRequestRejected;
use App\Mail\Job\Invitation as JobInvitation;
use App\Mail\Job\JobPostedApprovedAlertAllSubscribers;
use App\Mail\Job\NewJobPosted;
use App\Mail\Message\UnreadMessage;
use App\Mail\Order\ConfirmationAdmin;
use App\Models\User;
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

    protected $adminEmail;
    protected $devEmails;

    protected $additionalEmails;

    public function __construct($data, $emailType)
    {
        $this->data = $data;
        $this->emailType = $emailType;

        $this->adminEmail = User::where('email', env('ADMIN_EMAIL'))->first();
        $this->devEmails = explode(',', env('CC_EMAILS'));

        $realUserEmailTypes = [
            'account_approved',
            'account_denied',

            'agency_is_interested',

            'job_invitation',
            'friendship_request_sent',
            'friendship_request_accepted'
        ];

        // Check if the current email type is in the array and update the receiver's email
        if (!in_array($this->emailType, $realUserEmailTypes)) {
            $this->data['receiver'] = $this->adminEmail;
        }
        //$this->data['receiver'] = $this->adminEmail;

    }

    public function handle()
    {
        switch ($this->emailType) {
            /**
             * Account
             */
            case 'new_user_registration_creative_role':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new NewUserRegistrationCreative($this->data['data']));
                break;
            case 'new_user_registration_agency_role':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new NewUserRegistrationAgency($this->data['data']));
                break;
            case 'account_approved':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new AccountApproved($this->data['data']));
                break;
            case 'account_denied':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new AccountDenied($this->data['data']));
                break;

                /**
                 * Group
                 */
            case 'group_invitation':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new Invitation($this->data['data']));
                break;
            case 'order_confirmation':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new ConfirmationAdmin($this->data['data']));
                break;
            case 'job_approved_alert_all_subscribers':
                $email_data = $this->data['email_data'];
                $subscribers = $this->data['subscribers'];
                foreach ($subscribers as $subscriber) {
                    // Mail::to($subscriber->user->email)->send(new JobPostedApprovedAlertAllSubscribers($email_data, $subscriber->user));
                    Mail::to($this->adminEmail)->bcc($this->devEmails)->send(new JobPostedApprovedAlertAllSubscribers($email_data, $subscriber->user));
                }
                break;

                /**
                 * Job
                 */
            case 'new_job_added_admin': // To inform the admin that a new job has been added
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new NewJobPosted($this->data['data']));
                break;
            case 'job_invitation':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new JobInvitation($this->data['data']));
                break;
            case 'custom_job_request_rejected':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new CustomJobRequestRejected($this->data['data']));
                break;

                /**
                 * Application
                 */
            case 'application_submitted':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new ApplicationSubmitted($this->data['data'])); // To the applicant
                break;
            case 'new_candidate_application':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new NewApplication($this->data['data'])); // To the Agency
                break;
            case 'application_removed_by_agency':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new Removed($this->data['data'])); // To the applicant
                break;
            case 'agency_is_interested':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new Interested($this->data['data'])); // To the applicant
                break;

                /**
                 * Friend
                 */
            case 'friendship_request_sent':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new FriendshipRequest($this->data['data']));
                break;
            case 'friendship_request_accepted':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new FriendshipRequestAccepted($this->data['data']));
                break;

                /**
                 * Message Count
                 */
            case 'unread_message':
                Mail::to($this->data['receiver'])->bcc($this->devEmails)->send(new UnreadMessage($this->data['data']));
                break;

            default:
                // Handle unknown email types or fallback logic
                break;
        }
    }
}
