<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Resources\Note\NoteCollection;
use App\Http\Resources\Note\NoteResource;
use App\Models\Application;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Note::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
            ]);

        if ($request->has('resource_type')) {
            $resourceType = $request->resource_type;
            $modelClass = $this->getResourceModelClass($resourceType);

            if ($modelClass) {
                $query->where('notable_type', $modelClass);

                if ($request->has('resource_id')) {
                    $resource = $modelClass::where('uuid', $request->resource_id)->first();
                    $query->where('notable_id', $resource->id);
                }
            }
        }

        $notes = $query->paginate(config('global.request.pagination_limit'));

        return new NoteCollection($notes);
    }

    public function store(StoreNoteRequest $request)
    {
        $user = $request->user();
        // $application = Application::where('uuid', $request->application_id)->first();

        $modelAlias = $request->resource_type;
        $model_uuid = $request->resource_id;
        $modelClass = Bookmark::$modelAliases[$modelAlias] ?? null; //we are using same methods from Bookmark Model, no need to create duplicate methods
        $model_id = Bookmark::getIdByUUID($modelClass, $model_uuid);

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'notable_type' => $modelClass,
            'notable_id' => $model_id,
        ]);

        try {
            $note = Note::create($request->all());

            return ApiResponse::success(new NoteResource($note), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('NS-01 '.$e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $note = Note::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new NoteResource($note);
    }

    public function update(UpdateNoteRequest $request, $uuid)
    {
        try {
            $note = Note::where('uuid', $uuid)->first();
            $note->update($request->only('body'));

            return new NoteResource($note);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $note = Note::where('uuid', $uuid)->firstOrFail();
            $note->delete();

            return ApiResponse::success($note, 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    private function getResourceModelClass($resourceType)
    {
        $resourceModels = [
            'creatives' => 'App\Models\Creative',
            'agencies' => 'App\Models\Agency',
            'jobs' => 'App\Models\Job', // Add the appropriate model for each resource_type
            'applications' => 'App\Models\Application',
            'posts' => 'App\Models\Post',
        ];

        return $resourceModels[$resourceType] ?? null;
    }
}
