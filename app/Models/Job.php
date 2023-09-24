<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

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
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    const STATUSES = [
        'PENDING' => 0,
        'APPROVED' => 1,
        'REJECTED' => 2,
        'EXPIRED' => 3,
        'FILLED' => 4,
        'DRAFT' => 5,
        'PUBLISHED' => 6,
    ];

    const EMPLOYMENT_TYPE = [
        'Full-Time',
        'Part-Time',
        'Internship',
        'Freelance',
        'Contract 1099',
    ];

    const WORKPLACE_PREFERENCE = [
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
        return $this->belongsTo(Agency::class, 'user_id');
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

    public function scopeStateId(Builder $query, $state_id): Builder
    {
        $state = Location::where('uuid', $state_id)->first();

        return $query->where('state_id', $state->id);
    }

    public function scopeIndustryExperience(Builder $query, $industries): Builder
    {
        $industries = Industry::whereIn('uuid', $industries)->pluck('id');
        dd($industries);

        return $query->whereIn('industry_experience', $industries);
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
            Cache::forget('dashboard_stats_cache');

            /**
             * Send Notification to Admin about new job
             */
            $category = Category::find($job->category_id);
            $author = User::find($job->user_id);

            $data = [
                'data' => [
                    'job' => $job,
                    'category' => $category->name,
                    'author' => $author->first_name,
                ],
                'receiver' => User::find(1),
            ];
            SendEmailJob::dispatch($data, 'new_job_added_admin');

        });

        static::updating(function ($job) {
            $oldStatus = $job->getOriginal('status');
            if ($oldStatus !== 'approved' && $job->status === 'approved') {
                $categorySubscribers = JobAlert::with('user')->where('category_id', $job->category_id)->where('status', 1)->get();
                $category = Category::find($job->category_id);
                $data = [
                    'email_data' => [
                        'title' => $job->title,
                        'url' => env('FRONTEND_JOB_URL'),
                        'category' => $category->name,
                    ],
                    'subscribers' => $categorySubscribers,
                ];
                SendEmailJob::dispatch($data, 'job_approved_alert_all_subscribers');
            }
        });

        static::updated(function () {
            Cache::forget('dashboard_stats_cache');
        });

        static::deleted(function () {
            Cache::forget('dashboard_stats_cache');
        });
    }
}
