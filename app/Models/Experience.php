<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'resume_id',
        'title',
        'company',
        'description',
        'started_at',
        'completed_at',
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
