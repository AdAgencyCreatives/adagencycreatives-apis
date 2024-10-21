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

    public function application()
    {
        return $this->belongsTo(Application::class);
    }




    protected static function booted() {}
}
