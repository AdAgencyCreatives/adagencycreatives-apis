<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('pages.reports.index');
    }
}
