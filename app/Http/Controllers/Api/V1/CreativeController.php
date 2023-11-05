<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creative\StoreCreativeRequest;
use App\Http\Requests\Creative\UpdateCreativeProfileRequest;
use App\Http\Requests\Creative\UpdateCreativeRequest;
use App\Http\Resources\Creative\CreativeCollection;
use App\Http\Resources\Creative\CreativeResource;
use App\Http\Resources\Creative\CreativeSpotlightCollection;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Creative;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class CreativeController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Creative::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('years_of_experience_id'),
                AllowedFilter::scope('name'),
                AllowedFilter::scope('email'),
                AllowedFilter::scope('state_id'),
                AllowedFilter::scope('city_id'),
                AllowedFilter::scope('status'),
                'employment_type',
                'title',
                'slug',
                'is_featured',
                'is_urgent',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts('created_at');

        $query->whereHas('user', function ($userQuery) {
            $userQuery->where('is_visible', true);
        });
        $creatives = $query->with([
            'user.profile_picture',
            'user.addresses.state',
            'user.addresses.city',
            'user.personal_phone',
            'category',
        ])->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new CreativeCollection($creatives);
    }

    public function store(StoreCreativeRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();

        $creative = Creative::where('user_id', $user->id)->first();
        if ($creative) {
            return response()->json([
                'message' => 'Creative already exists.',
                'data' => new CreativeResource($creative),
            ], Response::HTTP_CONFLICT);
        }

        $category = Category::where('uuid', $request->category_id)->first();
        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'industry_experience' => ''.implode(',', $request->industry_experience ?? []).'',
            'media_experience' => ''.implode(',', $request->media_experience ?? []).'',
            'strengths' => ''.implode(',', $request->strengths ?? []).'',
        ]);

        $creative = Creative::create($request->all());

        return new CreativeResource($creative);
    }

    public function show($uuid)
    {
        $creative = Creative::where('uuid', $uuid)->first();

        if (! $creative) {
            return response()->json([
                'message' => 'No record found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return new CreativeResource($creative);
    }

    public function update(UpdateCreativeRequest $request, $uuid)
    {
        if (empty($request->all())) {
            return response()->json([
                'message' => 'You must provide data to update',
            ], Response::HTTP_NOT_FOUND);
        }

        $creative = Creative::where('uuid', $uuid)->first();

        if (! $creative) {
            return response()->json([
                'message' => 'No creative found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $request->except(['_token']);
        foreach ($data as $key => $value) {
            $creative->$key = $value;
        }
        $creative_updated = $creative->save();
        if ($creative_updated) {
            $creative->fresh();

            return response()->json([
                'message' => 'Creative updated successfully.',
                'data' => new CreativeResource($creative),
            ], Response::HTTP_OK);
        }
    }

    public function destroy($uuid)
    {
        $deleted = Creative::where('uuid', $uuid)->delete();
        if ($deleted) {
            return response()->json([
                'message' => 'Creative deleted successfully.',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'No record found.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function creative_spotlight(Request $request)
    {
        $creative_spotlights = Attachment::with('user.creative.category')
            ->where('resource_type', 'creative_spotlight')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new CreativeSpotlightCollection($creative_spotlights);
    }

    public function update_profile(UpdateCreativeProfileRequest $request, $uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $creative = $user->creative;

            if (! $creative) {
                return response()->json([
                    'message' => 'No creative found.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Update User
            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'is_visible' => $request->show_profile,
            ];

            $user->fill(array_filter($userData, function ($value) {
                return ! is_null($value);
            }));
            $user->save();

            updateLocation($request, $user, 'personal');
            // Update Phone, Location, and Links
            if ($request->has('phone_number')) {
                updatePhone($user, $request->phone_number, 'personal');
            }
            if ($request->input('linkedin')) {
                updateLink($user, $request->input('linkedin'), 'linkedin');
            }
            if ($request->input('portfolio_website')) {
                updateLink($user, $request->input('portfolio_website'), 'portfolio_website');
            }

            return response()->json([
                'message' => 'Creative updated successfully.',
                'data' => new CreativeResource($creative),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    public function update_resume(Request $request, $uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $creative = $user->creative;

            if (! $creative) {
                return response()->json([
                    'message' => 'No creative found.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Update Creative
            $creativeData = [
                'title' => $request->title,
                'employment_type' => $request->employment_type,
                'years_of_experience' => $request->years_of_experience,
                'about' => $request->about,
                'is_remote' => $request->is_remote,
                'is_hybrid' => $request->is_hybrid,
                'is_onsite' => $request->is_onsite,
                'is_opentorelocation' => $request->is_opentorelocation,
                'industry_experience' => implode(',', array_slice($request->industry_experience ?? [], 0, 10)),
                'media_experience' => implode(',', array_slice($request->media_experience ?? [], 0, 10)),
                'strengths' => implode(',', array_slice($request->strengths ?? [], 0, 5)),
            ];

            if ($request->category_id) {
                $category = Category::where('uuid', $request->category_id)->first();
                if ($category) {
                    $creativeData['category_id'] = $category->id;
                }
            }

            $creative->fill(array_filter($creativeData, function ($value) {
                return ! is_null($value);
            }));
            $creative->save();

            updateLocation($request, $user, 'personal');

            return response()->json([
                'message' => 'Creative updated successfully.',
                'data' => new CreativeResource($creative),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}