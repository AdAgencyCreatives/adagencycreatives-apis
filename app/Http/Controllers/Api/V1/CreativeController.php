<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creative\StoreCreativeRequest;
use App\Http\Requests\Creative\UpdateCreativeRequest;
use App\Http\Resources\Creative\CreativeCollection;
use App\Http\Resources\Creative\CreativeResource;
use App\Models\Creative;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class CreativeController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(Creative::class)
        ->allowedFilters([
            AllowedFilter::scope('user_id'),
            'years_of_experience', 
            'type_of_work'
        ]);

        $creatives = $query->paginate(config('global.request.pagination_limit'));

        return new CreativeCollection($creatives);
    }

    public function store(StoreCreativeRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();

        $creative = Creative::where('user_id', $user->id)->first();
        if ($creative) {
            return response()->json([
                'message' => 'Creative already exists.',
                'data' => new CreativeResource($creative),
            ], Response::HTTP_CONFLICT);
        }
        $creative = new Creative();
        $creative->uuid = Str::uuid();
        $creative->user_id = $user->id;
        $creative->years_of_experience = $request->years_of_experience;
        $creative->type_of_work = $request->type_of_work;
        $creative_created = $creative->save();

        if ($creative_created) {
            return response()->json([
                'message' => 'Creative created successfully.',
                'data' => new CreativeResource($creative),
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'message' => 'Something went wrong',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        dd($request->all());
    }

    public function show($uuid)
    {
        $creative = Creative::where('uuid', $uuid)->first();

        if (! $creative) {
            return response()->json([
                'message' => 'No record found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return new CreativeResource($creative);
    }

    public function update(UpdateCreativeRequest $request, $uuid)
    {
        if (empty($request->all())) {
            return response()->json([
                'message' => 'You must provide data to update',
            ], Response::HTTP_NOT_FOUND);
        }

        $creative = Creative::where('uuid', $uuid)->first();

        if (! $creative) {
            return response()->json([
                'message' => 'No creative found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $request->all();
        foreach ($data as $key => $value) {
            $creative->$key = $value;
        }
        $creative_updated = $creative->save();
        if ($creative_updated) {
            $creative->fresh();

            return response()->json([
                'message' => 'Creative updated successfully.',
                'data' => new CreativeResource($creative),
            ], Response::HTTP_OK);
        }
    }

    public function destroy($uuid)
    {
        $deleted = Creative::where('uuid', $uuid)->delete();
        if ($deleted) {
            return response()->json([
                'message' => 'Creative deleted successfully.',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'No record found.',
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
