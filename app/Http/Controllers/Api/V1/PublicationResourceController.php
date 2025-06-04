<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Http\Controllers\Controller;
use App\Http\Resources\Publication\FaqCollection;
use App\Http\Resources\Publication\PubResource;
use App\Models\Attachment;
use App\Models\PublicationResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PublicationResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(PublicationResource::class)
            ->defaultSort('sort_order')
            ->allowedSorts('link', 'created_at', 'updated_at', 'sort_order');

        $topics = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new FaqCollection($topics);
    }

    public function store(Request $request)
    {
        try {

            $resource= PublicationResource::create($request->all());

            if ($request->has('file') && is_object($request->file)) {

            $attachment = storeImage($request, auth()->id(), 'publication_resource_website_preview');

            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $resource->id,
                ]);
            }
        }

            return new PubResource($resource);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }

    public function destroy($id)
    {
        try {
            $topic = PublicationResource::where('id', $id)->firstOrFail();
            $topic->delete();

            return new PubResource($topic);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }
}
