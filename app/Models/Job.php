<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Traits\ActivityLoggerTrait;

class Job extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLoggerTrait;

    protected $table = 'job_posts';

    protected $fillable = [
        'uuid',
        'user_id',
        'advisor_id',
        'state_id',
        'city_id',
        'category_id',
        'title',
        'agency_name',
        'agency_website',
        'attachment_id',
        'description',
        'employment_type',
        'industry_experience',
        'media_experience',
        'strengths',
        'salary_range',
        'years_of_experience',
        'apply_type',
        'external_link',
        'status',
        'is_hybrid',
        'is_onsite',
        'is_remote',
        'is_featured',
        'is_urgent',
        'is_opentorelocation',
        'is_opentoremote',
        'created_at',
        'expired_at',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'views',
        'featured_at',
        'repost_job_id',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'featured_at' => 'datetime',
    ];

    public const STATUSES = [
        'PENDING' => 0,
        'APPROVED' => 1,
        'REJECTED' => 2,
        'EXPIRED' => 3,
        'FILLED' => 4,
        'DRAFT' => 5,
        'PUBLISHED' => 6,
    ];

    public const EMPLOYMENT_TYPE = [
        'Full-Time',
        'Part-Time',
        'Internship',
        'Freelance',
        'Contract 1099',
    ];

    public const WORKPLACE_PREFERENCE = [
        'is_remote' => 'Remote',
        'is_hybrid' => 'Hybrid',
        'is_onsite' => 'Onsite',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'user_id', 'user_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function state()
    {
        return $this->belongsTo(Location::class);
    }

    public function city()
    {
        return $this->belongsTo(Location::class);
    }

    public function attachment()
    {
        return $this->hasOne(Attachment::class, 'resource_id')->latestOfMany();
    }

    public function scopeUserId(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->first(); //this is user_id of logged_in user
        if (!$user) {
            return $query->where('user_id', 0);
        }

        //Uncomment this to disallow agencies to view the job(which is posted by advisor on their bahlf)
        // if(in_array($user->role, ['agency'])){
        //     return  $query->whereNull('advisor_id')->where('user_id', $user->id);
        // }

        if (in_array($user->role, ['advisor', 'recruiter'])) {

            return $query->where('advisor_id', $user->id)->orWhere('user_id', $user->id);
        }

        if (in_array($user->role, ['advisor', 'recruiter'])) {
            return $query->where('advisor_id', $user->id);
        }
        return $query->where('user_id', $user->id);
    }

    public function scopeCategoryId(Builder $query, $category_id): Builder
    {
        $category = Category::where('uuid', $category_id)->firstOrFail();

        return $query->where('category_id', $category->id);
    }

    public function scopeCategorySlug(Builder $query, $category_slug): Builder
    {
        $category = Category::where('slug', $category_slug)->firstOrFail();

        return $query->where('category_id', $category->id);
    }

    public function scopeStateId(Builder $query, $state_id): Builder
    {
        $state = Location::where('uuid', $state_id)->first();

        return $query->where('state_id', $state->id);
    }

    public function scopeStateSlug(Builder $query, $state_slug): Builder
    {
        $city = Location::where('slug', $state_slug)->whereNull('parent_id')->first();

        return $query->where('state_id', $city->id);
    }

    public function scopeCityId(Builder $query, $city_id): Builder
    {
        $city = Location::where('uuid', $city_id)->first();

        return $query->where('city_id', $city->id);
    }

    public function scopeAgency(Builder $query, $agency): Builder
    {
        $escapedAgencyName = str_replace('%', '\\%', $agency);

        return $query->where(function ($q) use ($escapedAgencyName) {
            $q->where(function ($q1) use ($escapedAgencyName) {
                $q1->where('agency_name', 'LIKE', "%" . $escapedAgencyName . "%");
            })->orWhere(function ($q2) use ($escapedAgencyName) {
                $q2->whereNull('agency_name')
                    ->whereHas('user.agency', function ($q22) use ($escapedAgencyName) {
                        $q22->where('name', 'LIKE', "%" . $escapedAgencyName . "%");
                    });
            });
        });

        // return $query->whereHas('user.agency', function ($q) use ($escapedAgencyName) {
        //     $q->where('name', 'LIKE', "%" . $escapedAgencyName . "%");
        // });
    }

    public function scopeCitySlug(Builder $query, $city_slug): Builder
    {
        $city = Location::where('slug', $city_slug)->whereNotNull('parent_id')->first();

        return $query->where('city_id', $city->id);
    }

    // public function scopeIndustryExperience(Builder $query, $industries): Builder
    // {
    //     $industries = Industry::whereIn('uuid', $industries)->pluck('id');
    //     return $query->whereIn('industry_experience', $industries);
    // }

    public function scopeMediaExperience(Builder $query, $medias): Builder
    {
        $medias = Media::whereIn('uuid', $medias)->pluck('id');

        return $query->whereIn('media_experience', $medias);
    }

    public function scopeSlug(Builder $query, $slug): Builder
    {

        return $query->where('slug', $slug);
    }

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case Job::STATUSES['PENDING']:
                return 'pending';
            case Job::STATUSES['APPROVED']:
                return 'approved';
            case Job::STATUSES['REJECTED']:
                return 'rejected';
            case Job::STATUSES['EXPIRED']:
                return 'expired';
            case Job::STATUSES['FILLED']:
                return 'filled';
            case Job::STATUSES['DRAFT']:
                return 'draft';
            case Job::STATUSES['PUBLISHED']:
                return 'published';

            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'approved':
                $this->attributes['status'] = Job::STATUSES['APPROVED'];
                break;
            case 'rejected':
                $this->attributes['status'] = Job::STATUSES['REJECTED'];
                break;
            case 'expired':
                $this->attributes['status'] = Job::STATUSES['EXPIRED'];
                break;
            case 'filled':
                $this->attributes['status'] = Job::STATUSES['FILLED'];
                break;
            case 'draft':
                $this->attributes['status'] = Job::STATUSES['DRAFT'];
                break;
            case 'published':
                $this->attributes['status'] = Job::STATUSES['PUBLISHED'];
                break;
            default:
                $this->attributes['status'] = Job::STATUSES['PENDING'];
                break;
        }
    }

    private static function getExistingSubscribedOrAppliedUsers($job)
    {
        $applications = array();
        $notifications = array();

        $current_job = Job::where('id', $job->id)->first();
        $apps = Application::where('job_id', $current_job?->id)->get()->pluck('user_id')->toArray();
        $notifs = Notification::where('type', 'job_alert')->where('body', json_encode(array('job_id' => $current_job?->id)))->get()->pluck('user_id')->toArray();
        $applications = array_merge($applications, $apps ? $apps : []);
        $notifications = array_merge($notifications, $notifs ? $notifs : []);

        $original_job = Job::where('id', $job?->repost_job_id)->first();

        while ($original_job?->id) {
            $apps = Application::where('job_id', $original_job?->id)->get()->pluck('user_id')->toArray();
            $notifs = Notification::where('type', 'job_alert')->where('body', json_encode(array('job_id' => $original_job?->id)))->get()->pluck('user_id')->toArray();
            $applications = array_merge($applications, $apps ? $apps : []);
            $notifications = array_merge($notifications, $notifs ? $notifs : []);
            $original_job = Job::where('id', $original_job?->repost_job_id)->first();
        }

        $users = array_values(array_unique(array_merge($applications, $notifications)));

        return $users;
    }

    protected static function booted()
    {
        static::creating(function ($job) {
            $job = $job->getDefaultExpirationDate($job);
        });


        static::created(function ($job) {
            if (!App::runningInConsole()) {
                Cache::forget('dashboard_stats_cache');
                Cache::forget('featured_cities');
            }

            if ($job->slug == null) {
                $agencyName = $job->user?->agency?->name ?? '';
                $slug = sprintf('%s %s %s %s %s', $agencyName, $job->state?->name, $job->city?->name, $job->employment_type, $job->title);
                $slug = Str::slug($slug);
                //if slug already exists, then add 2 to it, if that exists, then add 3 to it and so on
                $slugCount = count(Job::whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->get());
                $slug = $slugCount ? "{$slug}-{$slugCount}" : $slug;
                $job->slug = $slug;
                $job->seo_title = settings('job_title');
                $job->seo_description = settings('job_description');
                $job->save();
            }
        });

        static::updating(function ($job) {

            $agencyName = $job->user?->agency?->name ?? '';
            $slug = sprintf('%s %s %s %s %s', $agencyName, $job->state?->name, $job->city?->name, $job->employment_type, $job->title);
            $slug = Str::slug($slug);
            //if slug already exists, then add 2 to it, if that exists, then add 3 to it and so on
            $slugCount = count(Job::whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->where("id", '<>', $job->id)->get());
            $slug = $slugCount ? "{$slug}-{$slugCount}" : $slug;
            $job->slug = $slug;

            $category = Category::find($job->category_id);

            $author = User::find($job->user_id);
            $agency =  $author->agency; // fetch original agency for which we are posting

            $agency_name = $job?->agency_name ?? ($agency?->name ?? '');
            $agency_profile = $job?->agency_website ?? (in_array($author->role, ['agency']) ? $agency?->slug : '');

            if ($job->advisor_id) {
                $author = User::find($job->advisor_id); // author override only after original agency is fetched
            }

            $oldStatus = $job->getOriginal('status');
            if ($oldStatus == 'draft' && $job->status === 'approved') {
                // $categorySubscribers = JobAlert::with('user')->where('category_id', $job->category_id)->where('status', 1)->get();

                $categories = [];

                $category = Category::where('id', $job->category_id)->first();

                $group_categories = Category::where('group_name', $category->name)->get();

                if (count($group_categories) > 0) {
                    for ($i = 0; $i < count($group_categories); $i++) {
                        $categories[$i] = $group_categories[$i]->id;
                    }
                } else {
                    $categories[0] = $category->id;
                }

                $users = Job::getExistingSubscribedOrAppliedUsers($job);

                $categorySubscribers = JobAlert::with('user')->whereNotIn('user_id', $users)->whereIn('category_id', $categories)->where('status', 1)->get();

                $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);

                $data = [
                    'email_data' => [
                        'title' => $job->title ?? '',
                        'url' => $job_url,
                        'agency' => $agency_name,
                        'agency_profile' => strlen($agency_profile) > 0 ? sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency_profile) : '',
                        'category' => $category?->name,
                        'subscribers_count' => env('APP_ENV') == 'production' ? "" : "" . count($categorySubscribers),
                    ],
                    'subscribers' => $categorySubscribers,
                ];

                create_notification($job->user_id, sprintf('Job: %s approved.', $job->title)); //Send notification to agency about job approval
                if ($job->advisor_id) {
                    create_notification($job->advisor_id, sprintf('Job: %s approved.', $job->title)); //Send notification to agency about job approval
                }

                foreach ($categorySubscribers as $creative) {
                    create_notification($creative->user_id, sprintf('New job posted in %s category.', $category->name), 'job_alert', ['job_id' => $job->id]); //Send notification to candidates
                }

                // if ($categorySubscribers && count($categorySubscribers) > 0) {
                //     SendEmailJob::dispatch($data, 'job_approved_alert_all_subscribers');
                // }

                SendEmailJob::dispatch($data, 'job_approved_alert_all_subscribers');

                /**
                 * Send Notification to Admin about new job
                 */

                $data = [
                    'data' => [
                        'job' => $job,
                        'url' => $job_url,
                        'category' => $category->name,
                        'author' => $author->first_name,
                        'agency' => $agency_name,
                        'agency_profile' => strlen($agency_profile) > 0 ? sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency_profile) : '',
                        'created_at' => $job->created_at->format('M-d-Y'),
                        'expired_at' => $job->expired_at->format('M-d-Y'),
                    ],
                    'receiver' => User::where('email', env('ADMIN_EMAIL'))->first()
                ];

                SendEmailJob::dispatch($data, 'new_job_added_admin');
            }

            if ($job->status === 'filled' && $job->advisor_id != null) {

                $state = Location::where('uuid', $job->state_id)->first();
                $city = Location::where('uuid', $job->city_id)->first();
                $advisor = User::find($job->advisor_id);
                $admin = User::where('email', env('ADMIN_EMAIL'))->first();

                foreach ([$advisor, $admin] as $receiver_user) {
                    $data = [
                        'data' => [
                            'category' => $category->name,
                            'agency_name' => $agency_name,
                            'agency_profile' => sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency_profile),
                            'state' => $state?->name,
                            'city' => $city?->name,
                            'advisor' => $advisor->full_name,
                            'recipient' => $receiver_user->full_name,
                        ],
                        'receiver' => $receiver_user
                    ];
                    SendEmailJob::dispatch($data, 'hire-an-advisor-job-completed');
                }
            }

            if ($job->isDirty('expired_at')) {

                if (auth()->user()->role != 'admin') {
                    $job = $job->getDefaultExpirationDate($job);
                }
            }
        });

        static::updated(function () {
            Cache::forget('dashboard_stats_cache');
            Cache::forget('featured_cities');
        });

        static::deleted(function ($job) {
            Cache::forget('dashboard_stats_cache');
            Cache::forget('featured_cities');

            Application::where('job_id', $job->id)->delete();
        });
    }

    public function getDefaultExpirationDate($job)
    {
        /**
         * Set default expiration date for job posts
         * ---------------------------------------------
         * Only allow admin to update expired_at date to any future date other than default expired date for current active subscription package,
         * All other users can only set the max expired_at date according to current subscription default date
         * --------------------------------------------------------
         * Client Message Date: 20 January 2024 3:13 AM
         */

        $post_author_id = $job->advisor_id ?? $job->user_id;

        $user = User::find($post_author_id);
        if (!$user) {
            return now()->addDays(30);
        }
        $subscription = $user->active_subscription;

        $default_plan = Plan::where('slug', $subscription->name)->first();
        $default_expiration_date = now()->addDays($default_plan->days);

        $newExpirationDate = $job->getAttribute('expired_at');

        if ($newExpirationDate > $default_expiration_date) {
            $job->expired_at = $default_expiration_date;
        }

        return $job;
    }
}
