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
            'name' => ['required', 'max:255', new UniqueCategoryName],
        ];
    }
}
