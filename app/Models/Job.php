<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
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
        'category_id',
        'title',
        'description',
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

    // protected $casts = [
    //     'is_remote' => 'boolean',
    //     'is_hybrid' => 'boolean',
    //     'is_onsite' => 'boolean',
    //     'is_featured' => 'boolean',
    //     'is_urgent' => 'boolean'
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function scopeUserId(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->firstOrFail();
        return $query->where('user_id', $user->id);
    }

    public function scopeCategory(Builder $query, $category_name): Builder
    {
        $category = Category::where('name', $category_name)->firstOrFail();
        return $query->where('category_id', $category->id);
    }

    public function scopeCountry(Builder $query, $country): Builder
    {
        $country_ids = Address::where('country', $country)->pluck('id');
        return $query->whereIn('address_id', $country_ids);
    }
    
    public function scopeState(Builder $query, $state): Builder
    {
        $state_ids = Address::where('state', $state)->pluck('id');
        return $query->whereIn('address_id', $state_ids);
    }
}
