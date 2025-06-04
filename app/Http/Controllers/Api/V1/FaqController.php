<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Faq\StoreFaqRequest;
use App\Http\Requests\Faq\UpdateFaqRequest;
use App\Http\Resources\Faq\FaqCollection;
use App\Http\Resources\Faq\FaqResource;
use App\Models\Faq;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Faq::class)
            ->allowedFilters([
                'title',
            ])
            ->defaultSort('order')
            ->allowedSorts('order', 'title');

        $faqs = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new FaqCollection($faqs);
    }

    public function store(StoreFaqRequest $request)
    {
        try {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);

            $faq = Faq::create($request->all());

            return new FaqResource($faq);
        } catch (\Exception $e) {
            throw new ApiException($e, 'SS-01');
        }
    }

    public function show($uuid)
    {
        try {
            $faq = Faq::where('uuid', $uuid)->firstOrFail();
            return new FaqResource($faq);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function update(UpdateFaqRequest $request, $id)
    {
        try {
            $faq = Faq::where('id', $id)->first();
            $faq->update($request->only('title', 'description', 'order'));
            return new FaqResource($faq);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $faq = Faq::where('uuid', $uuid)->firstOrFail();
            $faq->delete();

            return new FaqResource($faq);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function get_faqs()
    {
        $cacheKey = 'all_faqs';
        $faqs = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return new FaqCollection(Faq::orderBy('order', 'asc')->get());
        });

        return $faqs;
    }
}