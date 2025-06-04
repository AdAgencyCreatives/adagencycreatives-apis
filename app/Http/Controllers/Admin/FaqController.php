<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function index()
    {
        return "Hello";
        // return view('pages.faqs.index');
    }

    public function create()
    {
        return view('pages.faqs.add');
    }
}
