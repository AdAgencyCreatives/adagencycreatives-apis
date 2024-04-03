<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Education\UpdateEducationRequest;
use App\Http\Requests\Experience\StoreExperienceRequest;
use App\Http\Resources\Experience\ExperienceCollection;
use App\Http\Resources\Experience\ExperienceResource;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ExperienceController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Experience::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
            ]);

        $experiences = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new ExperienceCollection($experiences);
    }

    public function store(StoreExperienceRequest $request)
    {
        try {
            $user = User::where('uuid', $request->user_id)->first();
            $experiencesData = $request->input('experiences');

            $createdExperiences = [];

            foreach ($experiencesData as $experienceData) {
                $createdExperiences[] = Experience::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'company' => $experienceData['company'],
                    'description' => $experienceData['description'],
                    'started_at' => $experienceData['started_at'],
                    'completed_at' => $experienceData['completed_at'],
                ]);
            }

            return new ExperienceCollection($createdExperiences);
        } catch (\Exception $e) {
            return ApiResponse::error('ExpS-01 ' . $e->getMessage(), 400);
        }
    }

    public function update(UpdateEducationRequest $request)
    {
        $user = $request->user();

        foreach ($request->input('experiences') as $experienceData) {

            if ($this->isEmptyEducationData($experienceData)) {
                continue;
            }

            $experience = Experience::where('uuid', $experienceData['id'])->first();

            if ($experience) {
                $experience->update($experienceData);
            } else {
                Experience::create(array_merge($experienceData, [
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                ]));
            }
        }
        $experiences = Experience::where('user_id', $user->id)->get();

        return new ExperienceCollection($experiences);
    }

    private function isEmptyEducationData($experienceData)
    {
        return empty(array_filter($experienceData, function ($value) {
            return $value !== null;
        }));
    }

    public function destroy($uuid)
    {
        try {
            $experience = Experience::where('uuid', $uuid)->firstOrFail();
            $experience->delete();

            return new ExperienceResource($experience);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
