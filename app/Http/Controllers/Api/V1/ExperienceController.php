<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\StoreExperienceRequest;
use App\Http\Resources\Experience\ExperienceCollection;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Resume;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class ExperienceController extends Controller
{

    public function index()
    {
        $experiences = Experience::paginate(config('global.request.pagination_limit'));

        return new ExperienceCollection($experiences);
    }

    public function store(StoreExperienceRequest $request)
    {
        try {
            $resume = Resume::where('uuid', $request->resume_id)->first();
            $experiencesData = $request->input('experiences');

            $createdExperiences = [];

            foreach ($experiencesData as $experienceData) {
                $createdExperiences[] = Experience::create([
                    'uuid' => Str::uuid(),
                    'resume_id' => $resume->id,
                    'title' => $experienceData['title'],
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

    public function update(UpdateEducationRequest $request, $uuid)
    {
        try {
            $education = Education::where('uuid', $uuid)->firstOrFail();
            $education->update($request->all());

            return new EducationResource($education);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }

    }

    public function destroy($uuid)
    {
        try {
            $education = Education::where('uuid', $uuid)->firstOrFail();
            $education->delete();

            return new EducationResource($education);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
