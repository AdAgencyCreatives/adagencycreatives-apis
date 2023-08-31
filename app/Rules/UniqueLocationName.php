<?php

namespace App\Rules;

use App\Models\Location;
use Illuminate\Contracts\Validation\Rule;

class UniqueLocationName implements Rule
{
    public function passes($attribute, $value)
    {
        return ! Location::where('name', $value)->exists();
    }

    public function message()
    {
        return 'The :attribute already exists.';
    }
}
