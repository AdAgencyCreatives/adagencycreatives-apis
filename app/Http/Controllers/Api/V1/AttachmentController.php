<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreAttachmentRequest;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AttachmentController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Attachment::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('post_id'),
                AllowedFilter::scope('resource_type'),
                AllowedFilter::scope('post_id'),
                'status',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts('created_at');

        $attachments = $query->with('user')->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new AttachmentCollection($attachments);
    }

    public function store(StoreAttachmentRequest $request)
    {
        try {
            $user = User::where('uuid', $request->user_id)->first();
            $resource_type = $request->resource_type;
            $attachment = storeImage($request, $user->id, $resource_type);

            return new AttachmentResource($attachment);
        } catch (\Exception $e) {
            throw new ApiException($e, 'ATS-001');
        }
    }

    public function show($uuid)
    {
        try {
            $attachment = Attachment::where('uuid', $uuid)->firstOrFail();

            return new AttachmentResource($attachment);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function update(Request $request, $uuid)
    {
        // dd($request->all());
        try {
            $attachment = Attachment::where('uuid', $uuid)->firstOrFail();
            $attachment->update($request->only('status'));

            return new AttachmentResource($attachment);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        /**
         * To DO: Delete file from storage
         */
        try {
            $attachment = Attachment::where('uuid', $uuid)->firstOrFail();
            $attachment->delete();

            return new AttachmentResource($attachment);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'ATD-01');
        }
    }
}
