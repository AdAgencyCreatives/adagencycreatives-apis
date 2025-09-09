<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Resources\Article\ArticleCollection;
use App\Http\Resources\Article\ArticleResource;
use App\Models\Article;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use Carbon\Carbon;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource with pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ArticleCollection
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Article::class)
            ->allowedFilters([
                'title',
                'order',
                'sub_title',
                'is_featured'
            ])
            ->defaultSort('article_date')
            ->allowedSorts('article_date', 'title', 'sub_title', 'is_featured', 'order');

        $articles = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new ArticleCollection($articles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Article\StoreArticleRequest  $request
     * @return ArticleResource
     * @throws ApiException
     */
    public function store(StoreArticleRequest $request)
    {
        try {
            $data = $request->validated();
            $data['uuid'] = Str::uuid();

            // Handle featured status
            if (isset($data['is_featured']) && $data['is_featured']) {
                $data['featured_at'] = Carbon::now();
            }

            $article = Article::create($data);

            return new ArticleResource($article);
        } catch (\Exception $e) {
            throw new ApiException($e, 'SS-01');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse|ArticleResource
     * @throws ModelNotFound
     */
    public function show($uuid)
    {
        try {
            $article = Article::where('uuid', $uuid)->firstOrFail();
            return new ArticleResource($article);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Article\UpdateArticleRequest  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse|ArticleResource
     */
    public function update(UpdateArticleRequest $request, $uuid)
    {
        try {
            $article = Article::where('uuid', $uuid)->first();
            $data = $request->only('title', 'sub_title', 'article_date', 'description', 'is_featured');

            // Handle featured status on update
            if (isset($data['is_featured']) && $data['is_featured'] && !$article->is_featured) {
                $data['featured_at'] = Carbon::now();
            }

            $article->update($data);
            return new ArticleResource($article);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse|ArticleResource
     * @throws ModelNotFound
     */
    public function destroy($uuid)
    {
        try {
            $article = Article::where('uuid', $uuid)->firstOrFail();
            $article->delete();

            return new ArticleResource($article);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    /**
     * Display the latest six articles, prioritized by featured status.
     *
     * @return ArticleCollection
     */
    public function getLatestPosts()
    {
        $articles = Article::orderByRaw('is_featured DESC, featured_at DESC')->take(6)->get();
        return new ArticleCollection($articles);
    }

    public function get_articles()
    {
        $cacheKey = 'all_articles';
        $articles = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return new ArticleCollection(Article::orderBy('article_date', 'desc')->get());
        });

        return $articles;
    }
}
