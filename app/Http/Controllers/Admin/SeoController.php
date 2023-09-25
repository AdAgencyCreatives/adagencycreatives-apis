<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SeoController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page;
        $seo = Seo::where('page', $page)->first();

        return response()->json([
            'data' => $seo,
        ]);

    }

    public function create()
    {
        $seo = Seo::where('page', 'home')->first();

        return view('pages.seo.index', compact('seo'));
    }

    public function update(Request $request, $id)
    {
        $seo = Seo::find($id);
        $seo->update([
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => implode(',', $request->keywords ? $request->keywords : []),
        ]);
        Session::flash('success', 'SEO updated successfully');

        return redirect()->back();
    }
}
