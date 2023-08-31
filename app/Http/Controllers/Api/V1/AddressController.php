<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\Address\AddressCollection;
use App\Http\Resources\Address\AddressResource;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AddressController extends Controller
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

    public function store(StoreAddressRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
        ]);
        try {
            $address = Address::create($request->all());

            return ApiResponse::success(new AddressResource($address), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('LS-01 '.$e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $address = Address::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new AddressResource($address);
    }

    public function update(UpdateAddressRequest $request, $uuid)
    {
        try {
            $address = Address::where('uuid', $uuid)->first();
            $address->update($request->only(['street_1', 'street_2', 'city', 'state', 'country']));

            return new AddressResource($address);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $address = Address::where('uuid', $uuid)->firstOrFail();
            $address->delete();

            return ApiResponse::success($address, 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
