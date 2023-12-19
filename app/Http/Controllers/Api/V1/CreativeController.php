<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creative\StoreCreativeRequest;
use App\Http\Requests\Creative\UpdateCreativeRequest;
use App\Http\Resources\Creative\CreativeResource;
use App\Http\Resources\Creative\CreativeSpotlightCollection;
use App\Http\Resources\Creative\HomepageCreativeCollection;
use App\Http\Resources\Creative\LoggedinCreativeCollection;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Creative;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class CreativeController extends Controller
{
    public function search1(Request $request)
    {
        $search = $request->search;
        $terms = explode(',', $search);

        // Search via First or Last Name
        $sql = 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = trim($terms[$i]);
            // Check if the term contains a space or underscore (full name or both names)
            if (strpos($term, ' ') !== false || strpos($term, '_') !== false) {
                $separator = strpos($term, ' ') !== false ? ' ' : '_';
                $names = explode($separator, $term);
                $firstName = trim($names[0]);
                $lastName = trim($names[1]);

                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "CONCAT(ur.first_name, ' ', ur.last_name) LIKE '%$firstName% $lastName%'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '%$lastName% $firstName%'" . "\n";

                // Additional check for reverse order
                $sql .= " OR CONCAT(ur.first_name, ' ', ur.last_name) LIKE '%$lastName% $firstName%'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '%$firstName% $lastName%'" . "\n";
            } else {
                // Search by individual terms
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ur.first_name LIKE '%$term%'" . "\n";
                $sql .= " OR ur.last_name LIKE '%$term%'" . "\n";
            }
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via City Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via State Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $res = DB::select($sql);
        $creativeIds = collect($res)->pluck('id')->toArray();

        $creatives = Creative::whereIn('id', $creativeIds)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->orderByDesc('is_featured')
            ->orderBy('created_at')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }

    public function search2(Request $request)
    {
        $search = $request->search;
        $terms = explode(',', $search);

        // Search via First or Last Name
        $sql = 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = trim($terms[$i]);
            // Check if the term contains a space or underscore (full name or both names)
            if (strpos($term, ' ') !== false || strpos($term, '_') !== false) {
                $separator = strpos($term, ' ') !== false ? ' ' : '_';
                $names = explode($separator, $term);
                $firstName = trim($names[0]);
                $lastName = trim($names[1]);

                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "CONCAT(ur.first_name, ' ', ur.last_name) LIKE '%$firstName% $lastName%'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '%$lastName% $firstName%'" . "\n";

                // Additional check for reverse order
                $sql .= " OR CONCAT(ur.first_name, ' ', ur.last_name) LIKE '%$lastName% $firstName%'" . "\n";
                $sql .= " OR CONCAT(ur.last_name, ' ', ur.first_name) LIKE '%$firstName% $lastName%'" . "\n";
            } else {
                // Search by individual terms
                $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ur.first_name LIKE '%$term%'" . "\n";
                $sql .= " OR ur.last_name LIKE '%$term%'" . "\n";
            }
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via City Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via State Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Industry Title (a.k.a Category)
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN categories ca ON cr.category_id = ca.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ca.name LIKE '%" . trim($term) . "%')" . "\n";
        }

        $res = DB::select($sql);
        $creativeIds = collect($res)->pluck('id')->toArray();

        $creatives = Creative::whereIn('id', $creativeIds)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->orderByDesc('is_featured')
            ->orderBy('created_at')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }


    public function search3(Request $request)
    {
        // Split the search terms into an array
        $searchTerms = explode(',', $request->search);

        // Initialize arrays to store IDs for each match type
        $exactMatchIds = [];
        $startsWithIds = [];
        $containsIds = [];

        // Iterate through each term for exact match
        foreach ($searchTerms as $term) {
            $exactMatchIds[] = $this->getCreativeIDs(trim($term), 'exact-match');
        }

        // Find common IDs across all exact match arrays
        $commonExactMatchIds = call_user_func_array('array_intersect', $exactMatchIds);

// dd($commonExactMatchIds);
        // Initialize arrays for common starts-with and common contains IDs
        $commonStartsWithIds = [];
        $commonContainsIds = [];

        // Initialize the combined array with common exact match IDs
        $combinedCreativeIds = $commonExactMatchIds;

        // Remove common exact match IDs from individual arrays
        foreach ($exactMatchIds as &$ids) {
            $ids = array_values(array_diff($ids, $commonExactMatchIds));
        }

        // Iterate through each term for starts-with match
        foreach ($searchTerms as $term) {
            $startsWithIds[] = $this->getCreativeIDs(trim($term), 'starts-with');
        }

        // Find common IDs across all starts-with arrays
        $commonStartsWithIds = call_user_func_array('array_intersect', $startsWithIds);

        // Prioritize common starts-with match IDs in the combined array
        $combinedCreativeIds = array_merge($combinedCreativeIds, $commonStartsWithIds);

        // Remove common starts-with match IDs from individual arrays
        foreach ($startsWithIds as &$ids) {
            $ids = array_values(array_diff($ids, $commonStartsWithIds));
        }

        // Iterate through each term for contains match
        foreach ($searchTerms as $term) {
            $containsIds[] = $this->getCreativeIDs(trim($term), 'contains');
        }

        // Find common IDs across all contains arrays
        $commonContainsIds = call_user_func_array('array_intersect', $containsIds);

        // Prioritize common contains match IDs in the combined array
        $combinedCreativeIds = array_merge($combinedCreativeIds, $commonContainsIds);

        // Remove common contains match IDs from individual arrays
        foreach ($containsIds as &$ids) {
            $ids = array_values(array_diff($ids, $commonContainsIds));
        }

        $combinedCreativeIds = array_merge($combinedCreativeIds, $exactMatchIds);
        $combinedCreativeIds = array_merge($combinedCreativeIds, $startsWithIds);
        $combinedCreativeIds = array_merge($combinedCreativeIds, $containsIds);
        $combinedCreativeIds = Arr::flatten($combinedCreativeIds);

        // Combine and deduplicate the IDs while preserving the order
        $combinedCreativeIds = array_values(array_unique($combinedCreativeIds, SORT_NUMERIC));

        $rawOrder = 'FIELD(id, ' . implode(',', $combinedCreativeIds) . ')';


        // Retrieve creative records from the database and order them based on the calculated order
        $creatives = Creative::with('category')
            ->whereIn('id', $combinedCreativeIds)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)->where('status', 1);
            })
            ->orderByRaw($rawOrder)
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }


    public function getCreativeIDs($search, $match_type = 'contains') // match_type => contains | starts-with | exact-match
    {
        if(!isset($match_type) || strlen($match_type) == 0) {
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
        $sql = 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id' . "\n";
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
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via City Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NOT NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via State Name
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(lc.parent_id IS NULL AND lc.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Industry Title (a.k.a Category)
        $sql .= 'SELECT cr.id FROM creatives cr INNER JOIN categories ca ON cr.category_id = ca.id' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "(ca.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "')" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Industry Experience
        $sql .= 'SELECT cr.id FROM creatives cr JOIN industries ind ON FIND_IN_SET(ind.uuid, cr.industry_experience) > 0' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "ind.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Media Experience
        $sql .= 'SELECT cr.id FROM creatives cr JOIN medias md ON FIND_IN_SET(md.uuid, cr.media_experience) > 0' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "md.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Strengths
        $sql .= 'SELECT cr.id FROM creatives cr JOIN strengths st ON FIND_IN_SET(st.uuid, cr.strengths) > 0' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "st.name LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";

        // Search via Employment Type (Tye of work e.g. Full-Time)
        $sql .= 'SELECT cr.id FROM creatives cr' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "cr.employment_type LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
        }

        $sql .= 'UNION DISTINCT' . "\n";
        // Search via Years of experience
        $sql .= 'SELECT cr.id FROM creatives cr' . "\n";
        for ($i = 0; $i < count($terms); $i++) {
            $term = $terms[$i];
            $sql .= ($i == 0 ? ' WHERE ' : ' OR ') . "cr.years_of_experience LIKE '" . $wildCardStart . '' . trim($term) . '' . $wildCardEnd . "'" . "\n";
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
                $sql .= ($i == 0 ? 'UNION DISTINCT' . "\n" . 'SELECT cr.id FROM creatives cr WHERE ' . "\n" : ' OR ') . $workplace_preferences[$term] . '=1' . "\n";
            }
        }

        $res = DB::select($sql);
        $creativeIds = collect($res)
            ->pluck('id')
            ->toArray();
        return $creativeIds;
    }

    public function search6(Request $request)
    {
        $creativeIds =  $this->getCreativeIDs($request->search, $request->match_type);

        $creatives = Creative::with('category')
                ->whereIn('id', $creativeIds)
                ->whereHas('user', function ($query) {
                    $query->where('is_visible', 1)
                        ->where('status', 1);
                })
                ->orderByDesc('is_featured')
                ->orderBy('created_at')
                ->paginate($request->per_page ?? config('global.request.pagination_limit'))
                ->withQueryString();

        return new LoggedinCreativeCollection($creatives);

        return new LoggedinCreativeCollection($creatives);
    }

    public function search4(Request $request)
    {
        $term = $request->search;
        $field = $request->field;

        try {
            $sql = '';
            $bindings = '';
            switch ($field) {

                case 'state':
                    // Search via State Name
                    $sql = 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
                    $sql .= " WHERE (lc.parent_id IS NULL AND lc.name ='" . trim($term) . "')";
                    break;

                case 'city':
                    // Search via City Name
                    $sql = 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
                    $sql .= " WHERE(lc.parent_id IS NOT NULL AND lc.name ='" . trim($term) . "')" . "\n";
                    break;

                case 'industry-experience':
                    // Search via Industry Experience
                    $sql = 'SELECT cr.id FROM creatives cr JOIN industries ind ON FIND_IN_SET(ind.uuid, cr.industry_experience) > 0' . "\n";
                    $sql .= " WHERE ind.name ='" . trim($term) . "'" . "\n";
                    break;

                case 'media-experience':
                    // Search via Media Experience
                    $sql = 'SELECT cr.id FROM creatives cr JOIN medias med ON FIND_IN_SET(med.uuid, cr.media_experience) > 0' . "\n";
                    $sql .= " WHERE med.name ='" . trim($term) . "'" . "\n";
                    break;

                case 'strengths':
                    // Search via Character Strengths
                    $sql = 'SELECT cr.id FROM creatives cr JOIN strengths strn ON FIND_IN_SET(strn.uuid, cr.strengths) > 0' . "\n";
                    $sql .= " WHERE strn.name ='" . trim($term) . "'" . "\n";
                    break;

                case 'work-type':
                    // Search via Type of Work e.g Freelance, Contract, Full-Time
                    $sql = 'SELECT cr.id FROM creatives cr' . "\n";
                    $sql .= " WHERE cr.employment_type LIKE '%" . trim($term) . "%'" . "\n";
                    break;

                case 'years-of-experience':
                    // Search via Type of Work e.g Freelance, Contract, Full-Time
                    $sql = 'SELECT cr.id FROM creatives cr' . "\n";
                    $sql .= " WHERE cr.years_of_experience ='" . trim($term) . "'" . "\n";
                    break;

                case 'industry-title':
                    // Search via Category (Industry Title )
                    $sql = 'SELECT cr.id FROM creatives cr INNER JOIN categories ca ON cr.category_id = ca.id' . "\n";
                    $sql .= " WHERE (ca.name ='" . trim($term) . "')" . "\n";
                    break;

                case 'education-college':
                    // Search via Degree Program in Education
                    $sql = 'SELECT cr.id FROM creatives cr ';
                    $sql .= 'INNER JOIN users ur ON cr.user_id = ur.id ';
                    $sql .= 'INNER JOIN educations edu ON ur.id = edu.user_id ';
                    $sql .= "WHERE edu.college LIKE :term" . "\n";
                    $bindings = ['term' => '%' . $term . '%'];
                    break;

                case 'education-degree-program':
                    // Search via Degree Program in Education
                    $sql = 'SELECT cr.id FROM creatives cr ';
                    $sql .= 'INNER JOIN users ur ON cr.user_id = ur.id ';
                    $sql .= 'INNER JOIN educations edu ON ur.id = edu.user_id ';
                    $sql .= "WHERE edu.degree = " . DB::raw('"' . trim($term) . '"') . "\n";
                    break;

                case 'experience-company':
                    // Search via Company name in Experience table
                    $sql = 'SELECT cr.id FROM creatives cr ';
                    $sql .= 'INNER JOIN users ur ON cr.user_id = ur.id ';
                    $sql .= 'INNER JOIN experiences exp ON ur.id = exp.user_id ';
                    $sql .= "WHERE exp.company = " . DB::raw('"' . trim($term) . '"') . "\n";
                    break;
            }
            if($bindings != '') {
                $res = DB::select($sql, $bindings);
            } else {
                $res = DB::select($sql);
            }

            $creativeIds = collect($res)->pluck('id')->toArray();
        } catch(\Exception $e) {
            $creativeIds = [];
        }

        $creatives = Creative::whereIn('id', $creativeIds)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->orderByDesc('is_featured')
            ->orderBy('created_at')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
    }

    public function related_creatives(Request $request) //based on first Title, Second State, Third City
    {
        $user = User::where('uuid', $request->creative_id)->first();
        $creative = Creative::where('user_id', $user->id)->first();
        $category = $creative->category;
        $location = get_location($user);

        $related_category_ids = Creative::where('category_id', $category->id)->pluck('id')->toArray();

        $sql = 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.state_id' . "\n";
        $sql .= " WHERE (lc.parent_id IS NULL AND lc.uuid ='" . $location['state_id'] . "')" . "\n";
        $res = DB::select($sql);
        $related_states_ids = collect($res)->pluck('id')->toArray();

        $sql = 'SELECT cr.id FROM creatives cr INNER JOIN users ur ON cr.user_id = ur.id INNER JOIN addresses ad ON ur.id = ad.user_id INNER JOIN locations lc ON lc.id = ad.city_id' . "\n";
        $sql .= " WHERE (lc.parent_id IS NOT NULL AND lc.uuid = '" . $location['city_id'] . "')" . "\n";
        $res = DB::select($sql);
        $related_city_ids = collect($res)->pluck('id')->toArray();

        $sortedCreatives = $this->sortCreatives($related_category_ids, $related_states_ids, $related_city_ids, $creative->id);

        $rawOrder = 'FIELD(id, ' . implode(',', $sortedCreatives) . ')';

        $creatives = Creative::whereIn('id', $sortedCreatives)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->orderByRaw($rawOrder)
            ->orderByDesc('is_featured')
            ->orderBy('created_at')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new LoggedinCreativeCollection($creatives);
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

        if (isset($filters['filter']['slug'])) {
            $slug = $filters['filter']['slug'];
            $logged_in_user = request()->user();

            $current_creative = Creative::where('user_id', $logged_in_user->id)->first();
            if ($current_creative && $current_creative->slug == $slug) { // Even if the user is not visible, he/she can view his/her own profile
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
                AllowedFilter::exact('slug'),
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
        ])->paginate($request->per_page ?? config('global.request.pagination_limit'))
        ->withQueryString();

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

            if ($request->filled('slug')) {
                $userData['username'] = $request->slug;
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
                updateLink($user, $request->input('portfolio_site'), 'portfolio_website');
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