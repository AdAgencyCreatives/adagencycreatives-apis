<?php

namespace App\Rules;

use App\Models\Media;
use Illuminate\Contracts\Validation\Rule;

class UniqueMediaName implements Rule
{
    public function passes($attribute, $value)
    {
        return ! Media::where('name', $value)->exists();
    }

    public function message()
    {
        return 'The :attribute already exists.';
    }
}
