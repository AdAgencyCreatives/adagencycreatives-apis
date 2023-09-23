<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creative\StoreCreativeRequest;
use App\Http\Requests\Creative\UpdateCreativeRequest;
use App\Http\Resources\Creative\CreativeCollection;
use App\Http\Resources\Creative\CreativeResource;
use App\Models\Creative;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class CreativeController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Creative::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('years_of_experience_id'),
                AllowedFilter::scope('name'),
                AllowedFilter::scope('state_id'),
                AllowedFilter::scope('city_id'),
                'employment_type',
                'title',
            ]);

        $creatives = $query->with('user.profile_picture', 'user.addresses.state', 'user.addresses.city')->paginate($request->per_page ?? config('global.request.pagination_limit'));

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
        $creative->save();

        return new CreativeResource($creative);
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

        $data = $request->except(['_token']);
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
