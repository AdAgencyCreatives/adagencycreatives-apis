<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Group\GroupCollection;
use App\Http\Resources\Group\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Group::class)
            ->allowedFilters([
                'name',
                'status',
            ]);

        $groups = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new GroupCollection($groups);
    }

    public function create()
    {
        return view('pages.groups.add');
    }

    public function show(Group $group)
    {
    }

    public function edit(Group $group)
    {
        //
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
