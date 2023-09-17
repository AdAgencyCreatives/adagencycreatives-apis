<?php

namespace App\Rules;

use App\Models\Strength;
use Illuminate\Contracts\Validation\Rule;

class UniqueStrengthName implements Rule
{
    public function passes($attribute, $value)
    {
        return ! Strength::where('name', $value)->exists();
    }

    public function message()
    {
        return 'The :attribute already exists.';
    }
}
