<?php

namespace App\Http\Requests\Award;

use Illuminate\Foundation\Http\FormRequest;

class StoreAwardRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'awards' => 'required|array|min:1',
            'awards.*.award_title' => 'required|string',
            'awards.*.award_year' => 'required|integer',
            'awards.*.award_work' => 'required|string',
        ];
    }
}
