<?php

namespace App\Rules;

use App\Models\YearsOfExperience;
use Illuminate\Contracts\Validation\Rule;

class UniqueYearsOfExperienceName implements Rule
{
    public function passes($attribute, $value)
    {
        return ! YearsOfExperience::where('name', $value)->exists();
    }

    public function message()
    {
        return 'The :attribute already exists.';
    }
}
