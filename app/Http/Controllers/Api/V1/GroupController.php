<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\StoreGroupRequest;
use App\Http\Resources\Group\GroupCollection;
use App\Http\Resources\Group\GroupResource;
use App\Models\Attachment;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Group::class)
            ->allowedFilters([
                'name',
                'status',
                AllowedFilter::scope('user_id'),
            ]);

        $groups = $query->with('attachment')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new GroupCollection($groups);
    }

    public function create()
    {
        return view('pages.groups.add');
    }

    public function store(StoreGroupRequest $request)
    {
        $user = $request->user();

        $group = Group::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'status' => $request->status ?? 'public',
        ]);

        if ($request->hasFile('file')) {
            $attachment = storeImage($request, $user->id, 'cover_image');

            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $group->id,
                ]);
            }
        }

        return new GroupResource($group);
    }

    public function update(Request $request, $uuid)
    {
        try {
            $group = Group::where('uuid', $uuid)->firstOrFail();
            $group->update($request->only('status'));

            return new GroupResource($group);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function destroy($uuid)
    {
        try {
            $group = Group::where('uuid', $uuid)->firstOrFail();
            $group->delete();

            return new GroupResource($group);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function get_groups(Request $request)
    {
        $cacheKey = 'all_groups';
        $groups = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return Group::select('uuid', 'name', 'status')->get();
        });

        return $groups;
    }
}