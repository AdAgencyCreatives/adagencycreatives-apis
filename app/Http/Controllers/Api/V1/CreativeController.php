<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creative\StoreCreativeRequest;
use App\Http\Requests\Creative\UpdateCreativeRequest;
use App\Http\Resources\Creative\CreativeCollection;
use App\Http\Resources\Creative\CreativeResource;
use App\Http\Resources\Creative\HomepageCreativeCollection;
use App\Http\Resources\Creative\LoggedinCreativeCollection;
use App\Http\Resources\User\UserCollection;
use App\Models\Application;
use App\Models\Category;
use App\Models\Creative;
use App\Models\CreativeCache;
use App\Models\JobAlert;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CreativeController extends Controller
{
    public function search1(Request $request) //Agency with No package
    {
        $role = $request?->role ?? 'agency';

        $agency_user_id = $request?->user()?->id;
        $agency_user_applicants = [];
        if (isset($agency_user_id)) {
            $agency_user_applicants = array_unique(Application::whereHas('job', function ($query) use ($agency_user_id) {
                $query->where('user_id', $agency_user_id);
            })->pluck('user_id')->toArray());
        }

        $search = $request->search;

        $exact_search_ids = $this->getSearch1CreativeIDs($search, 'exact-match');
        $contains_search_ids = $this->getSearch1CreativeIDs($search, 'contains');

        $combinedCreativeIds = array_merge($exact_search_ids, $contains_search_ids);
        $combinedCreativeIds = array_values(array_unique($combinedCreativeIds, SORT_NUMERIC));
        $rawOrder = 'FIELD(id, ' . implode(',', $combinedCreativeIds) . ')';

        $creatives = Creative::whereIn('id', $combinedCreativeIds)
            ->whereHas('user', function ($query) use ($agency_user_applicants) {
                $query->where('status', 1)
                    ->where(function ($q) use ($agency_user_applicants) {
                        $q->where('is_visible', 1)
                            ->orWhere(function ($q1) use ($agency_user_applicants) {
                                $q1->where('is_visible', 0)
                                    ->whereIn('user_id', $agency_user_applicants);
                            });
                    });
            })
            ->orderByRaw($rawOrder)
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }

    public function search2(Request $request) //Agency with active package
    {
        $role = $request?->role ?? 'agency';

        if ($role == 'agency') {
            $agency_user_id = $request?->user()?->id;
            $agency_user_applicants = [];
            if (isset($agency_user_id)) {
                $agency_user_applicants = array_unique(Application::whereHas('job', function ($query) use ($agency_user_id) {
                    $query->where('user_id', $agency_user_id);
                })->pluck('user_id')->toArray());
            }
        }

        $searchTerms = explode(',', $request->search);
        $combinedCreativeIds = $this->process_three_terms_search($searchTerms, $role);
        $combinedCreativeIds = Arr::flatten($combinedCreativeIds);
        // Combine and deduplicate the IDs while preserving the order
        $combinedCreativeIds = array_values(array_unique($combinedCreativeIds, SORT_NUMERIC));
        $rawOrder = 'FIELD(id, ' . implode(',', $combinedCreativeIds) . ')';

        if ($role == 'creative') {
            $creatives = Creative::whereIn('id', $combinedCreativeIds)
                ->whereHas('user', function ($query) {
                    $query->where('is_visible', 1)
                        ->where('status', 1);
                })
                ->orderByRaw($rawOrder)
                ->paginate($request->per_page ?? config('global.request.pagination_limit'))
                ->withQueryString();
        } else {
            $creatives = Creative::whereIn('id', $combinedCreativeIds)
                ->whereHas('user', function ($query) use ($agency_user_applicants) {
                    $query->where('status', 1)
                        ->where(function ($q) use ($agency_user_applicants) {
                            $q->where('is_visible', 1)
                                ->orWhere(function ($q1) use ($agency_user_applicants) {
                                    $q1->where('is_visible', 0)
                                        ->whereIn('user_id', $agency_user_applicants);
                                });
                        });
                })
                ->orderByRaw($rawOrder)
                ->paginate($request->per_page ?? config('global.request.pagination_limit'))
                ->withQueryString();
        }

        return new LoggedinCreativeCollection($creatives);
    }

    public function search3(Request $request)
    {
        $role = $request?->role ?? 'agency';

        $agency_user_id = $request?->user()?->id;
        $agency_user_role = $request?->user()?->role;

        $agency_user_applicants = [];

        if (isset($agency_user_id) && isset($agency_user_role) && ($agency_user_role == 'agency' || $agency_user_role == 'advisor')) {
            $agency_user_applicants = array_unique(Application::whereHas('job', function ($query) use ($agency_user_id, $agency_user_role) {
                if ($agency_user_role == 'advisor') {
                    $query->where('advisor_id', $agency_user_id);
                } else {
                    $query->where('user_id', $agency_user_id);
                }
            })->pluck('user_id')->toArray());
        }

        // Split the search terms into an array
        $searchTerms = [];
        if (!empty($request->search)) {
            $searchTerms = explode(',', $request->search);
        }

        if (!empty($request->search_level2)) {
            $searchTerms = array_merge($searchTerms, explode(',', $request->search_level2));
        }

        $combinedCreativeIds = $this->process_single_term_search($searchTerms[0], $role);
        for ($i = 1; $i < count($searchTerms); $i++) {
            $combinedCreativeIds = array_values(array_unique(array_intersect($combinedCreativeIds, $this->process_single_term_search($searchTerms[$i], $role))));
        }
        $combinedCreativeIds = $this->sortCreativeIdsFromCacheTable($combinedCreativeIds);
        $rawOrder = 'FIELD(id, ' . implode(',', $combinedCreativeIds) . ')';

        // Retrieve creative records from the database and order them based on the calculated order
        $creatives = Creative::with('category')
            ->whereIn('id', $combinedCreativeIds)
            ->whereHas('user', function ($query) use ($agency_user_applicants) {
                $query
                    ->where('status', 1)
                    ->where(function ($q) use ($agency_user_applicants) {
                        $q
                            ->where('is_visible', 1)
                            ->orWhere(function ($q1) use ($agency_user_applicants) {
                                $q1->where('is_visible', 0)
                                    ->whereIn('user_id', $agency_user_applicants);
                            });
                    });
            })
            ->orderByRaw($rawOrder)
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }

    public function process_single_term_search($searchTerm, $role)
    {
        $creative_1 = $this->getCreativeIDs(trim($searchTerm), 'exact-match', $role);
        $creative_2 = $this->getCreativeIDs(trim($searchTerm), 'starts-with', $role);
        $creative_3 = $this->getCreativeIDs(trim($searchTerm), 'contains', $role);

        return array_merge($creative_1, $creative_2, $creative_3);
    }


    public function process_three_terms_search($searchTerms, $role)
    {
        // Initialize arrays to store IDs for each match type
        $exactMatchIds = [];
        $containsIds = [];

        // Iterate through each term for exact match
        foreach ($searchTerms as $term) {
            $exactMatchIds[] = $this->getCreativeIDs(trim($term), 'exact-match', $role);
        }

        // Find common IDs across all exact match arrays
        $commonExactMatchIds = call_user_func_array('array_intersect', $exactMatchIds); // Point 1
        // Initialize the combined array with common exact match IDs

        if (isset($exactMatchIds[0]) && isset($exactMatchIds[1])) {
            // Find common IDs across first two arrays
            $commonExactMatchIds = array_merge($commonExactMatchIds, array_intersect($exactMatchIds[0], $exactMatchIds[1])); // Point 2
            // $commonExactMatchIds = array_merge($commonExactMatchIds, $exactMatchIds[0]);
        }

        $combinedCreativeIds = $commonExactMatchIds;

        foreach ($searchTerms as $term) {
            $containsIds[] = $this->getCreativeIDs(trim($term), 'contains', $role);
        }
        if (isset($containsIds[0]) && isset($containsIds[1])) {
            $ids_for_point_3 = array_intersect($containsIds[0], $exactMatchIds[1]); // Point 3 City is exact, Title is with % LIKE %

            $combinedCreativeIds = array_merge($combinedCreativeIds, $ids_for_point_3); // Point 3
            $combinedCreativeIds = array_merge($combinedCreativeIds, $exactMatchIds[0]); // Point 4
        }

        return $combinedCreativeIds;
    }

    public function getCreativeIDs($search, $match_type, $role) // match_type => contains | starts-with | exact-match
    {
        $wildCardStart = '%';
        $wildCardEnd = '%';

        if ($match_type == 'starts-with') {
            $wildCardStart = '';
        } elseif ($match_type == 'exact-match') {
            $wildCardStart = '';
            $wildCardEnd = '';
        }

        $terms = explode(',', $search);

        $sql = '';

        // Search via Industry Title (a.k.a Category)
        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN categories ca ON cr.category_id = ca.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ca.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
            // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ca.name LIKE '" . trim($term) . "')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via First or Last Name
        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = trim($terms[$i]);

            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(CONCAT(ur.first_name, ' ', ur.last_name) LIKE '%" . trim($term) . "%')" . "\n";
            $sql .= " OR (CONCAT(ur.last_name, ' ', ur.first_name) LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via City Name
        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '" . trim($term) . "')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via State Name
        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '" . trim($term) . "')" . "\n";
        }

        if ($role != 'creative') {

            $sql .= 'UNION DISTINCT' . "\n";

            // Search via Industry Experience
            $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr JOIN industries ind ON FIND_IN_SET(ind.uuid, cr.industry_experience) > 0' . "\n";
            for ($i = 0; $i < count($terms); $i++) {
                $term = $terms[$i];
                // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ind.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ind.name LIKE '" . trim($term) . "')" . "\n";
            }

            $sql .= 'UNION DISTINCT' . "\n";

            // Search via Media Experience
            $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr JOIN medias md ON FIND_IN_SET(md.uuid, cr.media_experience) > 0' . "\n";
            for ($i = 0; $i < count($terms); $i++) {
                $term = $terms[$i];
                // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "md.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(md.name LIKE '" . trim($term) . "')" . "\n";
            }

            $sql .= 'UNION DISTINCT' . "\n";

            // Search via Strengths
            $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr JOIN strengths st ON FIND_IN_SET(st.uuid, cr.strengths) > 0' . "\n";
            for ($i = 0; $i < count($terms); $i++) {
                $term = $terms[$i];
                // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "st.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(st.name LIKE '" . trim($term) . "')" . "\n";
            }

            $sql .= 'UNION DISTINCT' . "\n";

            // Search via Employment Type (Tye of work e.g. Full-Time)
            $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr' . "\n";
            for ($i = 0; $i < count($terms); $i++) {
                $term = $terms[$i];
                // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "cr.employment_type LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(cr.employment_type LIKE '" . trim($term) . "')" . "\n";
            }

            $sql .= 'UNION DISTINCT' . "\n";
            // Search via Years of experience
            $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr' . "\n";
            for ($i = 0; $i < count($terms); $i++) {
                $term = $terms[$i];
                // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "cr.years_of_experience LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(cr.years_of_experience LIKE '" . trim($term) . "')" . "\n";
            }

            $workplace_preferences = [
                'featured' => 'is_featured',
                'urgent' => 'is_urgent',
                'remote' => 'is_remote',
                'hybrid' => 'is_hybrid',
                'on site' => 'is_onsite',
                'open to relocation' => 'is_opentorelocation',
            ];

            // Search via Workplace Preference
            for ($i = 0; $i < count($terms); $i++) {
                $term = $terms[$i];
                if (isset($workplace_preferences[$term])) {
                    // $sql .= ($i == 0 ? 'UNION DISTINCT' . "\n" . 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr WHERE ' . "\n" : ' OR ') . $workplace_preferences[$term] . '=1' . "\n";
                    $sql .= ($i == 0 ? 'UNION DISTINCT' . "\n" . 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr WHERE ' . "\n" : ' OR ') . $workplace_preferences[$term] . '=1' . "\n";
                }
            }

            $sql .= 'UNION DISTINCT' . "\n";
            // Search via Years of experience
            $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN educations ed ON cr.user_id=ed.user_id' . "\n";
            for ($i = 0; $i < count($terms); $i++) {
                $term = $terms[$i];
                // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ed.degree LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ed.degree LIKE '" . trim($term) . "')" . "\n";
            }

            $sql .= 'UNION DISTINCT' . "\n";
            // Search via Years of experience
            $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN educations ed ON cr.user_id=ed.user_id' . "\n";
            for ($i = 0; $i < count($terms); $i++) {
                $term = $terms[$i];
                // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ed.college LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ed.college LIKE '" . trim($term) . "')" . "\n";
            }

            $sql .= 'UNION DISTINCT' . "\n";
            // Search via Years of experience
            $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN experiences ex ON cr.user_id=ex.user_id' . "\n";
            for ($i = 0; $i < count($terms); $i++) {
                $term = $terms[$i];
                // $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ex.company LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ex.company LIKE '" . trim($term) . "')" . "\n";
            }
        }

        $sql = 'SELECT T.id FROM (' . $sql . ') T ORDER BY T.featured_at DESC, T.created_at DESC';

        $res = DB::select($sql);
        $creativeIds = collect($res)
            ->pluck('id')
            ->toArray();
        return $creativeIds;
    }

    public function search6(Request $request)
    {
        $role = $request?->role ?? 'agency';

        $creativeIds = $this->getCreativeIDs($request->search, 'exact-match', $role);

        $creatives = Creative::with('category')
            ->whereIn('id', $creativeIds)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }

    public function getSearch4CreativeIds($request)
    {
        $term = $request->search;
        $field = $request->field;

        $creativeIds = [];
        try {
            $sql = '';
            $bindings = '';
            switch ($field) {

                case 'state':
                    // Search via State Name
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
                    $sql .= " WHERE (lc.parent_id IS NULL AND lc.name ='" . trim($term) . "')";
                    break;

                case 'city':
                    // Search via City Name
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
                    $sql .= " WHERE(lc.parent_id IS NOT NULL AND lc.name ='" . trim($term) . "')" . "\n";
                    break;

                case 'industry-experience':
                    // Search via Industry Experience
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr JOIN industries ind ON FIND_IN_SET(ind.uuid, cr.industry_experience) > 0' . "\n";
                    $sql .= " WHERE ind.name ='" . trim($term) . "'" . "\n";
                    break;

                case 'media-experience':
                    // Search via Media Experience
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr JOIN medias med ON FIND_IN_SET(med.uuid, cr.media_experience) > 0' . "\n";
                    $sql .= " WHERE med.name ='" . trim($term) . "'" . "\n";
                    break;

                case 'strengths':
                    // Search via Character Strengths
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr JOIN strengths strn ON FIND_IN_SET(strn.uuid, cr.strengths) > 0' . "\n";
                    $sql .= " WHERE strn.name ='" . trim($term) . "'" . "\n";
                    break;

                case 'work-type':
                    // Search via Type of Work e.g Freelance, Contract, Full-Time
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr' . "\n";
                    $sql .= " WHERE cr.employment_type LIKE '%" . trim($term) . "%'" . "\n";
                    break;

                case 'years-of-experience':
                    // Search via Type of Work e.g Freelance, Contract, Full-Time
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr' . "\n";
                    $sql .= " WHERE cr.years_of_experience ='" . trim($term) . "'" . "\n";
                    break;

                case 'industry-title':
                    // Search via Category (Industry Title )
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN categories ca ON cr.category_id = ca.id' . "\n";
                    $sql .= " WHERE (ca.name ='" . trim($term) . "')" . "\n";
                    break;

                case 'education-college':
                    // Search via Degree Program in Education
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr ';
                    $sql .= 'INNER JOIN users ur ON cr.user_id = ur.id ';
                    $sql .= 'INNER JOIN educations edu ON ur.id = edu.user_id ';
                    $sql .= "WHERE edu.college LIKE :term" . "\n";
                    $bindings = ['term' => '%' . $term . '%'];
                    break;

                case 'education-degree-program':
                    // Search via Degree Program in Education
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr ';
                    $sql .= 'INNER JOIN users ur ON cr.user_id = ur.id ';
                    $sql .= 'INNER JOIN educations edu ON ur.id = edu.user_id ';
                    $sql .= "WHERE edu.degree = " . DB::raw('"' . trim($term) . '"') . "\n";
                    break;

                case 'experience-company':
                    // Search via Company name in Experience table
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr ';
                    $sql .= 'INNER JOIN users ur ON cr.user_id = ur.id ';
                    $sql .= 'INNER JOIN experiences exp ON ur.id = exp.user_id ';
                    $sql .= "WHERE exp.company = " . DB::raw('"' . trim($term) . '"') . "\n";
                    break;
            }

            $sql = 'SELECT T.id FROM (' . $sql . ') T ORDER BY T.featured_at DESC, T.created_at DESC';

            if ($bindings != '') {
                $res = DB::select($sql, $bindings);
            } else {
                $res = DB::select($sql);
            }

            $creativeIds = collect($res)->pluck('id')->toArray();
        } catch (\Exception $e) {
            $creativeIds = [];
        }

        return $creativeIds;
    }
    public function search4(Request $request)
    {
        $role = $request?->role ?? 'agency';

        $combinedCreativeIds = $this->getSearch4CreativeIds($request);

        $searchTermsLevel2 = [];
        $search_level2 = $request->search_level2 ?? "";
        if (isset($search_level2) && strlen($search_level2) > 0) {
            $searchTermsLevel2 = explode(',', $request->search_level2 ?? "");
        }

        $combinedCreativeIdsLevel2 = [];
        if (count($searchTermsLevel2) > 0) {
            if (count($searchTermsLevel2) === 1) {
                $combinedCreativeIdsLevel2 = $this->process_single_term_search($searchTermsLevel2[0], $role);
            } else {
                $combinedCreativeIdsLevel2 = $this->process_three_terms_search($searchTermsLevel2, $role);
            }
            $combinedCreativeIds = array_values(array_unique(array_intersect($combinedCreativeIds, $combinedCreativeIdsLevel2)));
        }

        $creatives = Creative::whereIn('id', $combinedCreativeIds)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }

    public function related_creatives(Request $request) //based on first Title, Second State, Third City
    {
        $related_creative_ids = $this->process_related_creatives($request->creative_id);
        $related_creative_ids = array_values(array_unique($related_creative_ids));

        $rawOrder = 'FIELD(id, ' . implode(',', $related_creative_ids) . ')';

        $creatives = Creative::whereIn('id', $related_creative_ids)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->orderByRaw($rawOrder)
            ->paginate(25)
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }

    function process_related_creatives($creative_id)
    {
        $user = User::where('uuid', $creative_id)->first();
        $creative = Creative::where('user_id', $user->id)->first();

        $category = $creative->category;
        $location = get_location($user);

        $creative_1 = $this->getRelatedCreatives($creative, $user, $category, $location, 'category-location-match');
        $creative_2 = $this->getRelatedCreatives($creative, $user, $category, $location, 'category-most-active');
        $creative_3 = $this->getRelatedCreatives($creative, $user, $category, $location, 'closest-category');
        $creative_4 = $this->getRelatedCreatives($creative, $user, $category, $location, 'closest-category-most-active');

        return array_merge($creative_1, $creative_2, $creative_3, $creative_4);
    }

    function getRelatedCreatives($creative, $user, $category, $location, $key = null)
    {
        $sql = '';
        $creativeIds = [];

        switch ($key) {
            case 'category-location-match':
                // Priority 1: Same category title, location
                if ($category?->id) {
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr 
                            INNER JOIN categories ca ON cr.category_id = ca.id 
                            INNER JOIN users ur ON cr.user_id = ur.id 
                            INNER JOIN addresses ad ON ur.id = ad.user_id 
                            INNER JOIN locations lc ON lc.id = ad.city_id 
                            WHERE ca.id = ' . $category->id . '
                            AND lc.uuid = "' . $location['city_id'] . '"';

                    $sql = 'SELECT T.id FROM (' . $sql . ') T ORDER BY T.featured_at DESC, T.created_at DESC';

                    $res = DB::select($sql);
                    $creativeIds = collect($res)
                        ->pluck('id')
                        ->toArray();
                }
                break;
            case 'category-most-active':
                // Priority 1: Same category title, most active
                if ($category?->id) {
                    $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr 
                             INNER JOIN categories ca ON cr.category_id = ca.id 
                             WHERE ca.id = ' . $category->id;

                    $sql = 'SELECT T.id FROM (' . $sql . ') T ORDER BY T.featured_at DESC, T.created_at DESC';

                    $res = DB::select($sql);
                    $creativeIds = collect($res)
                        ->pluck('id')
                        ->toArray();

                    $creativeIds = $this->sortCreativeIdsFromCacheTable($creativeIds);
                }

                break;
            case 'closest-category':
                // Priority 1: Closest category title, location
                if ($category?->id) {
                    $like_categories = Category::where('name', 'LIKE', '%' . $category->name . '%')->orderBy('name', 'asc');
                    $like_categories = $like_categories->pluck('id')->toArray();
                    if ($like_categories) {
                        $like_categories = implode(', ', $like_categories);
                        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr 
                                 INNER JOIN users ur ON cr.user_id = ur.id 
                                 INNER JOIN addresses ad ON ur.id = ad.user_id 
                                 INNER JOIN locations lc ON lc.id = ad.city_id 
                                 WHERE cr.category_id IN (' . $like_categories . ')
                                 AND lc.uuid = "' . $location['city_id'] . '"';

                        $sql = 'SELECT T.id FROM (' . $sql . ') T ORDER BY T.featured_at DESC, T.created_at DESC';

                        $res = DB::select($sql);
                        $creativeIds = collect($res)
                            ->pluck('id')
                            ->toArray();
                    }
                }

                break;
            case 'closest-category-most-active':
                // Priority 1: Same category title, most active
                if ($category?->id) {
                    $like_categories = Category::where('name', 'LIKE', '%' . $category->name . '%')->orderBy('name', 'asc');
                    $like_categories = $like_categories->pluck('id')->toArray();
                    if ($like_categories) {
                        $like_categories = implode(', ', $like_categories);
                        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr 
                                    WHERE cr.category_id IN (' . $like_categories . ')';

                        $sql = 'SELECT T.id FROM (' . $sql . ') T ORDER BY T.featured_at DESC, T.created_at DESC';

                        $res = DB::select($sql);
                        $creativeIds = collect($res)
                            ->pluck('id')
                            ->toArray();

                        $creativeIds = $this->sortCreativeIdsFromCacheTable($creativeIds);
                    }
                }

                break;
        }

        return $creativeIds;
    }

    public function sortCreatives($idsCategory, $idsState, $idsCity, $currentCreativeId)
    {
        $allMatched = array_intersect($idsCategory, $idsState, $idsCity);
        $twoMatched = array_intersect($idsCategory, $idsState);

        $uniqueElementsArray1 = array_diff($idsCategory, $idsState, $idsCity);
        $uniqueElementsArray2 = array_diff($idsState, $idsCategory, $idsCity);
        $uniqueElementsArray3 = array_diff($idsCity, $idsCategory, $idsState);

        $singleMatched = array_merge($uniqueElementsArray1, $uniqueElementsArray2, $uniqueElementsArray3);


        $result = array_values(array_unique(array_merge($allMatched, $twoMatched, $singleMatched)));


        //exclude current creative id
        $result = array_diff($result, [$currentCreativeId]);

        // dump($idsCategory,$idsState,  $idsCity);
        // dump($allMatched);
        // dump($twoMatched);
        // dump($singleMatched);
        // dump($result);

        return $result;
    }

    public function index(Request $request)
    {
        $filters = $request->all();
        $logged_in_user = Auth::guard('sanctum')->user();

        if (isset($filters['filter']['slug'])) {
            $slug = $filters['filter']['slug'];

            $current_creative = Creative::where('user_id', $logged_in_user->id)->first();
            if ($current_creative && $current_creative->slug == $slug) { // Even if the user is not visible, he/she can view his/her own profile
                unset($filters['filter']['is_visible']);
                $request->replace($filters);
            }
        }

        // if (isset($filters['filter']['not_in_group'])) {
        //     $group = Group::where('uuid', $filters['filter']['not_in_group'])->first();
        //     $filters['filter']['not_in_group'] = $group->id;
        //     $request->replace($filters);
        // }

        // if (isset($filters['filter']['not_invited'])) {
        //     $group = Group::where('uuid', $filters['filter']['not_invited'])->first();
        //     $filters['filter']['not_invited'] = $group->id;
        //     $request->replace($filters);
        // }

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
                AllowedFilter::scope('not_in_group'),
                AllowedFilter::scope('not_invited'),
                'employment_type',
                'title',
                AllowedFilter::exact('slug'),
                'is_featured',
                'is_urgent',
            ])
            ->defaultSort('sort_order', '-featured_at', '-created_at')
            ->allowedSorts('sort_order', 'featured_at', 'created_at');

        // dd($query->toSql());
        $creatives = $query->with([
            'user.profile_picture',
            'user.addresses.state',
            'user.addresses.city',
            'user.personal_phone',
            'category',
        ])->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        if (isset($filters['filter']['slug'])) { //Means user profile is being viewed on creatives page
            if ($creatives->count() === 1) { //Check if the collection count is 1 and update views if true
                $creative = $creatives->first();
                $creative->increment('views');
                $creative->save();
            }
        }

        if ($logged_in_user) {
            return new LoggedinCreativeCollection($creatives);
        }

        return new CreativeCollection($creatives);
    }

    public function homepage_creatives(Request $request)
    {
        $perPage = $request->input('per_page');
        $useCache = is_null($perPage);

        $cacheKey = 'homepage_creatives';
        if ($perPage) {
            $cacheKey .= '_per_page_' . $perPage;
        }

        if ($useCache) {
            $creatives = Cache::remember($cacheKey, 86400, function () use ($request) {
                return $this->getCreatives($request);
            });
        } else {
            $creatives = $this->getCreatives($request);
        }

        return new HomepageCreativeCollection($creatives);
    }

    protected function getCreatives(Request $request)
    {
        $perPage = $request->input('per_page') ?? settings('creative_count_homepage');

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
            ->defaultSort('-featured_at', '-updated_at', "-created_at")
            ->allowedSorts('sort_order', 'featured_at', 'updated_at', 'created_at');

        $creatives = $query->with([
            'user.profile_picture',
            'user.addresses.state',
            'user.addresses.city',
            'user.personal_phone',
            'category',
        ])
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->paginate($perPage)
            ->withQueryString();

        return $creatives;
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

    private function getCreativeProfileProgress($creative)
    {
        $progress = 0;
        $required_fields = 10;
        $completed_fields = 0;

        $completed_fields += (strlen($creative?->title ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->category?->name ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->years_of_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->industry_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->media_experience ?? "") > 0) ? 1 : 0;

        $address = $creative?->user?->addresses ? collect($creative?->user->addresses)->firstWhere('label', 'personal') : null;

        if ($address) {
            $completed_fields += (strlen($address?->state?->name ?? "") > 0) ? 1 : 0;
            $completed_fields += (strlen($address?->city?->name ?? "") > 0) ? 1 : 0;
        }

        $completed_fields += (strlen($creative?->strengths ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->employment_type ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->about ?? "") > 0) ? 1 : 0;

        $progress = intval(100 * $completed_fields / $required_fields);

        return $progress;
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

        $now_is_featured = $creative->is_featured;

        $progress = $this->getCreativeProfileProgress($creative);
        $creative->profile_complete_progress = $progress;
        $creative->profile_completed_at = $progress == 100 ? today() : null;

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

            $customMessages = [
                'email.unique' => 'The email address has already been taken',
                'slug.required' => 'The slug field is required',
                'slug.alpha_dash' => 'The slug may only contain letters, numbers, dashes, and underscores',
                'slug.unique' => 'The slug has already been taken',
            ];

            $request->validate([
                'email' => 'unique:users,email,' . $user->id,
                'slug' => 'required|alpha_dash|unique:users,username,' . $user->id,
            ], $customMessages);


            // Update User
            $userData = [];

            if ($request->filled('first_name')) {
                $userData['first_name'] = $request->first_name;
            }

            if ($request->filled('last_name')) {
                $userData['last_name'] = $request->last_name;
            }

            if ($request->filled('slug') && $user->slug != $request->slug) {
                $userData['username'] = $request->slug;
            }

            if ($request->filled('email')) {
                $userData['email'] = $request->email;
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
            if ($request->input('linkedin_profile')) {
                updateLink($user, $request->input('linkedin_profile'), 'linkedin');
            }
            if ($request->input('portfolio_site')) {
                $portfolio_site = formate_url($request->input('portfolio_site'));
                updateLink($user, $portfolio_site, 'portfolio');
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
            $creative->fresh();

            updateLocation($request, $user, 'personal');

            $category = $creative?->category ?? null;

            if ($category?->id) {
                $cat_ids = array($category->id);
                $group_cat_ids = Category::where('group_name', '=', $category->name)->get()->pluck('id')->toArray();
                $cat_ids = array_values(array_merge($cat_ids, $group_cat_ids));
                foreach ($cat_ids as $cat_id) {
                    $alert = JobAlert::where('user_id', $user->id)->where('category_id', $cat_id)->first();
                    if (!$alert) {
                        JobAlert::create([
                            'uuid' => Str::uuid(),
                            'user_id' => $user->id,
                            'category_id' => $cat_id,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
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

    public function getSearch1CreativeIDs($search, $match_type = 'contains')
    {
        if (!isset($match_type) || strlen($match_type) == 0) {
            $match_type = 'contains';
        }

        $wildCardStart = '%';
        $wildCardEnd = '%';

        if ($match_type == 'starts-with') {
            $wildCardStart = '';
        } elseif ($match_type == 'exact-match') {
            $wildCardStart = '';
            $wildCardEnd = '';
        }


        $terms = explode(',', $search);

        // Search via First or Last Name
        $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = trim($terms[$i]);
            // Check if the term contains a space or underscore (full name or both names)
            if (strpos($term, ' ') !== false || strpos($term, '_') !== false) {
                $separator = strpos($term, ' ') !== false ? ' ' : '_';
                $names = explode($separator, $term);
                $firstName = trim($names[0]);
                $lastName = trim($names[1]);

                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "CONCAT(ur.first_name, ' ', ur.last_name) LIKE '" . $wildCardStart . "$firstName% $lastName" . $wildCardEnd . "'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '" . $wildCardStart . "$lastName% $firstName" . $wildCardEnd . "'" . "\n";

                // Additional check for reverse order
                $sql .= " OR CONCAT(ur.first_name, ' ', ur.last_name) LIKE '" . $wildCardStart . "$lastName% $firstName" . $wildCardEnd . "'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '" . $wildCardStart . "$firstName% $lastName" . $wildCardEnd . "'" . "\n";
            } else {
                // Search by individual terms
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ur.first_name LIKE '" . $wildCardStart . "$term" . $wildCardEnd . "'" . "\n";
                $sql .= " OR ur.last_name LIKE '" . $wildCardStart . "$term" . $wildCardEnd . "'" . "\n";
            }

            break; //Because we only allow single term search
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via City Name
        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
            break; //Because we only allow single term search
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via State Name
        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
            if ($i == 0) {
                break; //Because we only allow single term search
            }
        }

        $sql = 'SELECT T.id FROM (' . $sql . ') T ORDER BY T.featured_at DESC, T.created_at DESC';
        $res = DB::select($sql);
        $creativeIds = collect($res)
            ->pluck('id')
            ->toArray();
        return $creativeIds;
    }

    public function getSearch2CreativeIDs($search, $match_type = 'contains')
    {
        if (!isset($match_type) || strlen($match_type) == 0) {
            $match_type = 'contains';
        }

        $wildCardStart = '%';
        $wildCardEnd = '%';

        if ($match_type == 'starts-with') {
            $wildCardStart = '';
        } elseif ($match_type == 'exact-match') {
            $wildCardStart = '';
            $wildCardEnd = '';
        }


        $terms = explode(',', $search);

        $iterationCount = 0;
        // Search via First or Last Name
        $sql = 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = trim($terms[$i]);
            // Check if the term contains a space or underscore (full name or both names)
            if (strpos($term, ' ') !== false || strpos($term, '_') !== false) {
                $separator = strpos($term, ' ') !== false ? ' ' : '_';
                $names = explode($separator, $term);
                $firstName = trim($names[0]);
                $lastName = trim($names[1]);

                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "CONCAT(ur.first_name, ' ', ur.last_name) LIKE '" . $wildCardStart . "$firstName% $lastName" . $wildCardEnd . "'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '" . $wildCardStart . "$lastName% $firstName" . $wildCardEnd . "'" . "\n";

                // Additional check for reverse order
                $sql .= " OR CONCAT(ur.first_name, ' ', ur.last_name) LIKE '" . $wildCardStart . "$lastName% $firstName" . $wildCardEnd . "'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '" . $wildCardStart . "$firstName% $lastName" . $wildCardEnd . "'" . "\n";
            } else {
                // Search by individual terms
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ur.first_name LIKE '" . $wildCardStart . "$term" . $wildCardEnd . "'" . "\n";
                $sql .= " OR ur.last_name LIKE '" . $wildCardStart . "$term" . $wildCardEnd . "'" . "\n";
            }

            $iterationCount++;
            if ($iterationCount >= 2) {
                break; //Because we only allow single term search
            }
        }

        $sql .= 'UNION DISTINCT' . "\n";

        $iterationCount = 0;
        // Search via City Name
        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
        for ($i = 0; $i < min(2, count($terms)); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";

            $iterationCount++;
            if ($iterationCount >= 2) {
                break; //Because we only allow single term search
            }
        }

        $sql .= 'UNION DISTINCT' . "\n";

        $iterationCount = 0;
        // Search via State Name
        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
        for ($i = 0; $i < min(2, count($terms)); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";

            $iterationCount++;
            if ($iterationCount >= 2) {
                break; //Because we only allow single term search
            }
        }

        $sql .= 'UNION DISTINCT' . "\n";

        $iterationCount = 0;
        // Search via Industry Title (a.k.a Category)
        $sql .= 'SELECT cr.id, cr.created_at, cr.featured_at FROM creatives cr INNER JOIN categories ca ON cr.category_id = ca.id' . "\n";
        for ($i = 0; $i < min(2, count($terms)); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ca.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";

            $iterationCount++;
            if ($iterationCount >= 2) {
                break; //Because we only allow single term search
            }
        }

        $sql = 'SELECT T.id FROM (' . $sql . ') T ORDER BY T.featured_at DESC, T.created_at DESC';
        $res = DB::select($sql);
        $creativeIds = collect($res)
            ->pluck('id')
            ->toArray();
        return $creativeIds;
    }

    public function get_tag_creatives(Request $request)
    {
        $name = $request->name;
        $creatives = User::where('role', 4)
            ->whereRaw("CONCAT(first_name,' ',last_name) LIKE '$name%'")
            ->orderByRaw("CONCAT(first_name,' ',last_name)")
            ->take(5)
            ->get();

        return new UserCollection($creatives);
    }

    public function get_system_resume_url()
    {
        $user = request()->user();
        $resume_filename = sprintf('%s_%s_AdAgencyCreatives_%s', $user->first_name, $user->last_name, date('Y-m-d'));
        return route('download.resume', ['name' => $resume_filename, 'u1' => $user->uuid, 'u2' => $user->uuid]);
    }

    public function get_user_preferred_picture(Request $request)
    {
        $slug = $request->has('slug') ? $request->slug : '';
        $preferred_picture = asset('assets/img/placeholder.png');
        if (strlen($slug) > 0) {

            $user = User::where('username', 'LIKE', $slug)->first();

            if ($user) {
                $preferred_picture = get_user_picture_preferred($user);
            }
        }

        return response(file_get_contents($preferred_picture), 200)->header('Content-Type', 'image/jpeg');

        // // Load the GIF
        // $gif = imagecreatefromgif(asset('assets/img/welcome-blank.gif'));

        // // Load the image to embed
        // $image = imagecreatefromjpeg($preferred_picture);

        // // Get dimensions of the GIF and the image
        // $gif_width = imagesx($gif);
        // $gif_height = imagesy($gif);
        // $image_width = imagesx($image);
        // $image_height = imagesy($image);

        // // Define the position to embed the image (centered)
        // $x = ($gif_width - $image_width) / 2;
        // $y = ($gif_height - $image_height) / 2;

        // // Merge the image into the GIF
        // imagecopy($gif, $image, $x, $y, 0, 0, $image_width, $image_height);

        // // Start output buffering 
        // ob_start();

        // // Save the resulting image
        // imagegif($gif);

        // // Get the image content from the buffer 
        // $imageContent = ob_get_clean();

        // // Free up memory
        // imagedestroy($gif);
        // imagedestroy($image);

        // return response($imageContent, 200)->header('Content-Type', 'image/jpeg');
    }

    protected function sortCreativeIdsFromCacheTable(array $creativeIds): array
    {
        if (empty($creativeIds)) {
            return [];
        }

        if (CreativeCache::count() === 0) {
            return $creativeIds;
        }

        $creative_ids_in_cache = CreativeCache::all()->pluck('creative_id')->toArray();
        $creative_ids_not_in_cache = Creative::whereNotIn('id', $creative_ids_in_cache)->pluck('id')->toArray();
        if (count($creative_ids_not_in_cache) > 0) {
            $this->updateCreativeCache($creative_ids_not_in_cache);
        }

        $cachedCreatives = CreativeCache::whereIn('creative_id', $creativeIds)
            ->orderBy(DB::raw('CASE WHEN location IS NULL THEN 1 ELSE 0 END'))
            ->orderBy('category')
            ->orderBy('location')
            ->orderByDesc('activity_rank')
            ->orderBy('created_at')
            ->get();

        $sortedCreativeIds = $cachedCreatives->pluck('creative_id')->toArray();

        return $sortedCreativeIds;
    }

    private function updateCreativeCache($creative_ids)
    {
        $creatives = Creative::with('user', 'category', 'user.addresses', 'user.addresses.state', 'user.addresses.city')
            ->whereNotNull('category_id')
            ->whereIn('id', $creative_ids)
            ->select('id', 'user_id', 'category_id', 'created_at')->latest()->get();

        $max_messages = Message::select(DB::raw('count(*) as message_count'))
            ->groupBy('sender_id')
            ->orderByDesc('message_count')
            ->limit(1)
            ->value('message_count');

        $max_applications = Application::select(DB::raw('count(*) as application_count'))
            ->groupBy('user_id')
            ->orderByDesc('application_count')
            ->limit(1)
            ->value('application_count');

        $max_posts = PostReaction::select(DB::raw('count(*) as post_count'))
            ->groupBy('user_id')
            ->orderByDesc('post_count')
            ->limit(1)
            ->value('post_count');

        $cacheData = [];

        foreach ($creatives as $creative) {

            $category = $creative->category?->name;
            $user = $creative->user;
            $location = get_location_text($user);

            $cacheData[] = [
                'creative_id' => $creative->id,
                'category' => $category,
                'location' => $location,
                'activity_rank' => calculate_activity_score($creative->user_id, $max_messages, $max_applications, $max_posts),
                'created_at' => $creative->created_at,
            ];
        }

        CreativeCache::insert($cacheData);
    }
}