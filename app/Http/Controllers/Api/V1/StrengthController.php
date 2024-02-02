<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Strength\StoreStrengthRequest;
use App\Http\Requests\Strength\UpdateStrengthRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\Strength;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class StrengthController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Strength::class)
            ->allowedFilters([
                'name',
            ])
            ->defaultSort('name')
            ->allowedSorts('name');

        $strengths = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new CategoryCollection($strengths);
    }

    public function store(StoreStrengthRequest $request)
    {
        try {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);

            $strength = Strength::create($request->all());

            return new CategoryResource($strength);
        } catch (\Exception $e) {
            throw new ApiException($e, 'SS-01');
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

    public function update(UpdateStrengthRequest $request, $uuid)
    {
        try {
            $strength = Strength::where('uuid', $uuid)->first();
            $strength->update($request->only('name'));

            return new CategoryResource($strength);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $strength = Strength::where('uuid', $uuid)->firstOrFail();
            $strength->delete();

            return new CategoryResource($strength);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function get_strengths()
    {
        $cacheKey = 'all_strengths';
        $strengths = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return new CategoryCollection(Strength::orderBy('name', 'asc')->get());
        });

        return $strengths;
    }
}