<?php

namespace App\Jobs;

use App\Mail\Account\AccountApproved;
use App\Mail\Account\AccountApprovedAgency;
use App\Mail\Account\AccountDenied;
use App\Mail\Account\NewUserRegistrationAgency;
use App\Mail\Account\NewUserRegistrationCreative;
use App\Mail\Account\ProfileCompletionAgencyReminder;
use App\Mail\Account\ProfileCompletionCreativeReminder;
use App\Mail\Application\ApplicationSubmitted;
use App\Mail\Application\Interested;
use App\Mail\Application\JobClosed;
use App\Mail\Application\NewApplication;
use App\Mail\Application\Removed;
use App\Mail\ContactFormMail;
use App\Mail\ContentUpdated\EmailUpdated;
use App\Mail\CustomPkg\HireAnAdvisorJobCompleted;
use App\Mail\CustomPkg\RequestAdminAlert;
use App\Mail\ErrorNotificationMail;
use App\Mail\Friend\FriendshipRequest;
use App\Mail\Friend\FriendshipRequestAccepted;
use App\Mail\Group\Invitation;
use App\Mail\Job\CustomJobRequestRejected;
use App\Mail\Job\Invitation as JobInvitation;
use App\Mail\Job\JobPostedApprovedAlertAllSubscribers;
use App\Mail\Job\NewJobPosted;
use App\Mail\Job\NoJobPostedAgencyReminder;
use App\Mail\JobPostExpiring\JobPostExpiringAdmin;
use App\Mail\JobPostExpiring\JobPostExpiringAgency;
use App\Mail\Message\UnreadMessage;
use App\Mail\Order\ConfirmationAdmin;
use App\Mail\Post\LoungeMention;
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


    public function __construct($data, $emailType)
    {
        $this->data = $data;
        $this->emailType = $emailType;

        $this->adminEmail = User::where('email', env('ADMIN_EMAIL'))->first();
        $this->devEmails = explode(',', env('CC_EMAILS'));

        $realUserEmailTypes = [
            'account_approved_agency',
            'account_approved',
            'account_denied',

            'agency_is_interested',

            'job_invitation',
            'friendship_request_sent',
            'friendship_request_accepted',

            'unread_message',

            'job_expiring_soon_admin',
            'job_expiring_soon_agency',
            'job_closed_email',

            'email_updated',

            'user_mentioned_in_post',

            'application_submitted',

            'hire-an-advisor-job-completed',

            'job_approved_alert_all_subscribers',
            'application_removed_by_agency',
            'new_candidate_application',
            'profile_completion_creative',
            'profile_completion_agency',
            'no_job_posted_agency_reminder',
            'error_notification',

            'contact_us_inquiry',
        ];

        // Check if the current email type is in the array and update the receiver's email
        if (!in_array($this->emailType, $realUserEmailTypes)) {
            $this->data['receiver'] = $this->adminEmail;
        }
        //$this->data['receiver'] = $this->adminEmail;

    }

    private function sendEmail($receiver, $bcc = [], $mailable)
    {
        $final_bcc = array_values(array_diff($bcc, is_array($receiver) ? $receiver : [$receiver]));
        Mail::to($receiver)->bcc($final_bcc)->send($mailable);
    }

    public function handle()
    {
        switch ($this->emailType) {
            /**
             * Account
             */
            case 'new_user_registration_creative_role':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new NewUserRegistrationCreative($this->data['data']));
                break;
            case 'new_user_registration_agency_role':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new NewUserRegistrationAgency($this->data['data']));
                break;
            case 'account_approved_agency':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new AccountApprovedAgency($this->data['data']));
                break;
            case 'account_approved':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new AccountApproved($this->data['data']));
                break;
            case 'account_denied':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new AccountDenied($this->data['data']));
                break;

            /**
             * Group
             */
            case 'group_invitation':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new Invitation($this->data['data']));
                break;
            case 'order_confirmation':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new ConfirmationAdmin($this->data['data']));
                break;
            case 'job_approved_alert_all_subscribers':
                $email_data = $this->data['email_data'];
                $subscribers = $this->data['subscribers'];

                if (env('APP_ENV') == 'production') {
                    //only on production emails goes to real users otherwise admin email
                    foreach ($subscribers as $subscriber) {
                        // dd($subscriber->user?->full_name);
                        $subscriber = $subscriber->user;
                        $recipient = $subscriber->email;
                        $this->sendEmail($recipient, $this->devEmails, new JobPostedApprovedAlertAllSubscribers($email_data, $subscriber));
                    }
                } else {
                    // on staging / non-production only 1 email goes to admin for confirmation of alert
                    $this->sendEmail($this->adminEmail, $this->devEmails, new JobPostedApprovedAlertAllSubscribers($email_data, $this->adminEmail));
                }

                break;

            /**
             * Job
             */
            case 'new_job_added_admin': // To inform the admin that a new job has been added
                $this->sendEmail($this->data['receiver'], $this->devEmails, new NewJobPosted($this->data['data']));
                break;
            case 'job_invitation':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new JobInvitation($this->data['data']));
                break;
            // case 'custom_job_request_rejected':
            //     $this->sendEmail($this->data['receiver'], $this->devEmails, new CustomJobRequestRejected($this->data['data']));
            //     break;

            case 'custom_pkg_request_admin_alert':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new RequestAdminAlert($this->data['data']));
                break;
            case 'hire-an-advisor-job-completed':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new HireAnAdvisorJobCompleted($this->data['data']));
                break;

            /**
             * Application
             */
            case 'application_submitted':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new ApplicationSubmitted($this->data['data'])); // To the applicant
                break;
            case 'new_candidate_application':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new NewApplication($this->data['data'])); // To the Agency
                break;
            case 'application_removed_by_agency':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new Removed($this->data['data'])); // To the applicant
                break;
            case 'agency_is_interested':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new Interested($this->data['data'])); // To the applicant
                break;

            case 'job_closed_email':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new JobClosed($this->data['data'])); // To the applicant
                break;

            /**
             * Friend
             */
            case 'friendship_request_sent':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new FriendshipRequest($this->data['data']));
                break;
            case 'friendship_request_accepted':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new FriendshipRequestAccepted($this->data['data']));
                break;

            /**
             * Message Count
             */
            case 'unread_message':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new UnreadMessage($this->data['data']));
                break;

            case 'contact_us_inquiry':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new ContactFormMail($this->data['data']));
                break;

            /**
             * Job Post Expiring Soon
             */
            case 'job_expiring_soon_admin':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new JobPostExpiringAdmin($this->data['data']));
                break;

            case 'job_expiring_soon_agency':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new JobPostExpiringAgency($this->data['data']));
                break;

            case 'email_updated':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new EmailUpdated($this->data['data']));
                break;


            /**
             * Group Post
             */
            case 'user_mentioned_in_post':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new LoungeMention($this->data['data']));
                break;

            case 'profile_completion_creative':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new ProfileCompletionCreativeReminder($this->data['data']));
                break;

            case 'profile_completion_agency':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new ProfileCompletionAgencyReminder($this->data['data']));
                break;

            case 'no_job_posted_agency_reminder':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new NoJobPostedAgencyReminder($this->data['data']));
                break;

            case 'error_notification':
                $this->sendEmail($this->data['receiver'], $this->devEmails, new ErrorNotificationMail($this->data['data']));
                break;
            default:
                // Handle unknown email types or fallback logic
                break;
        }
    }
}