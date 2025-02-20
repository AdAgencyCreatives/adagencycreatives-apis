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
use Illuminate\Support\Facades\DB;

class FeaturedLocationWithJobCountController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::select('locations.*', DB::raw('(SELECT COUNT(job_posts.id) FROM job_posts WHERE job_posts.city_id = locations.id AND job_posts.status = 1) as job_count'))
            ->where('is_featured', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('job_count', 'desc');

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