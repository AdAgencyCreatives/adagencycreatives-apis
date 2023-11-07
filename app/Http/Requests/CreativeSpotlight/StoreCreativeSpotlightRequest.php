<?php

namespace App\Http\Requests\CreativeSpotlight;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreativeSpotlightRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
             'user_id' => 'required|exists:users,uuid',
        ];
    }
}
