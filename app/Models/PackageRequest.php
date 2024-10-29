<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggerTrait;

class PackageRequest extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'assigned_to',
        'category_id',
        'state_id',
        'city_id',
        'package',
        'start_date',
        'status',
        'employment_type',
        'industry_experience',
        'media_experience',
        'is_opentorelocation',
        'is_opentoremote',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(Location::class);
    }

    public function city()
    {
        return $this->belongsTo(Location::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'user_id', 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeUserId(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('user_id', $user->id);
    }

    public function scopeAssignedTo(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('assigned_to', $user->id);
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
            default:
                $this->attributes['status'] = Job::STATUSES['PENDING'];
                break;
        }
    }

    public static function assignSubscription($user_id, $plan_name, $role)
    {
        $subscription = Subscription::where('user_id', $user_id)->latest()->first(); // Retrieve the latest subscription

        $plan = Plan::where('slug', $plan_name)->first();

        $data = [
            'name' => $plan->slug, // Plan Name
            'quota_left' => $plan->quota,
            'ends_at' => now()->addDays($plan->days),
        ];

        if ($subscription) {
            if ($role == "advisor") {
                $data['quota_left'] = $data['quota_left'] + 1;
            }
            $subscription->update($data);
        } else {

            $newSubscriptionData = array_merge($data, [
                'user_id' => $user_id,
                'price' => $plan->price,
                'quantity' => $plan->quota,
            ]);

            Subscription::create($newSubscriptionData);
        }
    }

    protected static function booted()
    {
        static::updating(function ($package_request) {
            $oldStatus = $package_request->getOriginal('status');
            $newStatus = $package_request->status;

            if ($oldStatus === 'pending' && $newStatus != 'pending') { //New status is something else
                $user = User::find($package_request->user_id);
                if ($newStatus === 'approved') {
                    PackageRequest::assignSubscription($package_request->user_id, 'premium-hire-an-advisor', 'agency');
                    PackageRequest::assignSubscription($package_request->assigned_to, 'premium-hire-an-advisor', 'advisor');
                } elseif ($newStatus === 'rejected') {
                    SendEmailJob::dispatch([
                        'receiver' => $user,
                        'data' => $user,
                    ], 'custom_job_request_rejected');
                }
            }
        });
    }
}
