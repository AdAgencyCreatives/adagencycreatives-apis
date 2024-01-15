<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class EmploymentController extends Controller
{
    public function index()
    {
        return view('pages.employment_type.index');
    }

    public function create()
    {

        return view('pages.employment_type.add');
    }
}