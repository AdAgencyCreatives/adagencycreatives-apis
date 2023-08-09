<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, $status = 200)
    {
        return new JsonResponse([
            'data' => $data,
        ], $status);
    }

    public static function error($message = 'Error', $status = 500)
    {
        return new JsonResponse([
            'message' => $message,
        ], $status);
    }
}
