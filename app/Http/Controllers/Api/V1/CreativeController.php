<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creative\StoreCreativeRequest;
use App\Http\Requests\Creative\UpdateCreativeProfileRequest;
use App\Http\Requests\Creative\UpdateCreativeRequest;
use App\Http\Resources\Creative\CreativeCollection;
use App\Http\Resources\Creative\CreativeResource;
use App\Http\Resources\Creative\CreativeSpotlightCollection;
use App\Http\Resources\Creative\HomepageCreativeCollection;
use App\Http\Resources\Creative\HomepageCreativeResource;
use App\Http\Resources\Creative\LoggedinCreativeCollection;
use App\Http\Resources\Creative\LoggedinCreativeResource;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Creative;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class CreativeController extends Controller
{
    public function search1(Request $request)
    {
        $search = $request->search;
        $terms = explode(',', $search);

        // Search via First or Last Name
        $sql = "SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "ur.first_name LIKE '%" . trim($term) . "%'" . "\n";
            $sql .= " OR ur.last_name LIKE '%" . trim($term) . "%'" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via City Name
        $sql .= "SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "(lc.parent_id IS NOT NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via State Name
        $sql .= "SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "(lc.parent_id IS NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $res = DB::select($sql);
        $creativeIds = collect($res)->pluck('id')->toArray();

        $creatives = Creative::whereIn('id', $creativeIds)
        ->whereHas('user', function ($query) {
            $query->where('is_visible', 1);
        })
        ->orderByDesc('is_featured')
        ->orderBy('created_at')
        ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new LoggedinCreativeCollection($creatives);
    }

    public function search2(Request $request)
    {
        $search = $request->search;
        $terms = explode(',', $search);

        // Search via First or Last Name
        $sql = "SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "ur.first_name LIKE '%" . trim($term) . "%'" . "\n";
            $sql .= " OR ur.last_name LIKE '%" . trim($term) . "%'" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via City Name
        $sql .= "SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "(lc.parent_id IS NOT NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via State Name
        $sql .= "SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "(lc.parent_id IS NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via Industry Title (a.k.a Category)
        $sql .= "SELECT cr.id FROM creatives cr INNER JOIN categories ca ON cr.category_id = ca.id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "(ca.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $res = DB::select($sql);
        $creativeIds = collect($res)->pluck('id')->toArray();

        $creatives = Creative::whereIn('id', $creativeIds)
        ->whereHas('user', function ($query) {
            $query->where('is_visible', 1);
        })
        ->orderByDesc('is_featured')
        ->orderBy('created_at')
        ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new LoggedinCreativeCollection($creatives);



    }

    public function search3(Request $request)
    {
        $search = $request->search;
        $terms = explode(',', $search);


        // Search via First or Last Name
        $sql = "SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "ur.first_name LIKE '%" . trim($term) . "%'" . "\n";
            $sql .= " OR ur.last_name LIKE '%" . trim($term) . "%'" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via City Name
        $sql .= "SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "(lc.parent_id IS NOT NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via State Name
        $sql .= "SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "(lc.parent_id IS NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via Industry Title (a.k.a Category)
        $sql .= "SELECT cr.id FROM creatives cr INNER JOIN categories ca ON cr.category_id = ca.id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "(ca.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via Industry Experience
        $sql .= "SELECT cr.id FROM creatives cr JOIN industries ind ON FIND_IN_SET(ind.uuid, cr.industry_experience) > 0" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "ind.name LIKE '%" . trim($term) . "%'" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via Media Experience
        $sql .= "SELECT cr.id FROM creatives cr JOIN medias md ON FIND_IN_SET(md.uuid, cr.media_experience) > 0" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "md.name LIKE '%" . trim($term) . "%'" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via Strengths
        $sql .= "SELECT cr.id FROM creatives cr JOIN strengths st ON FIND_IN_SET(st.uuid, cr.strengths) > 0" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "st.name LIKE '%" . trim($term) . "%'" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        // Search via Employment Type
        $sql .= "SELECT cr.id FROM creatives cr" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "cr.employment_type LIKE '%" . trim($term) . "%'" . "\n";
        }

        $workplace_preferences = array(
            "featured" => "is_featured",
            "urgent" => "is_urgent",
            "remote" => "is_remote",
            "hybrid" => "is_hybrid",
            "on site" => "is_onsite",
            "open to relocation" => "is_opentorelocation",
        );

        // Search via Workplace Preference
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            if(isset($workplace_preferences[$term])) {
                $sql .= ($i == 0 ? ("UNION DISTINCT" . "\n" . "SELECT cr.id FROM creatives cr WHERE ") . "\n" : " OR ") . $workplace_preferences[$term] . "=1" . "\n";
            }
        }

        $res = DB::select($sql);
        $creativeIds = collect($res)->pluck('id')->toArray();

        $creatives = Creative::whereIn('id', $creativeIds)
        ->whereHas('user', function ($query) {
            $query->where('is_visible', 1);
        })
        ->orderByDesc('is_featured')
        ->orderBy('created_at')
        ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new LoggedinCreativeCollection($creatives);
    }

    public function index(Request $request)
    {
        $filters = $request->all();

        if (isset($filters['filter']['slug'])) {
            $slug = $filters['filter']['slug'];
            $logged_in_user = request()->user();

            $current_creative = Creative::where('user_id', $logged_in_user->id)->first();
            if($current_creative && $current_creative->slug == $slug) {
                unset($filters['filter']['is_visible']);
                $request->replace($filters);
            }
        }
        $query = QueryBuilder::for(Creative::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('years_of_experience_id'),
                AllowedFilter::scope('name'),
                AllowedFilter::scope('email'),
                AllowedFilter::scope('state_id'),
                AllowedFilter::scope('city_id'),
                AllowedFilter::scope('status'),
                AllowedFilter::scope('is_visible'),
                'employment_type',
                'title',
                'slug',
                'is_featured',
                'is_urgent',
            ])
            ->defaultSort('-is_featured', '-created_at')
            ->allowedSorts('created_at', 'is_featured');

        // dd($query->toSql());
        $creatives = $query->with([
            'user.profile_picture',
            'user.addresses.state',
            'user.addresses.city',
            'user.personal_phone',
            'category',
        ])->paginate($request->per_page ?? config('global.request.pagination_limit'));


        if (isset($filters['filter']['slug'])) { //Means user profile is being viewed on creatives page
            if ($creatives->count() === 1) { //Check if the collection count is 1 and update views if true
                $creative = $creatives->first();
                $creative->increment('views');
                $creative->save();
            }
        }


        return new LoggedinCreativeCollection($creatives);
    }


    public function homepage_creatives(Request $request) //Home page creatives
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
                AllowedFilter::scope('is_visible'),
                'employment_type',
                'title',
                'slug',
                'is_featured',
                'is_urgent',
            ])
            ->defaultSort('-is_featured', '-created_at')
            ->allowedSorts('created_at', 'is_featured');

        $creatives = $query->with([
            'user.profile_picture',
            'user.addresses.state',
            'user.addresses.city',
            'user.personal_phone',
            'category',
        ])->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new HomepageCreativeCollection($creatives);
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
            'industry_experience' => '' . implode(',', $request->industry_experience ?? []) . '',
            'media_experience' => '' . implode(',', $request->media_experience ?? []) . '',
            'strengths' => '' . implode(',', $request->strengths ?? []) . '',
        ]);

        $creative = Creative::create($request->all());

        return new CreativeResource($creative);
    }

    public function show($uuid)
    {
        $creative = Creative::where('uuid', $uuid)->first();

        if (!$creative) {
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

        if (!$creative) {
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

    public function update_profile(Request $request, $uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $creative = $user->creative;

            if (!$creative) {
                return response()->json([
                    'message' => 'No creative found.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Update User
            $userData = [];

            if ($request->filled('first_name')) {
                $userData['first_name'] = $request->first_name;
            }

            if ($request->filled('last_name')) {
                $userData['last_name'] = $request->last_name;
            }

            if ($request->filled('username')) {
                $userData['username'] = $request->username;
            }

            if ($request->filled('show_profile')) {
                $userData['is_visible'] = $request->show_profile ? 1 : 0;
            }

            $user->fill($userData);
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

            if (!$creative) {
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
                return !is_null($value);
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