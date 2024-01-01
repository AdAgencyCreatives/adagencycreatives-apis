<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Http\Controllers\Controller;
use App\Http\Resources\Featured_Cities\LocationCollection;
use App\Http\Resources\Featured_Cities\LocationResource;
use App\Models\FeaturedLocation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class FeaturedLocationController extends Controller
{
    public function index(Request $request)
    {
       $query = QueryBuilder::for(FeaturedLocation::class)
            ->defaultSort('sort_order')
            ->allowedSorts('created_at', 'updated_at', 'sort_order');

       $query = $query->with('location');

       if ($request->per_page == -1) {
            $cities = $query->get();
        } else {
            $cities = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));
        }

       return new LocationCollection($cities);
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
