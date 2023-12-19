<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobAlert\StoreJobAlertRequest;
use App\Http\Resources\JobAlert\JobAlertResource;
use App\Models\Category;
use App\Models\JobAlert;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class JobAlertController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(JobAlert::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                'status',
            ]
            );

        $alerts = $query->first();
        if (!$alerts) {
            return ApiResponse::error('Job alert not found', 404);
        }

        return new JobAlertResource($alerts);
    }

    public function store(StoreJobAlertRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $category = Category::where('uuid', $request->category_id)->first();


        try {
            if ($existingJobAlert = JobAlert::where('user_id', $user->id)->first()) {
                $existingJobAlert->update([
                    'category_id' => $category->id,
                    'status' => $request->status,
                ]);

                return ApiResponse::success(new JobAlertResource($existingJobAlert), 200);
            }

            $data['uuid'] = Str::uuid();
            $data['user_id'] = $user->id;
            $data['status'] = $request->status;
            $data['category_id'] = $category->id;

            $alert = JobAlert::create($data);

            return ApiResponse::success(new JobAlertResource($alert), 200);

        } catch (\Exception $e) {
            return ApiResponse::error('SA-01 '.$e->getMessage(), 400);
        }
    }
}
