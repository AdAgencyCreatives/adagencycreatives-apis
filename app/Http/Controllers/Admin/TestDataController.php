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
        $query = Job::where('status', 'approved')->whereDate('expired_at', now()->addDays(3));
        return view('pages.test_data.index', ['data' => $query->get()]);
    }
}
