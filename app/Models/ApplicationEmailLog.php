<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationEmailLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'application_id',
        'status',
        'email_sent_at',
    ];

    const STATUSES = [
        'PENDING' => 0,
        'ACCEPTED' => 1,
        'REJECTED' => 2,
        'ARCHIVED' => 3, // Application will remove from agency frontend, but it will still exist in the database, so that candidate can't submit the application again.
        'RECOMMENDED' => 4,
        'SHORTLISTED' => 5,
        'HIRED' => 6,
    ];

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case Application::STATUSES['PENDING']:
                return 'pending';
            case Application::STATUSES['ACCEPTED']:
                return 'accepted';
            case Application::STATUSES['REJECTED']:
                return 'rejected';
            case Application::STATUSES['ARCHIVED']:
                return 'archived';
            case Application::STATUSES['SHORTLISTED']:
                return 'shortlisted';
            case Application::STATUSES['RECOMMENDED']:
                return 'recommended';
            case Application::STATUSES['HIRED']:
                return 'hired';
            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'accepted':
                $this->attributes['status'] = Application::STATUSES['ACCEPTED'];
                break;
            case 'rejected':
                $this->attributes['status'] = Application::STATUSES['REJECTED'];
                break;
            case 'archived':
                $this->attributes['status'] = Application::STATUSES['ARCHIVED'];
                break;
            case 'shortlisted':
                $this->attributes['status'] = Application::STATUSES['SHORTLISTED'];
                break;
            case 'recommended':
                $this->attributes['status'] = Application::STATUSES['RECOMMENDED'];
                break;
            case 'hired':
                $this->attributes['status'] = Application::STATUSES['HIRED'];
                break;
            default:
                $this->attributes['status'] = Application::STATUSES['PENDING'];
                break;
        }
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    protected static function booted() {}
}
