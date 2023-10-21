<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Resources\Note\NoteCollection;
use App\Http\Resources\Note\NoteResource;
use App\Models\Application;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class NoteController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(Note::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('application_id'),
            ]);

        $notes = $query->paginate(config('global.request.pagination_limit'));

        return new NoteCollection($notes);
    }

    public function store(StoreNoteRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $application = Application::where('uuid', $request->application_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'application_id' => $application->id,
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
}
