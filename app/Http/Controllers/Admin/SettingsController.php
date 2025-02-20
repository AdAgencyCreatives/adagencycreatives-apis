<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'job_title' => settings('job_title'),
            'job_description' => settings('job_description'),

            'creative_title' => settings('creative_title'),
            'creative_description' => settings('creative_description'),

            'agency_title' => settings('agency_title'),
            'agency_description' => settings('agency_description'),

            'creative_spotlight_title' => settings('creative_spotlight_title'),
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

    public function update_creatives_count(Request $request)
    {
        settings($request->only('creative_count_homepage')); //number of creatives to appear on homepage slider
      
        Cache::forget('homepage_creatives');
        Artisan::call('cache:clear');
        Session::flash('success', 'Homepage creatives count updated successfully');
        return redirect()->back();
    }

    public function update_agencies(Request $request)
    {
        settings($request->only('agency_title', 'agency_description'));
        Session::flash('success', 'SEO updated successfully');

        Artisan::call('cache:clear');

        return redirect()->back();
    }

    public function update_creative_spotlight(Request $request)
    {
        settings($request->only('creative_spotlight_title'));
        Session::flash('success', 'SEO updated successfully');

        Artisan::call('cache:clear');

        return redirect()->back();
    }
}