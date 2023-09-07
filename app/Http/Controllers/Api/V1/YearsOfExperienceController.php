<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\YearsOfExperience;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class YearsOfExperienceController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'years_of_experience';
        $yearsOfExperience = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return new CategoryCollection(YearsOfExperience::paginate(100));
        });

        return $yearsOfExperience;
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);

            $yearsOfExperience = YearsOfExperience::create($request->all());

            return new CategoryResource($yearsOfExperience);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }

    public function update(UpdateMediaRequest $request, $uuid)
    {
        try {
            $yearsOfExperience = YearsOfExperience::where('uuid', $uuid)->first();
            $yearsOfExperience->update($request->only('name'));

            return new CategoryResource($yearsOfExperience);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $yearsOfExperience = YearsOfExperience::where('uuid', $uuid)->firstOrFail();
            $yearsOfExperience->delete();

            return new CategoryResource($yearsOfExperience);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }
}
