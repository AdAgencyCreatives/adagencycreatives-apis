<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResourceUpdateRequest;
use App\Models\Resource;
use Illuminate\Http\Request;

class MentorResourceController extends Controller
{

    public function index(Request $request)
    {
        $resources = Resource::all();

        return view('pages.resource.index', compact('resources'));
    }

    public function create(Request $request)
    {
        return view('pages.resource.create');
    }
}