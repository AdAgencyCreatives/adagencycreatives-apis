<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Education;
use App\Models\Location;
use App\Models\EmploymentTypes;
use App\Models\Experience;
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

        $categories = Category::select('name')->distinct()->orderBy('name')->get()->toArray();

        if ($categories && count($categories) > 0) {
            $search_items['categories'] = $categories;
        }

        $states = Location::select('name')->distinct()->whereNull('parent_id')->orderBy('name')->get()->toArray();

        if ($states && count($states) > 0) {
            $search_items['states'] = $states;
        }

        $cities = Location::select('name')->distinct()->whereNotNull('parent_id')->orderBy('name')->get()->toArray();
        if ($cities && count($cities) > 0) {
            $search_items['cities'] = $cities;
        }

        $employment_types = EmploymentTypes::select('name')->distinct()->orderBy('name')->get()->toArray();

        if ($employment_types && count($employment_types) > 0) {
            $search_items['employment_types'] = $employment_types;
        }

        $years_of_experience = YearsOfExperience::select('name')->distinct()->orderBy('name')->get()->toArray();

        if ($years_of_experience && count($years_of_experience) > 0) {
            $search_items['years_of_experience'] = $years_of_experience;
        }

        $media_experiences = Media::select('name')->distinct()->orderBy('name')->get()->toArray();

        if ($media_experiences && count($media_experiences) > 0) {
            $search_items['media_experiences'] = $media_experiences;
        }

        // limited by the client to load only BICOP and Bilingual
        $strengths = Strength::whereIn('name', ["BICOP", "Bilingual"])->select('name')->distinct()->orderBy('name')->get()->toArray();

        if ($strengths && count($strengths) > 0) {
            $search_items['strengths'] = $strengths;
        }

        $industry_experiences = Industry::select('name')->distinct()->orderBy('name')->get()->toArray();

        if ($industry_experiences && count($industry_experiences) > 0) {
            $search_items['industry_experiences'] = $industry_experiences;
        }

        $education_colleges = Education::select('college as name')->distinct()->orderBy('college')->get()->toArray();

        if ($education_colleges && count($education_colleges) > 0) {
            $search_items['education_colleges'] = $education_colleges;
        }

        $education_degrees = Education::select('degree as name')->distinct()->orderBy('degree')->get()->toArray();

        if ($education_degrees && count($education_degrees) > 0) {
            $search_items['education_degrees'] = $education_degrees;
        }

        $experience_companies = Experience::select('company as name')->distinct()->orderBy('company')->get()->toArray();

        if ($experience_companies && count($experience_companies) > 0) {
            $search_items['experience_companies'] = $experience_companies;
        }

        return $search_items;
    }
}
