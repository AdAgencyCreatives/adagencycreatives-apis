<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'status',
        'attachment_id',
    ];

    const STATUSES = [
        'PUBLIC' => 0,
        'PRIVATE' => 1,
        'HIDDEN' => 2,
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case Group::STATUSES['PUBLIC']:
                return 'public';
            case Group::STATUSES['PRIVATE']:
                return 'private';
            case Group::STATUSES['HIDDEN']:
                return 'hidden';

            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'public':
                $this->attributes['status'] = Group::STATUSES['PUBLIC'];
                break;
            case 'private':
                $this->attributes['status'] = Group::STATUSES['PRIVATE'];
                break;
            default:
                $this->attributes['status'] = Group::STATUSES['HIDDEN'];
                break;
        }
    }
}
