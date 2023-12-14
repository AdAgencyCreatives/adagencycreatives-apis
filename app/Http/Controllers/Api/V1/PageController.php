<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PageController extends Controller
{
    public function index(Request $request)
    {
        return QueryBuilder::for(Page::class)
            ->allowedFilters([
                'page',
            ])->get();

    }
}