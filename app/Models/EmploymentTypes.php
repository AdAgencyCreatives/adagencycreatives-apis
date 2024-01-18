<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\ActivityLoggerTrait;

class EmploymentTypes extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'name',
    ];

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('employment_types');
        });

        static::updated(function ($employment_type) {
            Cache::forget('employment_types');
            $oldEmployment = $employment_type->getOriginal('name');
            $newEmployment = $employment_type->name;

            $creatives = Creative::where('employment_type', 'LIKE', '%' . $oldEmployment . '%')->get();
            foreach($creatives as $creative) {
                $emp_type = $creative->employment_type;
                $emp_type = str_replace($oldEmployment, $newEmployment, $emp_type);
                $creative->update([
                'employment_type' => $emp_type
            ]);
            }

            $jobs = Job::where('employment_type', 'LIKE', '%' . $oldEmployment . '%')->get();
            foreach($jobs as $job) {
                $emp_type = $job->employment_type;
                $emp_type = str_replace($oldEmployment, $newEmployment, $emp_type);
                $job->update([
                'employment_type' => $emp_type
            ]);
            }
        });

        static::deleted(function () {
            Cache::forget('employment_types');
        });
    }
}