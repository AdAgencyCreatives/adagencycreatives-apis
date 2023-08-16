<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bookmark\StoreBookmarkRequest;
use App\Http\Resources\Bookmark\BookmarkCollection;
use App\Http\Resources\Bookmark\BookmarkResource;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookmarkController extends Controller
{
    public function index()
    {
        $bookmarks = Bookmark::paginate(config('global.request.pagination_limit'));

        return new BookmarkCollection($bookmarks);
    }

    public function store(StoreBookmarkRequest $request)
    {
        try {
            $user = User::where('uuid', $request->user_id)->firstOrFail();

            $resource_type = $request->resource_type;
            $resource_id = $request->resource_id;

            $resource = DB::table($resource_type)->where('uuid', $resource_id)->first();
            if (! $resource) {
                throw new ModelNotFound('Not found', 404);
            }

            $request->merge([
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'resource_type' => $resource_type,
                'resource_id' => $resource->id,
            ]);

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
}
