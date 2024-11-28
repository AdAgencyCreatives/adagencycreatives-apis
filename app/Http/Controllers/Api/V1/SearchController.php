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

        $categories = Category::orderBy('name')->select('name')->get()->toArray();
        if ($categories && count($categories) > 0) {
            $search_items['categories'] = $categories;
        }

        $states = Location::whereNull('parent_id')->orderBy('name')->select('name')->get()->toArray();
        if ($states && count($states) > 0) {
            $search_items['states'] = $states;
        }

        $cities = Location::whereNotNull('parent_id')->orderBy('name')->select('name')->get()->toArray();
        if ($cities && count($cities) > 0) {
            $search_items['cities'] = $cities;
        }

        $employment_types = EmploymentTypes::orderBy('name')->select('name')->get()->toArray();
        if ($employment_types && count($employment_types) > 0) {
            $search_items['employment_types'] = $employment_types;
        }

        $years_of_experience = YearsOfExperience::orderBy('name')->select('name')->get()->toArray();
        if ($years_of_experience && count($years_of_experience) > 0) {
            $search_items['years_of_experience'] = $years_of_experience;
        }

        $media_experiences = Media::orderBy('name')->select('name')->get()->toArray();
        if ($media_experiences && count($media_experiences) > 0) {
            $search_items['media_experiences'] = $media_experiences;
        }

        $strengths = Strength::orderBy('name')->select('name')->get()->toArray();
        if ($strengths && count($strengths) > 0) {
            $search_items['strengths'] = $strengths;
        }

        $industry_experiences = Industry::orderBy('name')->select('name')->get()->toArray();
        if ($industry_experiences && count($industry_experiences) > 0) {
            $search_items['industry_experiences'] = $industry_experiences;
        }

        return $search_items;
    }
}
