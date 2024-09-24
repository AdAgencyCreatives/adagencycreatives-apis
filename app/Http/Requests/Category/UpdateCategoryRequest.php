<?php

namespace App\Http\Requests\Category;

use App\Rules\UniqueCategoryName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
 {
    public function authorize()
 {
        return true;
    }

    public function rules()
 {
        return [
            'name' => [ 'sometimes', 'max:255' ],
            'group_name' => [ 'sometimes', 'max:255' ],
        ];
    }
}