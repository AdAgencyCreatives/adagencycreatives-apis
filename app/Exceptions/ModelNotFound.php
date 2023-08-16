<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ModelNotFound extends Exception
{
    public function render(Request $request): Response
    {
        return response(['message' => trans('response.not_found')], 404);
    }
}
