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

class ArticlesController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Article::class)
            ->allowedFilters([
                'title',
                'sub_title',
            ])
            ->defaultSort('article_date')
            ->allowedSorts('article_date', 'title', 'sub_title');

        $articles = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new ArticleCollection($articles);
    }

    public function store(StoreArticleRequest $request)
    {
        try {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);

            $article = Article::create($request->all());

            return new ArticleResource($article);
        } catch (\Exception $e) {
            throw new ApiException($e, 'SS-01');
        }
    }

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

    public function update(UpdateArticleRequest $request, $uuid)
    {
        try {
            $article = Article::where('uuid', $uuid)->first();
            $article->update($request->only('title', 'sub_title', 'article_date', 'description'));
            return new ArticleResource($article);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

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

    public function get_articles()
    {
        $cacheKey = 'all_articles';
        $articles = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return new ArticleCollection(Article::orderBy('article_date', 'desc')->get());
        });

        return $articles;
    }
}
