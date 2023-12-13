<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MentorTopicController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.topic.index');
    }


    public function create(Request $request)
    {
        return view('pages.topic.create');
    }
}