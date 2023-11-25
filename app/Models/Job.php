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

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_posts';

    protected $fillable = [
        'uuid',
        'user_id',
        'state_id',
        'city_id',
        'category_id',
        'title',
        'agency_name',
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
        'expired_at',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'views',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
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
        return $this->hasOne(Attachment::class, 'resource_id');
    }

    public function scopeUserId(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

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
        $city = Location::where('slug', $state_slug)->first();

        return $query->where('state_id', $city->id);
    }

    public function scopeCityId(Builder $query, $city_id): Builder
    {
        $city = Location::where('uuid', $city_id)->first();

        return $query->where('city_id', $city->id);
    }

    public function scopeCitySlug(Builder $query, $city_slug): Builder
    {
        $city = Location::where('slug', $city_slug)->first();

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

    protected static function booted()
    {
        static::created(function ($job) {
            if (! App::runningInConsole()) {
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
            $oldStatus = $job->getOriginal('status');
            if ($oldStatus == 'draft' && $job->status === 'approved') {
                $categorySubscribers = JobAlert::with('user')->where('category_id', $job->category_id)->where('status', 1)->get();
                $category = Category::find($job->category_id);
                $author = User::find($job->user_id);
                $agencyName = $author->agency?->name ?? '';

                $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);
                $data = [
                    'email_data' => [
                        'title' => $job->title ?? '',
                        'url' => $job_url,
                        'agency' => $agencyName,
                        'category' => $category?->name,
                    ],
                    'subscribers' => $categorySubscribers,
                ];

                create_notification($job->user_id, sprintf('Job: %s approved.', $job->title)); //Send notification to agency about job approval
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
                        'agency' => $agencyName,
                    ],
                    'receiver' => User::where('email', 'erika@adagencycreatives.com')->first()
                ];
                SendEmailJob::dispatch($data, 'new_job_added_admin');
            }

        });

        static::updated(function () {
            Cache::forget('dashboard_stats_cache');
            Cache::forget('featured_cities');
        });

        static::deleted(function () {
            Cache::forget('dashboard_stats_cache');
            Cache::forget('featured_cities');

        });
    }
}