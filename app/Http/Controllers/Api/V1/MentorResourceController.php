<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MentorResource\MentorResource;
use App\Http\Resources\MentorResource\MentorResourceCollection;
use App\Models\Resource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MentorResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Resource::class)
            ->allowedFilters([
                AllowedFilter::scope('topic'),
            ])
            ->defaultSort('created_at')
            ->allowedSorts('title', 'created_at', 'updated_at');

        $topics = $query->with('topic')->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new MentorResourceCollection($topics);
    }

    public function store(Request $request)
    {
        try {

            $resource= Resource::create($request->all());

            return new MentorResource($resource);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $topic = Resource::where('id', $id)->first();

            // Assuming the column to be updated is sent in the request
            $column = $request->input('column');

            // Check if the requested column is allowed to be updated
            $allowedColumns = ['title', 'link', 'description'];

            if (!in_array($column, $allowedColumns)) {
                return ApiResponse::error(trans('response.invalid_column'), 400);
            }

            // Update the category based on the requested column
            $topic->update([$column => $request->input('value')]);

            return new MentorResource($topic);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($id)
    {
        try {
            $topic = Resource::where('id', $id)->firstOrFail();
            $topic->delete();

            return new MentorResource($topic);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

}
