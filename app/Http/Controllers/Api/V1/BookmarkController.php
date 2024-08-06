<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bookmark\StoreBookmarkRequest;
use App\Http\Resources\Bookmark\BookmarkCollection;
use App\Http\Resources\Bookmark\BookmarkResource;
use App\Models\Bookmark;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Bookmark::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
            ]);

        if ($request->has('resource_type')) {
            $resourceType = $request->resource_type;
            $modelClass = $this->getResourceModelClass($resourceType);

            if ($modelClass) {

                $query = $query->where('bookmarkable_type', $modelClass);

                if ($request->has('resource_id')) {
                    $resource = $modelClass::where('uuid', $request->resource_id)->first();
                    $query->where('bookmarkable_id', $resource->id);
                }
            }
        }

        $query->orderByDesc('updated_at');

        $bookmarks = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        if ($request->has('search')) {
            $filtered = [];
            foreach ($bookmarks as $bookmark) {
                $user_full_name = $bookmark->user->first_name . " " . $bookmark->user->last_name;
                if (true || stripos($user_full_name, $request->search) !== false) {
                    $filtered[count($filtered)] = $bookmark;
                }
            }
            $bookmarks = $filtered;
        }

        return new BookmarkCollection($bookmarks);
    }

    public function store(StoreBookmarkRequest $request)
    {
        try {
            $user = $request->user();

            $modelAlias = $request->resource_type;
            $model_uuid = $request->resource_id;
            $modelClass = Bookmark::$modelAliases[$modelAlias] ?? null;

            $model_id = Bookmark::getIdByUUID($modelClass, $model_uuid);

            $request->merge([
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'bookmarkable_type' => $modelClass,
                'bookmarkable_id' => $model_id,
            ]);

            // $existing = Bookmark::where([
            //     'user_id' => $user->id,
            //     'bookmarkable_type' => $modelClass,
            //     'bookmarkable_id' => $model_id,
            // ])->get();

            // if ($existing) {
            //     return new BookmarkResource($existing);
            // }

            $bookmark = Bookmark::create($request->all());

            return new BookmarkResource($bookmark);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'BS-01');
        }
    }

    public function destroy($uuid)
    {
        try {
            $bookmark = Bookmark::where('uuid', $uuid)->firstOrFail();
            $bookmark->delete();

            return new BookmarkResource($bookmark);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    private function getResourceModelClass($resourceType)
    {
        $resourceModels = [
            'creatives' => 'App\Models\Creative',
            'agencies' => 'App\Models\Agency',
            'jobs' => 'App\Models\Job', // Add the appropriate model for each resource_type
            'applications' => 'App\Models\Application',
            'posts' => 'App\Models\Post',
        ];

        return $resourceModels[$resourceType] ?? null;
    }
}