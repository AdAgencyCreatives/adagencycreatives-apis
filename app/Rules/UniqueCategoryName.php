<?php

namespace App\Rules;

use App\Models\Category;
use Illuminate\Contracts\Validation\Rule;

class UniqueCategoryName implements Rule
{
    public function passes($attribute, $value)
    {
        return ! Category::where('name', $value)->exists();
    }

    public function message()
    {
        return 'The :attribute already exists.';
    }
}
