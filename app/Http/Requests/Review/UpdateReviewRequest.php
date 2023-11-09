<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'target_id' => 'required|exists:users,uuid',
            'comment' => 'required|string',
            'rating' => 'required|integer',
        ];
    }
}