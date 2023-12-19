<?php

namespace App\Models\Api\V1;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'creative_id',
        'job_id',
        'status',
        'read_at',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id', 'id');
    }

    public function agency()
    {
        return $this->belongsTo(User::class, 'agency_id', 'id');
    }
}
