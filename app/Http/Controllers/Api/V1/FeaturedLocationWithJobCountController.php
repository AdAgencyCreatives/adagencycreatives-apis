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
        // $query = Location::select('locations.*', \DB::raw('COUNT(job_posts.id) as job_count'))
        //     ->leftJoin('job_posts', function ($join) {
        //         $join->on('locations.id', '=', 'job_posts.city_id');
        //     })
        //     ->groupBy('locations.id', 'locations.uuid', 'locations.name', 'locations.slug', 'locations.parent_id', 'locations.preview_link', 'locations.is_featured', 'locations.created_at', 'locations.updated_at');


        $query = Location::selectRaw("SELECT T.* FROM (SELECT locations.*, (SELECT COUNT(*) FROM job_posts WHERE job_posts.city_id = locations.id AND job_posts.status = 1) as job_count FROM locations) T ORDER BY T.sort_order ASC, T.job_count DESC;");

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