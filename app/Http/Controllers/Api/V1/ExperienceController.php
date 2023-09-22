<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Education\UpdateEducationRequest;
use App\Http\Requests\Experience\StoreExperienceRequest;
use App\Http\Resources\Experience\ExperienceCollection;
use App\Http\Resources\Experience\ExperienceResource;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            return ApiResponse::error('ExpS-01 '.$e->getMessage(), 400);
        }
    }

    public function update(UpdateEducationRequest $request, $uuid)
    {
        try {
            $education = Experience::where('uuid', $uuid)->firstOrFail();
            $education->update($request->all());

            return new ExperienceResource($education);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
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
