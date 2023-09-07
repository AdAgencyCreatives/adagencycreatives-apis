<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ExperienceController extends Controller
{
    public function index()
    {
        return view('pages.experiences.index');
    }

    public function create()
    {

        return view('pages.experiences.add');
    }
}
