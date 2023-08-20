<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Industry\StoreIndustryRequest;
use App\Http\Requests\Industry\UpdateIndustryRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Industry;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class IndustryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', config('global.request.pagination_limit'));

        $cacheKey = 'industries'.$perPage;

        $categories = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($perPage) {
            if ($perPage == -1) {
                return Industry::all();
            } else {
                return Industry::paginate($perPage);
            }
        });

        return new CategoryCollection($categories);
    }

    public function store(StoreIndustryRequest $request)
    {
        try {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);

            $industry = Industry::create($request->all());

            return new CategoryResource($industry);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }

    public function show($uuid)
    {
        try {
            $industry = Industry::where('uuid', $uuid)->firstOrFail();

            return new CategoryResource($industry);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function update(UpdateIndustryRequest $request, $uuid)
    {
        try {
            $industry = Industry::where('uuid', $uuid)->first();
            $industry->update($request->only('name'));

            return new CategoryResource($industry);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $industry = Industry::where('uuid', $uuid)->firstOrFail();
            $industry->delete();

            return new CategoryResource($industry);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }
}
