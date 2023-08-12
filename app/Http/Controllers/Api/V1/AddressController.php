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
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = QueryBuilder::for(Address::class)
                ->allowedFilters('state', 'country')
                ->allowedSorts('id')
                ->get();
        

        // $addresses = Address::paginate(config('global.request.pagination_limit'));

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
            return ApiResponse::error('LS-01 ' . $e->getMessage(), 400);
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