<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobAlert\StoreJobAlertRequest;
use App\Http\Resources\Address\AddressCollection;
use App\Http\Resources\JobAlert\JobAlertResource;
use App\Models\Address;
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
        $query = QueryBuilder::for(Address::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                'label',
                'street_1',
                'street_2',
                'city',
                'state',
                'country',
                'postal_code',
            ]

            );

        $addresses = $query->paginate(config('global.request.pagination_limit'));

        return new AddressCollection($addresses);
    }

    public function store(StoreJobAlertRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $category = Category::where('uuid', $request->category_id)->first();

        $data = [
            'user_id' => $user->id,
            'category_id' => $category->id,
        ];

        try {
            if ($existingJobAlert = JobAlert::where($data)->first()) {
                $existingJobAlert->update(['status' => $request->status]);

                return ApiResponse::success(new JobAlertResource($existingJobAlert), 200);
            }

            $data['uuid'] = Str::uuid();
            $data['status'] = $request->status;

            $alert = JobAlert::create($data);

            return ApiResponse::success(new JobAlertResource($alert), 200);

        } catch (\Exception $e) {
            return ApiResponse::error('SA-01 '.$e->getMessage(), 400);
        }
    }
}
