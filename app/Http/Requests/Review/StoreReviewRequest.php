<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'target_id' => 'required|exists:users,uuid',
            'comment' => 'required|string',
            'rating' => 'required|integer'
        ];
    }
}