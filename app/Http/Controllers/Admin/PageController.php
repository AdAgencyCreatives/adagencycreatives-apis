<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function store_img(Request $request)
    {
        try {
            $uuid = Str::uuid();
            $file = $request->upload;
            $folder = 'page/'.$uuid;
            $filePath = Storage::disk('s3')->put($folder, $file);

            return response()->json([
                'url' => getAttachmentBasePath().$filePath,
            ]);

        } catch (\Exception $e) {
            throw new ApiException($e, 'ATS-001');
        }
    }
}
