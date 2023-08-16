<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class UnauthorizedException extends Exception
{
    public function render(Request $request)
    {
        $data = [
            'message' => trans('response.no_permission'),
        ];

        return response($data, 401);
    }
}
