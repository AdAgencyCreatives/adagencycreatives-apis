<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    public function index()
    {
        return view('pages.pages.home');
    }

    public function create(Request $request)
    {
        $page = $request->page;
        $data = Page::wherePage($page)->get();

        return view('pages.pages.home', compact('page', 'data'));
    }

    public function store(Request $request)
    {

        $pageData = $request->except('_token');
        $page = $request->page;
        foreach ($pageData as $key => $value) {
            Page::where('page', $page)
                ->where('key', $key)
                ->update(['value' => $value]);
        }

        Session::flash('success', 'SEO updated successfully');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        //
    }
}
