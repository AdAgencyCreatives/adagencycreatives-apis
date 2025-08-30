<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Http\Resources\Award\AwardCollection;
use App\Http\Resources\Award\AwardResource;
use App\Models\Award;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AwardController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Award::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
            ]);

        $awards = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        $awards = $awards->sortByDesc('completed_at');

        return new AwardCollection($awards);
    }

    public function store(StoreAwardRequest $request)
    {
        try {
            $user = User::where('uuid', $request->user_id)->first();
            $awardsData = $request->input('awards');

            $createdAwards = [];

            foreach ($awardsData as $awardData) {
                $createdAwards[] = Award::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'award_title' => $awardData['award_title'],
                    'award_year' => $awardData['award_year'],
                    'award_work' => $awardData['award_work'],
                ]);
            }

            return new AwardCollection($createdAwards);
        } catch (\Exception $e) {
            return ApiResponse::error('EdS-01 ' . $e->getMessage(), 400);
        }
    }

    public function update(UpdateAwardRequest $request)
    {
        $user = $request->user();
        $awards = $request->input('awards');

        foreach ($awards as $awardData) {

            if ($this->isEmptyExperienceData($awardData)) {
                continue;
            }

            $award = Award::where('uuid', $awardData['id'])->first();

            if ($award) {
                $award->update($awardData);
            } else {
                Award::create(array_merge($awardData, [
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                ]));
            }
        }
        $awards = Award::where('user_id', $user->id)->get();

        return new AwardCollection($awards);
    }

    private function isEmptyExperienceData($experienceData)
    {
        return empty(array_filter($experienceData, function ($value) {
            return $value !== null;
        }));
    }

    public function destroy($uuid)
    {
        try {
            $award = Award::where('uuid', $uuid)->firstOrFail();
            $award->delete();

            return new AwardResource($award);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
