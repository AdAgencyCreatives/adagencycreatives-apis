<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'job_title' => settings('job_title'),
            'job_description' => settings('job_description'),

            'creative_title' => settings('creative_title'),
            'creative_description' => settings('creative_description'),
        ];

        return view('pages.settings.index', compact('settings'));
    }

    public function create()
    {
        $data = [
            'site_name' => settings('site_name', env('APP_NAME')),
            'site_description' => settings('site_description'),
            'separator' => settings('separator'),
        ];

        return view('pages.settings.create', compact('data'));
    }

    public function store(Request $request)
    {
        settings($request->only('site_name', 'site_description', 'separator'));
        Session::flash('success', 'SEO updated successfully');

        Artisan::call('cache:clear');

        return redirect()->back();
    }

    public function update_job(Request $request)
    {
        settings($request->only('job_title', 'job_description'));
        Session::flash('success', 'SEO updated successfully');

        Artisan::call('cache:clear');

        return redirect()->back();
    }

    public function update_creatives(Request $request)
    {
        settings($request->only('creative_title', 'creative_description'));
        Session::flash('success', 'SEO updated successfully');

        Artisan::call('cache:clear');

        return redirect()->back();
    }
}