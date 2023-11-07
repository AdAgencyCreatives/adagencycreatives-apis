<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Models\CreativeSpotlight;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreativeSpotlight\StoreCreativeSpotlightRequest;
use App\Http\Resources\CreativeSpotlight\CreativeSpotlightCollection;
use App\Http\Resources\CreativeSpotlight\CreativeSpotlightResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CreativeSpotlightController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(CreativeSpotlight::class)
            ->allowedFilters([
                AllowedFilter::exact('slug'),
                'status',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts('created_at');

        $creatives = $query->with([
        ])->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new CreativeSpotlightCollection($creatives);
    }



    public function store(Request $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $attachment = $this->storeVideo($request, $user, 'pending');

        return new CreativeSpotlightResource($attachment);
    }

    public function update(Request $request, $uuid)
    {
        // dd($request->all());
        try {
            $spotlight = CreativeSpotlight::where('uuid', $uuid)->firstOrFail();
            $spotlight->update($request->only('status'));

            return new CreativeSpotlightResource($spotlight);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        $creative = CreativeSpotlight::where('uuid', $uuid)->first();
        if($creative) {
            $creative->delete();
            return ApiResponse::success('CreativeSpot deleted successfully');
        } else {
            return ApiResponse::error('CreativeSpot not found', 400);
        }
    }

    public function storeVideo($request, $user, $status)
    {
        $uuid = Str::uuid();
        $file = $request->file;

        $folder = 'creative_spotlight/' . $uuid;
        $filePath = Storage::disk('s3')->put($folder, $file);

        $filename = $file->getClientOriginalName();

        if($user->creative?->title) {
            $title = sprintf("%s, %s", $user->creative?->title, $user->full_name);
        } else {
            $title = $user->full_name;
        }

        $attachment = CreativeSpotlight::create([
            'uuid' => $uuid,
            'title' => $title,
            'path' => $filePath,
            'name' => $filename,
            'slug' => Str::slug($user->full_name),
            'status' => $status,
        ]);

        return $attachment;
    }
}