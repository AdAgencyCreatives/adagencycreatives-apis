<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class UtilsController extends Controller
{

    public function get_base64(Request $request)
    {
        $user = $request?->user();

        if (!$user) {
            return "un-authorized";
        }

        $url = $request->url;
        return "data:image/jpeg;charset=utf-8;base64," . (strlen($url) > 0 ? base64_encode(file_get_contents($url)) : "");
    }
}