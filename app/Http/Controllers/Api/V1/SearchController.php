<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Models\EmploymentTypes;
use App\Models\YearsOfExperience;
use App\Models\Media;
use App\Models\Strength;
use App\Models\Industry;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SearchController extends Controller
{
    public function get_search_items(Request $request)
    {
        $search_items = [];

        $categories = Category::orderBy('name')->select(DB::raw('"Category" as type'), 'name')->get()->toArray();
        if ($categories && count($categories) > 0) {
            $search_items = array_merge($search_items, $categories);
        }

        $states = Location::whereNull('parent_id')->orderBy('name')->select(DB::raw('"State" as type'), 'name')->get()->toArray();
        if ($states && count($states) > 0) {
            $search_items = array_merge($search_items, $states);
        }

        $cities = Location::whereNotNull('parent_id')->orderBy('name')->select(DB::raw('"City" as type'), 'name')->get()->toArray();
        if ($cities && count($cities) > 0) {
            $search_items = array_merge($search_items, $cities);
        }

        $employment_types = EmploymentTypes::orderBy('name')->select(DB::raw('"City" as type'), 'name')->get()->toArray();
        if ($employment_types && count($employment_types) > 0) {
            $search_items = array_merge($search_items, $employment_types);
        }

        return $search_items;
    }
}
