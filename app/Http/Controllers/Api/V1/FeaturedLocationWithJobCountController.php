<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Http\Controllers\Controller;
use App\Http\Resources\Featured_Cities\LocationResource;
use App\Http\Resources\Featured_Cities\LocationWithJobsCountCollection;
use App\Models\FeaturedLocation;
use App\Models\Location;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FeaturedLocationWithJobCountController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::select('locations.*', \DB::raw('COUNT(job_posts.id) as job_count'))
            ->leftJoin('job_posts', function ($join) {
                $join->on('locations.id', '=', 'job_posts.state_id')
                    ->orOn('locations.id', '=', 'job_posts.city_id');
            })
            ->where('job_posts.status', '=', 1)
            ->groupBy('locations.id', 'locations.uuid', 'locations.name', 'locations.slug', 'locations.parent_id', 'locations.preview_link', 'locations.is_featured', 'locations.created_at', 'locations.updated_at');

        // $query->orderByDesc('is_featured');
        $query->orderBy('sort_order');
        $query->orderByDesc('job_count');

        $perPage = $request->per_page ?? config('global.request.pagination_limit');

        $topLocations = $query->paginate($perPage);

        return new LocationWithJobsCountCollection($topLocations);
    }

    public function destroy($id)
    {
        try {
            $city = FeaturedLocation::where('id', $id)->firstOrFail();
            $city->delete();

            return new LocationResource($city);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }
}