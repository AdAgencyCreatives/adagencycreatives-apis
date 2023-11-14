<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest\StorePackageRequest;
use App\Http\Resources\AssignedAgency\AssignedAgencyCollection;
use App\Http\Resources\PackageRequest\PackageRequestCollection;
use App\Http\Resources\PackageRequest\PackageRequestResource;
use App\Models\Category;
use App\Models\Location;
use App\Models\PackageRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PackageRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(PackageRequest::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                'status',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts('created_at');

        $package_requests = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new PackageRequestCollection($package_requests);
    }

    public function store(StorePackageRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $category = Category::where('uuid', $request->category_id)->first();

        $state = Location::where('uuid', $request->state_id)->first();
        $city = Location::where('uuid', $request->city_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'state_id' => $state->id ?? null,
            'city_id' => $city->id ?? null,
            'industry_experience' => ''.implode(',', $request->industry_experience).'',
            'media_experience' => ''.implode(',', $request->media_experience).'',
        ]);

        $package_request = PackageRequest::create($request->all());

        return ApiResponse::success(new PackageRequestResource($package_request), 200);
    }

    public function update(Request $request, $uuid)
    {
        try {
            $package_request = PackageRequest::where('uuid', $uuid)->first();
            $package_request->update($request->only('status'));

            return new PackageRequestResource($package_request);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $package_request = PackageRequest::where('uuid', $uuid)->first();
            $package_request->delete();

            return new PackageRequestResource($package_request);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function get_assigned_agencies()
    {
        try {

            // Find the user by UUID
            $user = request()->user();

            // Retrieve package requests for the user
            $packageRequests = PackageRequest::select('user_id')
                ->where('assigned_to', $user->id)
                ->where('status', 1) //only approved
                ->pluck('user_id')
                ->toArray();

            // dd($packageRequests);

            // Retrieve agencies for the package requests
            $agencies = User::with('agency')
                ->whereIn('id', $packageRequests)
                ->get();

            // dd($agencies->toArray());
            return new AssignedAgencyCollection($agencies);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }
}