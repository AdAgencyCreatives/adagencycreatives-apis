<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\StoreAgencyRequest;
use App\Http\Requests\Agency\UpdateAgencyRequest;
use App\Http\Resources\Agency\AgencyCollection;
use App\Http\Resources\Agency\AgencyResource;
use App\Models\Agency;
use App\Models\Industry;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class AgencyController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->all();
        $industries = $this->processIndustryExperience($request, $filters);
        $medias = $this->processMediaExperience($request, $filters);

        // dd($industries);
        $query = QueryBuilder::for(Agency::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('state_id'),
                AllowedFilter::scope('city_id'),
                AllowedFilter::scope('status'),
                AllowedFilter::scope('is_visible'),
                'size',
                'name',
                'slug',
                'is_featured',
                'is_urgent',
            ])
            ->defaultSort('-is_featured', '-created_at')
            ->allowedSorts('created_at', 'is_featured');

        $agency_user_ids = User::where('role', 3)->pluck('id');

        if ($industries !== null) {
            $this->applyExperienceFilter($query, $industries, 'industry_experience');

        }

        if ($medias !== null) {
            $this->applyExperienceFilter($query, $medias, 'media_experience');
        }

        $agencies = $query
            ->with([
                'user.addresses.state',
                'user.addresses.city',
                'user.links',
                'user.business_phone',
            ])
            ->whereIn('user_id', $agency_user_ids)
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new AgencyCollection($agencies);
    }

    private function applyExperienceFilter($query, $experience, $experienceType)
    {
        $query->whereIn('id', function ($query) use ($experience, $experienceType) {
            $query->select('id')
                ->from('agencies')
                ->where(function ($q) use ($experience, $experienceType) {
                    foreach ($experience as $targetId) {
                        $q->orWhereRaw("FIND_IN_SET(?, $experienceType)", [$targetId]);
                    }
                });
        });
    }

    public function store(StoreAgencyRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();

        $agency = Agency::where('user_id', $user->id)->first();
        if ($agency) {
            return response()->json([
                'message' => 'Agency already exists.',
                'data' => new AgencyResource($agency),
            ], Response::HTTP_CONFLICT);
        }

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'industry_experience' => '' . implode(',', $request->industry_experience ?? []) . '',
            'media_experience' => '' . implode(',', $request->media_experience ?? []) . '',
        ]);

        $agency = Agency::create($request->all());

        if ($agency) {
            return response()->json([
                'message' => 'Agency created successfully.',
                'data' => new AgencyResource($agency),
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'message' => 'Something went wrong',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($uuid)
    {
        $agency = Agency::with('attachment')->where('uuid', $uuid)->first();
        if (!$agency) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new AgencyResource($agency);
    }

    public function update(UpdateAgencyRequest $request, $uuid)
    {
        if (empty($request->all())) {
            return response()->json([
                'message' => 'You must provide data to update',
            ], Response::HTTP_NOT_FOUND);
        }

        $agency = Agency::where('uuid', $uuid)->first();

        if (!$agency) {
            return response()->json([
                'message' => 'No agency found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $request->except(['_token']);
        foreach ($data as $key => $value) {
            $agency->$key = $value;
        }
        $agency_updated = $agency->save();
        if ($agency_updated) {
            $agency->fresh();

            return response()->json([
                'message' => 'Agency updated successfully.',
                'data' => new AgencyResource($agency),
            ], Response::HTTP_OK);
        }
    }

    public function update_profile(Request $request, $uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->first();
            $agency = Agency::where('user_id', $user->id)->first();

            if (!$agency) {
                return response()->json([
                    'message' => 'No agency found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $agency->name = $request->company_name;
            $agency->size = $request->size;
            $agency->about = $request->about;
            $agency->slug = $request->slug;
            $agency->is_remote = $request->is_remote;
            $agency->is_hybrid = $request->is_hybrid;
            $agency->is_onsite = $request->is_onsite;
            $agency->industry_experience = implode(',', array_slice($request->industry_experience ?? [], 0, 10));
            $agency->media_experience = implode(',', array_slice($request->media_experience ?? [], 0, 10));
            $agency->save();

            // Update User
            $userData = [];

            if ($request->filled('first_name') || $request->first_name === null) {
                $userData['first_name'] = $request->first_name;
            }

            if ($request->filled('last_name') || $request->last_name === null) {
                $userData['last_name'] = $request->last_name;
            }

            if ($request->filled('email') || $request->email === null) {
                $userData['email'] = $request->email;
            }

            if ($request->filled('show_profile') || $request->show_profile === null) {
                $userData['is_visible'] = $request->show_profile;
            }

            $user->fill($userData);
            $user->save();

            updateLocation($request, $user, 'business');
            if ($request->has('phone_number')) {
                updatePhone($user, $request->phone_number, 'business');
            }

            updateLink($user, $request->linkedin, 'linkedin');
            updateLink($user, $request->website, 'website');

            return response()->json([
                'message' => 'Agency updated successfully.',
                'data' => new AgencyResource($agency),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    public function destroy($uuid)
    {
        $deleted = Agency::where('uuid', $uuid)->delete();
        if ($deleted) {
            return response()->json([
                'message' => 'Agency deleted successfully.',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'No record found.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function processIndustryExperience(Request $request, &$filters, $experienceKey = 'industry_experience')
    {
        if (!isset($filters['filter'][$experienceKey])) {
            return null;
        }

        $experience_ids = $filters['filter'][$experienceKey];
        unset($filters['filter'][$experienceKey]);
        $request->replace($filters);

        $experience_ids = $experience_ids ? explode(',', $experience_ids) : [];

        return Industry::whereIn('uuid', $experience_ids)->pluck('uuid')->toArray();
    }

    public function processMediaExperience(Request $request, &$filters, $experienceKey = 'media_experience')
    {
        if (!isset($filters['filter'][$experienceKey])) {
            return null;
        }

        $experience_ids = $filters['filter'][$experienceKey];
        unset($filters['filter'][$experienceKey]);
        $request->replace($filters);

        $experience_ids = $experience_ids ? explode(',', $experience_ids) : [];

        return Media::whereIn('uuid', $experience_ids)->pluck('uuid')->toArray();
    }
}