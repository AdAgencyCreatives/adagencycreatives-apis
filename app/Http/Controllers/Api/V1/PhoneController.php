<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Phone\StorePhoneRequest;
use App\Http\Requests\Phone\UpdatePhoneRequest;
use App\Http\Resources\Phone\PhoneCollection;
use App\Http\Resources\Phone\PhoneResource;
use App\Models\Phone;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PhoneController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(Phone::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                // AllowedFilter::exact('label'),
                'label',
                // 'phone_number'
            ]);

        $phones = $query->paginate(config('global.request.pagination_limit'));

        return new PhoneCollection($phones);
    }

    public function store(StorePhoneRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
        ]);
        try {
            $phone = Phone::create($request->all());

            return ApiResponse::success(new PhoneResource($phone), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('LS-01 '.$e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $phone = Phone::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new PhoneResource($phone);
    }

    public function update(UpdatePhoneRequest $request, $uuid)
    {
        try {
            $phone = Phone::where('uuid', $uuid)->first();
            $phone->update($request->only('country_code', 'phone_number'));

            return new PhoneResource($phone);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $phone = Phone::where('uuid', $uuid)->firstOrFail();
            $phone->delete();

            return ApiResponse::success($phone, 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
