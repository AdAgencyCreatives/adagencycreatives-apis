<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'address_id',
        'title',
        'description',
        'category',
        'employement_type',
        'industry_experience',
        'media_experience',
        'salary_range',
        'experience',
        'apply_type',
        'external_link',
        'status',
        'is_remote',
        'is_hybrid',
        'is_onsite',
        'is_featured',
        'is_urgent',
        'expired_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
