<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Topic\TopicCollection;
use App\Http\Resources\Topic\TopicResource;
use App\Models\Category;
use App\Models\Topic;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;

class MentorTopicController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Topic::class)
            ->allowedFilters([
                'id',
                'title',
                'slug',
            ])
            ->defaultSort('created_at')
            ->allowedSorts('title', 'created_at', 'updated_at');

        $topics = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));
        return new TopicCollection($topics);
    }

    public function store(Request $request)
    {
        try {

            $topic = Topic::create($request->all());

            return new TopicResource($topic);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
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

    public function update(Request $request, $id)
    {

        try {
            $topic = Topic::where('id', $id)->first();

            // Assuming the column to be updated is sent in the request
            $column = $request->input('column');

            // Check if the requested column is allowed to be updated
            $allowedColumns = ['title', 'slug', 'description'];

            if (!in_array($column, $allowedColumns)) {
                return ApiResponse::error(trans('response.invalid_column'), 400);
            }

            // Update the category based on the requested column
            $topic->update([$column => $request->input('value')]);

            return new TopicResource($topic);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($id)
    {
        try {
            $topic = Topic::where('id', $id)->firstOrFail();
            $topic->delete();

            return new TopicResource($topic);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

}
