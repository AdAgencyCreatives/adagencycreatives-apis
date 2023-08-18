<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard.index', get_defined_vars());
    }
}
