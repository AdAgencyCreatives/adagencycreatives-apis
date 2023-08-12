<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Link\StoreLinkRequest;
use App\Http\Requests\Link\UpdateLinkRequest;
use App\Http\Resources\Link\LinkCollection;
use App\Http\Resources\Link\LinkResource;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::paginate(config('global.request.pagination_limit'));

        return new LinkCollection($links);
    }

    public function store(StoreLinkRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
        ]);
        try {
            $application = Link::create($request->all());

            return ApiResponse::success(new LinkResource($application), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('LS-01 '.$e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $link = Link::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new LinkResource($link);
    }

    public function update(UpdateLinkRequest $request, $uuid)
    {
        try {
            $link = Link::where('uuid', $uuid)->first();
            $link->update($request->only('url'));

            return new LinkResource($link);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $link = Link::where('uuid', $uuid)->firstOrFail();
            $link->delete();

            return ApiResponse::success($link, 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
