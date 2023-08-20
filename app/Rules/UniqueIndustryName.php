<?php

namespace App\Rules;

use App\Models\Industry;
use Illuminate\Contracts\Validation\Rule;

class UniqueIndustryName implements Rule
{
    public function passes($attribute, $value)
    {
        return ! Industry::where('name', $value)->exists();
    }

    public function message()
    {
        return 'The :attribute already exists.';
    }
}
