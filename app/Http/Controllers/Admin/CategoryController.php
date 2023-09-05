<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        return view('pages.categories.index');
    }
}
