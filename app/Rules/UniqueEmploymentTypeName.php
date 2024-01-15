<?php

namespace App\Rules;

use App\Models\EmploymentTypes;
use Illuminate\Contracts\Validation\Rule;

class UniqueEmploymentTypeName implements Rule
{
    public function passes($attribute, $value)
    {
        return ! EmploymentTypes::where('name', $value)->exists();
    }

    public function message()
    {
        return 'The :attribute already exists.';
    }
}
