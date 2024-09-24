<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryCreativeCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Category::class)
            ->allowedFilters([
                'name',
                'group_name',
            ])
            ->defaultSort('name')
            ->allowedSorts('name');

        $categories = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new CategoryCollection($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);

            $category = Category::create($request->all());

            return new CategoryResource($category);
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

    public function update(UpdateCategoryRequest $request, $uuid)
    {
        try {
            $category = Category::where('uuid', $uuid)->first();
            $category->update($request->only('name'));

            return new CategoryResource($category);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $category = Category::where('uuid', $uuid)->firstOrFail();
            $category->delete();

            return new CategoryResource($category);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function get_categories(Request $request)
    {
        $cacheKey = 'all_categories';
        $categories = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return new CategoryCollection(Category::all()->sortBy('name'));
        });

        return $categories;
    }

    public function get_categories_with_creatives_count(Request $request)
    {
        $categoriesWithCount = Category::withCount('creatives')->get();
        return new CategoryCreativeCollection($categoriesWithCount);
    }
}