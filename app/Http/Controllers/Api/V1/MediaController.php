<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Media;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Media::class)
            ->allowedFilters([
                'name',
            ])
            ->defaultSort('name')
            ->allowedSorts('name');

        $industries = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new CategoryCollection($industries);
    }

    public function store(StoreMediaRequest $request)
    {
        try {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);

            $media = Media::create($request->all());

            return new CategoryResource($media);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }

    public function show($uuid)
    {
        try {
            $media = Media::where('uuid', $uuid)->firstOrFail();

            return new CategoryResource($media);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function update(UpdateMediaRequest $request, $uuid)
    {
        try {
            $media = Media::where('uuid', $uuid)->first();
            $media->update($request->only('name'));

            return new CategoryResource($media);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $media = Media::where('uuid', $uuid)->firstOrFail();
            $media->delete();

            return new CategoryResource($media);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function get_medias(Request $request)
    {
        $cacheKey = 'all_medias';
        $medias = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return new CategoryCollection(Media::all());
        });

        return $medias;
    }
}