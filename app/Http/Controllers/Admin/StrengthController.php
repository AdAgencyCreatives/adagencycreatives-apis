<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class StrengthController extends Controller
{
    public function index()
    {
        return view('pages.strengths.index');
    }

    public function create()
    {
        return view('pages.strengths.add');
    }
}
