<?php

namespace App\Http\Requests\Media;

use App\Rules\UniqueMediaName;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:255', new UniqueMediaName],
        ];
    }
}
