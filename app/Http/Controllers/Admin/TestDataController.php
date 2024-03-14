<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TestDataController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.test_data.index');
    }
}
