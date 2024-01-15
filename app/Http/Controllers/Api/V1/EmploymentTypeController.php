<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentType\StoreEmploymentTypeRequest;
use App\Http\Requests\EmploymentType\UpdateEmploymentTypeRequest ;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\EmploymentTypes;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EmploymentTypeController extends Controller
{
    public function index(Request $request)
    {
        return new CategoryCollection(EmploymentTypes::paginate(100));
    }

    public function store(StoreEmploymentTypeRequest $request)
    {
        try {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);

            $yearsOfExperience = EmploymentTypes::create($request->all());

            return new CategoryResource($yearsOfExperience);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }

    public function update(UpdateEmploymentTypeRequest $request, $uuid)
    {
        try {
            $yearsOfExperience = EmploymentTypes::where('uuid', $uuid)->first();
            $yearsOfExperience->update($request->only('name'));

            return new CategoryResource($yearsOfExperience);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $yearsOfExperience = EmploymentTypes::where('uuid', $uuid)->firstOrFail();
            $yearsOfExperience->delete();

            return new CategoryResource($yearsOfExperience);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    //for frontend
    public function get_employment_types()
    {
        $employmentTypes = Cache::remember('employment_types', 9999, function () {
            return EmploymentTypes::select('name')->get()->pluck('name');
        });

        return response()->json($employmentTypes);
    }
}
