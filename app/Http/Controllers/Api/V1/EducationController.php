<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Education\StoreEducationRequest;
use App\Http\Requests\Education\UpdateEducationRequest;
use App\Http\Resources\Education\EducationCollection;
use App\Http\Resources\Education\EducationResource;
use App\Models\Education;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EducationController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Education::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
            ]);

        $educations = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new EducationCollection($educations);
    }

    public function store(StoreEducationRequest $request)
    {
        try {
            $user = User::where('uuid', $request->user_id)->first();
            $educationsData = $request->input('educations');

            $createdEducations = [];

            foreach ($educationsData as $educationData) {
                $createdEducations[] = Education::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'degree' => $educationData['degree'],
                    'college' => $educationData['college'],
                    'completed_at' => $educationData['completed_at'],
                ]);
            }

            return new EducationCollection($createdEducations);
        } catch (\Exception $e) {
            return ApiResponse::error('EdS-01 '.$e->getMessage(), 400);
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
