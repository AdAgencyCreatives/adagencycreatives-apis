<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreativeSpotlight extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'path',
        'name',
        'slug',
        'status',
    ];

    public const STATUSES = [
        'PENDING' => 0,
        'APPROVED' => 1,
        'REJECTED' => 2,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case self::STATUSES['PENDING']:
                return 'pending';
            case self::STATUSES['APPROVED']:
                return 'approved';
            case self::STATUSES['REJECTED']:
                return 'rejected';
            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'pending':
                $this->attributes['status'] = self::STATUSES['PENDING'];
                break;
            case 'approved':
                $this->attributes['status'] = self::STATUSES['APPROVED'];
                break;
            case 'rejected':
                $this->attributes['status'] = self::STATUSES['REJECTED'];
                break;
            default:
                $this->attributes['status'] = self::STATUSES['PENDING'];
                break;
        }
    }
}