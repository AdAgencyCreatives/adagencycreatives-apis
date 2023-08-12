<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreAttachmentRequest;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{

    public function index()
    {
        $attachments = Attachment::paginate(config('global.request.pagination_limit'));

        return new AttachmentCollection($attachments);
    }

    public function store(StoreAttachmentRequest $request)
    {
        $uuid = Str::uuid();

        try {
            $user = User::where('uuid', $request->user_id)->first();

            $file = $request->file;
            $resource_type = $request->resource_type;
            $extension = $file->getClientOriginalExtension();
            $filename = $uuid . '.' . $extension;
            $file_path = Storage::disk('public')->putFileAs($resource_type, $file, $filename);

            $request->merge([
                'uuid' => $uuid,
                'user_id' => $user->id,
                'resource_type' => $resource_type,
                'path' => $file_path,
                'extension' => $extension,
            ]);

            $attachment = Attachment::create($request->all());
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