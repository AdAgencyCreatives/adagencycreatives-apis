<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Resources\Job\JobCollection;
use App\Http\Resources\Job\JobLoggedInCollection;
use App\Http\Resources\Job\JobResource;
use App\Models\Application;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Industry;
use App\Models\Job;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->all();

        $industries = processIndustryExperience($request, $filters);
        $medias = processMediaExperience($request, $filters);

        $query = QueryBuilder::for(Job::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('advisor_id'),
                AllowedFilter::scope('category_id'),
                AllowedFilter::scope('category_slug'),
                AllowedFilter::scope('state_id'),
                AllowedFilter::scope('city_id'),
                AllowedFilter::scope('state_slug'),
                AllowedFilter::scope('city_slug'),
                AllowedFilter::scope('agency'),
                AllowedFilter::scope('slug'),
                'title',
                'employment_type',
                'apply_type',
                'salary_range',
                'is_remote',
                'is_hybrid',
                'is_onsite',
                'is_featured',
                'is_urgent',
                'status',
                AllowedFilter::trashed(),
            ])
            ->defaultSort('-featured_at', '-updated_at', "-created_at")
            ->allowedSorts('featured_at', 'updated_at', 'created_at');

        if ($industries !== null) {
            applyExperienceFilter($query, $industries, 'industry_experience', 'job_posts');
        }

        if ($medias !== null) {
            applyExperienceFilter($query, $medias, 'media_experience', 'job_posts');
        }

        $query->with('user.agency', 'category', 'state', 'city', 'attachment')
            ->withCount('applications');

        if ($request->applications_count) {
            $query->having('applications_count', '>=', $request->applications_count);
        }

        $recent_only = $request->has('recent_only') && $request->recent_only == "yes";

        if ($recent_only) {
            $query->where('status', 1);
        }

        if ($request->has('jobSearch') && strlen($request->jobSearch) > 0) {
            $query->where("title", "LIKE", "%" . str_replace(" ", "%", $request->jobSearch) . "%");
        }

        // $query->withWhereHas('applications', function ($q) use ($recent_only, $request) {
        //     if ($recent_only) {
        //         $q->where('status', 0);
        //     }
        //     $q->orderBy('status', 'asc')->orderBy('id', 'desc');

        //     if ($request->has('applicantSearch') && strlen($request->applicantSearch) > 0) {
        //         $q->whereHas('user', function ($q) use ($request) {
        //             $q->whereRaw("CONCAT(users.first_name,' ', users.last_name) LIKE '%" . $request->applicantSearch . "%'");
        //         });
        //     }
        // });

        $jobs = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new JobCollection($jobs);
    }

    public function jobs_for_logged_in(Request $request)
    {
        $filters = $request->all();

        $industries = processIndustryExperience($request, $filters);
        $medias = processMediaExperience($request, $filters);

        $query = QueryBuilder::for(Job::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('category_id'),
                AllowedFilter::scope('category_slug'),
                AllowedFilter::scope('state_id'),
                AllowedFilter::scope('city_id'),
                AllowedFilter::scope('state_slug'),
                AllowedFilter::scope('city_slug'),
                AllowedFilter::scope('slug'),
                'title',
                'slug',
                'employment_type',
                'apply_type',
                'salary_range',
                'is_remote',
                'is_hybrid',
                'is_onsite',
                'is_featured',
                'is_urgent',
                'status',
                AllowedFilter::trashed(),
            ])
            ->defaultSort('-featured_at', '-updated_at', "-created_at")
            ->allowedSorts('featured_at', 'updated_at', 'created_at');

        if ($industries !== null) {
            applyExperienceFilter($query, $industries, 'industry_experience', 'job_posts');
        }

        if ($medias !== null) {
            applyExperienceFilter($query, $medias, 'media_experience', 'job_posts');
        }

        $jobs = $query->with('user.agency', 'category', 'state', 'city', 'attachment')
            ->withCount('applications')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        $loggedInUserId = $request->user()->id;
        $userApplications = Application::where('user_id', $loggedInUserId)->pluck('job_id')->toArray();

        $jobs->getCollection()->transform(function ($job) use ($userApplications) {
            $job['user_has_applied'] = in_array($job->id, $userApplications);

            return $job;
        });

        return new JobLoggedInCollection($jobs);
    }

    public function jobs_homepage(Request $request)
    {
        $search = $request->search;
        $terms = explode(',', $search);

        // Search via City Name
        $sql = 'SELECT jp.id FROM job_posts jp INNER JOIN locations lc ON lc.id = jp.city_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via State Name
        $sql .= 'SELECT jp.id FROM job_posts jp INNER JOIN locations lc ON lc.id = jp.state_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Industry Title (a.k.a Category)
        $sql .= 'SELECT jp.id FROM job_posts jp INNER JOIN categories ca ON jp.category_id = ca.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ca.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Job Title
        $sql .= 'SELECT jp.id FROM job_posts jp' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(jp.title LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= "UNION DISTINCT" . "\n";

        //Search via Agency Name
        $sql .= "SELECT jp.id FROM job_posts jp INNER JOIN agencies ag ON jp.user_id = ag.user_id" . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? " WHERE " : " OR ") . "(ag.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $res = DB::select($sql);
        $jobIds = collect($res)->pluck('id')->toArray();

        $jobs = Job::whereIn('id', $jobIds)
            ->where('status', 1)
            ->with('user.agency', 'category', 'state', 'city', 'attachment')
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new JobCollection($jobs);
    }

    public function jobs_homepage_logged_in(Request $request)
    {
        $search = $request->search;
        $terms = explode(',', $search);

        // Search via City Name
        $sql = 'SELECT jp.id FROM job_posts jp INNER JOIN locations lc ON lc.id = jp.city_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via State Name
        $sql .= 'SELECT jp.id FROM job_posts jp INNER JOIN locations lc ON lc.id = jp.state_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Industry Title (a.k.a Category)
        $sql .= 'SELECT jp.id FROM job_posts jp INNER JOIN categories ca ON jp.category_id = ca.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ca.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Job Title
        $sql .= 'SELECT jp.id FROM job_posts jp' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(jp.title LIKE '%" . trim($term) . "%')" . "\n";
        }

        // $sql .= "UNION DISTINCT" . "\n";

        // Search via Agency Name
        // $sql .= "SELECT jp.id FROM job_posts jp INNER JOIN agencies ag ON jp.user_id = ag.user_id" . "\n";
        // for ($i = 0; $i < count($terms); $i++) {
        //     $term = $terms[$i];
        //     $sql .= ($i == 0 ? " WHERE " : " OR ") . "(ag.name LIKE '%" . trim($term) . "%')" . "\n";
        // }

        $res = DB::select($sql);
        $jobIds = collect($res)->pluck('id')->toArray();

        $jobs = Job::whereIn('id', $jobIds)
            ->where('status', 1)
            ->with('user.agency', 'category', 'state', 'city', 'attachment')
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        $loggedInUserId = $request->user()->id;
        $userApplications = Application::where('user_id', $loggedInUserId)->pluck('job_id')->toArray();

        $jobs->getCollection()->transform(function ($job) use ($userApplications) {
            $job['user_has_applied'] = in_array($job->id, $userApplications);

            return $job;
        });

        return new JobLoggedInCollection($jobs);
    }

    public function store(StoreJobRequest $request)
    {
        $user = request()->user();

        /**
         * Store advisor id in separate column and in user_id ,
         * keep putting agency id becasue advisor is also working
         * on behalf of agency
         */

        $advisor = null;
        if (in_array($user->role, ['advisor', 'recruiter'])) {
            if ($request->has('agency_id')) {
                $advisor = $user;
                $request->merge([
                    'advisor_id' => $advisor->id,
                ]);

                $user = User::where('uuid', $request->agency_id)->first();
            }
        }

        $category = Category::where('uuid', $request->category_id)->first();
        $state = Location::where('uuid', $request->state_id)->first();
        $city = Location::where('uuid', $request->city_id)->first();
        $repost_job = Job::where('uuid', $request->repost_job_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'state_id' => $state->id ?? null,
            'city_id' => $city->id ?? null,
            'status' => 'draft',
            'industry_experience' => '' . implode(',', array_slice($request->industry_experience ?? [], 0, 10)) . '',
            'media_experience' => '' . implode(',', array_slice($request->media_experience ?? [], 0, 10)) . '',
            'strengths' => '' . implode(',', array_slice($request->strengths ?? [], 0, 5)) . '',
            'repost_job_id' => $repost_job?->id,
        ]);

        try {
            $job = Job::create($request->all());

            if ($request->attachment_id) {
                $attachment = Attachment::where("uuid", $request->attachment_id)->first();
                if (!empty($attachment)) {
                    $attachment->update([
                        'resource_id' => $job->id,
                    ]);
                    $job->update([
                        'attachment_id' => $attachment->id,
                    ]);
                }
            }

            create_notification($user->id, 'Job submitted successfully.');
            if ($request->has('agency_id')) { // Sending notification to advisor user also
                create_notification($advisor->id, 'Job submitted successfully.');
            }

            return ApiResponse::success(new JobResource($job), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('JS-01' . $e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $job = Job::with('user.agency', 'attachment')->where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        // return $job;
        return new JobResource($job);
    }

    public function updateFromAdmin(UpdateJobRequest $request, $uuid)
    {
        try {
            $job = Job::where('uuid', $uuid)->firstOrFail();

            $category = Category::where('uuid', $request->category_id)->first();
            $request->merge([
                'category_id' => $category->id,
                'industry_experience' => '' . implode(',', $request->industry_experience) . '',
                'media_experience' => '' . implode(',', $request->media_experience) . '',
            ]);

            $job->update($request->all());

            return new JobResource($job);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            $job = Job::where('uuid', $uuid)->firstOrFail();

            $oldStatus = $job->status;
            $newStatus = $request->input('status');

            if ($newStatus === 'pending' && $oldStatus === 'draft') {
                $user = Auth::user();

                if (!$user) {
                    return ApiResponse::error(trans('response.unauthorized'), 401);
                }

                $subscription = Subscription::where('user_id', $user->id)
                    ->where('quota_left', '>', 0)
                    ->latest();

                if (!$subscription) {
                    return ApiResponse::error("You don't have enough quota for this job", 402);
                }

                $subscription->decrement('quota_left', 1);

                //Also decrement quota for agency for whom this job as created
                if ($job->advisor_id) {
                    $agency_user = User::find($job->user_id);

                    $subscription = Subscription::where('user_id', $agency_user->id)
                        ->where('quota_left', '>', 0)
                        ->latest();

                    if (!$subscription) {
                        return ApiResponse::error("You don't have enough quota for this job", 402);
                    }

                    $subscription->decrement('quota_left', 1);
                }


                $request->merge([
                    'status' => 'approved',
                ]);
            }

            if ($request->has('category_id')) {
                $category = Category::where('uuid', $request->category_id)->first();
                $request->merge([
                    'category_id' => $category->id ?? null,
                ]);
            }

            if ($request->has('state_id')) {
                $state = Location::where('uuid', $request->state_id)->first();
                $request->merge([
                    'state_id' => $state->id ?? null,
                ]);
            }

            if ($request->has('city_id')) {
                $city = Location::where('uuid', $request->city_id)->first();
                $request->merge([
                    'city_id' => $city->id ?? null,
                ]);
            }

            if ($request->has('industry_experience')) {
                $request->merge([
                    'industry_experience' => implode(',', array_slice($request->industry_experience ?? [], 0, 10)),
                ]);
            }

            if ($request->has('media_experience')) {
                $request->merge([
                    'media_experience' => implode(',', array_slice($request->media_experience ?? [], 0, 10)),
                ]);
            }

            if ($request->has('strengths')) {
                $request->merge([
                    'strengths' => implode(',', array_slice($request->strengths ?? [], 0, 10)),
                ]);
            }

            if ($request->has('attachment_id')) {
                $attachment = Attachment::where("uuid", $request->attachment_id)->first();
                if (!empty($attachment)) {
                    $attachment->update([
                        'resource_id' => $job->id,
                    ]);
                    $request->merge([
                        'attachment_id' => $attachment->id,
                    ]);
                }
            }

            $job->update($request->all());

            return new JobResource($job);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $job = Job::where('uuid', $uuid)->firstOrFail();
            $job->delete();

            return ApiResponse::success(new JobResource($job), 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function processExperience(Request $request, &$filters, $experienceKey)
    {
        if (isset($filters['filter'][$experienceKey])) {
            $experience_ids = $filters['filter'][$experienceKey];
            unset($filters['filter'][$experienceKey]);
            $request->replace($filters);

            if ($experience_ids) {
                $experience_ids = explode(',', $experience_ids);
            } else {
                $experience_ids = [];
            }

            return Industry::whereIn('uuid', $experience_ids)->pluck('id');
        }

        return null;
    }

    public function get_employment_types()
    {
        $cacheKey = 'employment_types';
        $users = Cache::remember($cacheKey, now()->addMinutes(120), function () {
            return Job::EMPLOYMENT_TYPE;
        });

        return $users;
    }

    public function featured_cities()
    {
        return Cache::remember('featured_cities', now()->addMinutes(120), function () {
            $locations = Location::whereIn('slug', ['dallas', 'new-york', 'los-angeles', 'miami', 'chicago'])->get();

            $locationData = [];
            foreach ($locations as $location) {
                $jobs = Job::where('city_id', $location->id)->where('status', 1)->get(); //Approved jobs only
                $locationData[] = [
                    'name' => $location->name,
                    'uuid' => $location->uuid,
                    'count' => $jobs->count(),
                ];
            }

            return $locationData;
        });
    }
}