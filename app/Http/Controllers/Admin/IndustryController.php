<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class IndustryController extends Controller
{
    public function index()
    {
        return view('pages.industries.index');
    }

    public function create()
    {
        return view('pages.industries.add');
    }
}
