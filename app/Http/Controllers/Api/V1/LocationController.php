<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Location\LocationCollection;
use App\Http\Resources\Location\LocationResource;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->all();
        $query = QueryBuilder::for(Location::class);

        if (isset($filters['filter']['state_id']) || isset($filters['filter']['name'])) {
            $query->allowedFilters([
                AllowedFilter::scope('state_id'),
                'name',
            ]);
        } else {
            $query->whereNull('parent_id');
        }

        $query = $query->defaultSort('name')->allowedSorts('name');

        if ($request->per_page == -1) {
            $locations = $query->get();
        } else {
            $locations = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));
        }

        return new LocationCollection($locations);
    }

    public function store(StoreLocationRequest $request)
    {
        try {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);

            $location = Location::create($request->all());

            return new LocationResource($location);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }

    public function show($uuid)
    {
        try {
            $category = Category::where('uuid', $uuid)->firstOrFail();

            return new CategoryResource($category);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function update(UpdateLocationRequest $request, $uuid)
    {
        try {
            $location = Location::where('uuid', $uuid)->first();
            $location->update($request->only('name'));

            return new LocationResource($location);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $location = Location::where('uuid', $uuid)->firstOrFail();
            $location->delete();

            return new LocationResource($location);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function cities(Request $request)
    {
        $query = QueryBuilder::for(Location::class);
        $query = $query->whereNotNull('parent_id');
        if ($request->per_page == -1) {

            $cities = $query->get();
        } else {
            $cities = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));
        }

        return new LocationCollection($cities);
    }

    public function get_states(Request $request)
    {
        $query = QueryBuilder::for(Location::class);
        $query = $query->whereNull('parent_id');
        if ($request->per_page == -1) {
            $states = $query->get();
        } else {
            $states = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));
        }

        return new LocationCollection($states);
    }

    public function get_cities(Request $request)
    {
        $query = QueryBuilder::for(Location::class);
        $query = $query->whereNotNull('parent_id');
        if ($request->per_page == -1) {
            $cities = $query->get();
        } else {
            $cities = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));
        }

        return new LocationCollection($cities);
    }
}
